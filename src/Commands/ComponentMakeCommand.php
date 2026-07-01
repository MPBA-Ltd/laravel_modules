<?php

namespace mpba\Modules\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use mpba\Modules\Support\Config\GenerateConfigReader;
use mpba\Modules\Support\Stub;
use mpba\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ComponentMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected Filesystem $files;

    protected $name = 'module:make-component';

    protected $description = 'Generate a component for the specified module.';

    protected $argumentName = 'module';

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Get the default namespace for the component
     */
    public function getDefaultNamespace(): string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.component.namespace') ?: $module->config('paths.generator.component.path', 'Component');
    }

    /**
     * Recover the arguments
     *
     * @return array[]
     */
    protected function getArguments(): array
    {
        return [
            ['component', InputArgument::REQUIRED, 'The name of the component class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Recover the options
     *
     * @return array[]
     */
    protected function getOptions(): array
    {
        return [
            ['component', null, InputOption::VALUE_OPTIONAL, 'The name of the component', null],
        ];
    }

    /**
     * returns the proper case and separated name
     */
    public function to_snake_case(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public function handle(): int
    {
        parent::handle();
        $this->createViewFile();
        $this->registerComponentNamespaceInServiceProvider();

        return true;
    }

    /**
     * Recover the stub and provide the required replacements
     */
    protected function getTemplateContents(): string
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'MODULE' => $this->getModuleName(),
            'NAMESPACE' => $module->getStudlyName(),
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getControllerNameWithoutNamespace(),
            'VIEW_NAME' => $this->to_snake_case($this->getControllerNameWithoutNamespace()),
        ]))->render();
    }

    /**
     * Generate the view name as componentName.blade.php
     */
    protected function getComponentView(): string
    {
        $base = Str::kebab($this->argument('component'));

        // Strip "-component" if the user supplied "SomethingComponent"
        return 'components/'.Str::of($base)->replace('-component', '').'.blade.php';
    }

    /**
     * Creates the required view file
     */
    protected function createViewFile(): void
    {
        $viewPath = module_path($this->getModuleName()).'/Resources/views';
        $viewFile = $viewPath.'/'.$this->getComponentView();

        if (! $this->files->exists(dirname($viewFile))) {
            $this->files->makeDirectory(dirname($viewFile), 0755, true);
        }

        if (! $this->files->exists($viewFile)) {
            $stub = new Stub($this->getStubName(true), [
                'COMPONENT' => $this->argument('component'),
                'MODULE' => $this->getModuleName(),
                'CLASS' => $this->getControllerNameWithoutNamespace(),
            ]);
            $this->files->put($viewFile, $stub->render());
            $this->info("View created: {$viewFile}");
        } else {
            $this->warn("View already exists: {$viewFile}");
        }
    }

    /**
     * Recover the real controller name without the namespace
     */
    private function getControllerNameWithoutNamespace(): array|string
    {
        return class_basename($this->getControllerName());
    }

    /**
     * Return the controller name to be used
     */
    protected function getControllerName(): array|string
    {
        $controller = Str::studly($this->argument('component'));

        if (Str::contains(strtolower($controller), 'component') === false) {
            $controller .= 'Component';
        }

        return $controller;
    }

    /**
     * Get the component name from the provided arguments
     */
    protected function getComponentName(): array|string
    {
        $component = Str::studly($this->argument('component'));
        if (Str::contains(strtolower($component), 'component') === false) {
            $component .= 'Component';
        }

        return $component;
    }

    /**
     * Get the destination path from the entry in modules.config
     */
    protected function getDestinationFilePath(): string
    {

        $path = $this->laravel['modules']->getModulePath($this->getModuleName());
        $helperPath = GenerateConfigReader::read('component');

        return $path.$helperPath->getPath().'/'.$this->getComponentName().'.php';
    }

    /**
     * Set the view name
     */
    protected function getFileName(): string
    {
        return 'view';
    }

    /**
     * Sets the view or controller stub based on a flag
     */
    protected function getStubName($view = false): string
    {
        if ($view) {
            return '/component/view.stub';
        }

        return '/component/controller.stub';
    }

    /**
     * Recover the module name from the provided arguments
     */
    protected function getModuleName(): string
    {
        return $this->argument('module');
    }

    /**
     * Adds the required use statement(s) and also the Blade definition to the
     * modules service provider.
     *
     * @throws FileNotFoundException
     */
    protected function registerComponentNamespaceInServiceProvider(): void
    {
        $moduleName = $this->getModuleName();
        $namespace = "Modules\\$moduleName\\View\\Components";
        $alias = 'components';

        $providerPath = module_path($moduleName)."/Providers/{$moduleName}ServiceProvider.php";

        if (! $this->files->exists($providerPath)) {
            $this->warn("Service provider not found: $providerPath");

            return;
        }

        $contents = $this->files->get($providerPath);
        $registration = "Blade::componentNamespace('{$namespace}', '{$alias}');";
        $import = 'use Illuminate\\Support\\Facades\\Blade;';

        // Insert 'use Blade' after the last existing use statements
        if (! Str::contains($contents, $import)) {
            $contents = preg_replace_callback(
                '/^(use .+?;\n)+/m',
                function ($matches) use ($import) {
                    return $matches[0].$import."\n";
                },
                $contents,
                1
            );
            $this->info('Added Blade import to ServiceProvider.');
        }

        // Add registration if not already present in the service provider
        if (! Str::contains($contents, $registration)) {
            $contents = preg_replace_callback(
                '/public function boot\(\): void\s*\{/',
                fn ($matches) => $matches[0]."\n        {$registration}",
                $contents
            );
            $this->files->put($providerPath, $contents);
            $this->info("Registered Blade component namespace in {$moduleName} ServiceProvider.");
        }

    }
}
