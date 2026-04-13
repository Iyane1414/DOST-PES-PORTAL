<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('dx_items')
            ->where('category', 'project')
            ->update(['code' => null]);
    }

    public function down(): void
    {
        //
    }
};
