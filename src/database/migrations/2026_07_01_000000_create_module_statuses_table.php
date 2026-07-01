<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('module_statuses')) {
            return;
        }

        Schema::create('module_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('module')->unique();
            $table->boolean('enabled')->default(true);
            $table->text('description')->nullable();
            $table->string('version')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_statuses');
    }
};
