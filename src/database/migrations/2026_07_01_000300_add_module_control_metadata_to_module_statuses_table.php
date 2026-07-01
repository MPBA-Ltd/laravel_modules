<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_statuses', function (Blueprint $table): void {
            if (! Schema::hasColumn('module_statuses', 'icon')) {
                $table->string('icon')->nullable()->after('version');
            }

            if (! Schema::hasColumn('module_statuses', 'category')) {
                $table->string('category')->nullable()->after('icon');
            }

            if (! Schema::hasColumn('module_statuses', 'vendor_type')) {
                $table->string('vendor_type')->nullable()->after('category');
            }

            if (! Schema::hasColumn('module_statuses', 'author')) {
                $table->string('author')->nullable()->after('vendor_type');
            }

            if (! Schema::hasColumn('module_statuses', 'notes')) {
                $table->text('notes')->nullable()->after('author');
            }
        });
    }

    public function down(): void
    {
        Schema::table('module_statuses', function (Blueprint $table): void {
            foreach (['notes', 'author', 'vendor_type', 'category', 'icon'] as $column) {
                if (Schema::hasColumn('module_statuses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
