<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('issuances', 'erm_number')) {
            Schema::table('issuances', function (Blueprint $table): void {
                $table->string('erm_number')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('issuances', 'erm_number')) {
            Schema::table('issuances', function (Blueprint $table): void {
                $table->dropColumn('erm_number');
            });
        }
    }
};
