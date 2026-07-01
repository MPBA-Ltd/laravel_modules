<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('module_statuses')) {
            return;
        }

        Schema::table('module_statuses', function (Blueprint $table): void {
            if (! Schema::hasColumn('module_statuses', 'description')) {
                $table->text('description')->nullable()->after('enabled');
            }

            if (! Schema::hasColumn('module_statuses', 'version')) {
                $table->string('version')->nullable()->after('description');
            }

            if (! Schema::hasColumn('module_statuses', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('version');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('module_statuses')) {
            return;
        }

        Schema::table('module_statuses', function (Blueprint $table): void {
            if (Schema::hasColumn('module_statuses', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            if (Schema::hasColumn('module_statuses', 'version')) {
                $table->dropColumn('version');
            }

            if (Schema::hasColumn('module_statuses', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
