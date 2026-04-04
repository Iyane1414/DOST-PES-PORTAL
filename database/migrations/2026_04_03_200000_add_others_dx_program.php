<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $existingProgramId = DB::table('dx_items')
            ->where('category', 'program')
            ->where('slug', 'others')
            ->value('id');

        if (! $existingProgramId) {
            $existingProgramId = DB::table('dx_items')->insertGetId([
                'category' => 'program',
                'slug' => 'others',
                'parent_id' => null,
                'domain_key' => 'other',
                'code' => null,
                'icon' => null,
                'image_path' => null,
                'file_url' => null,
                'sort_order' => 999,
                'is_active' => true,
                'title' => 'Others',
                'description' => 'Cross-cutting and support projects that sit outside the six main DOST DX sub-program buckets.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($this->othersProjects() as $index => $project) {
            $exists = DB::table('dx_items')
                ->where('category', 'project')
                ->where('slug', $project['slug'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('dx_items')->insert([
                'category' => 'project',
                'slug' => $project['slug'],
                'parent_id' => $existingProgramId,
                'domain_key' => 'other',
                'code' => 'ETC',
                'icon' => null,
                'image_path' => null,
                'file_url' => null,
                'sort_order' => $index + 1,
                'is_active' => true,
                'title' => $project['title'],
                'description' => 'Additional DOST DX support project under the Others sub-program.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $programId = DB::table('dx_items')
            ->where('category', 'program')
            ->where('slug', 'others')
            ->value('id');

        if (! $programId) {
            return;
        }

        DB::table('dx_items')->where('parent_id', $programId)->delete();
        DB::table('dx_items')->where('id', $programId)->delete();
    }

    private function othersProjects(): array
    {
        return [
            ['slug' => 'dost-lms-development', 'title' => 'DOST LMS Development'],
            ['slug' => 'mbt', 'title' => 'MBT'],
            ['slug' => 'cbt', 'title' => 'CBT'],
            ['slug' => 'wbt', 'title' => 'WBT'],
            ['slug' => 'internal-users', 'title' => 'Internal Users'],
            ['slug' => 'external-users', 'title' => 'External Users'],
            ['slug' => 'supply-chain-mobile-app', 'title' => 'Supply Chain Mobile App'],
            ['slug' => 'openscience', 'title' => 'OpenScience'],
            ['slug' => 'ias-system', 'title' => 'IAS System'],
            ['slug' => 'web-content', 'title' => 'Web Content'],
            ['slug' => 'geo-spatial-platform-enhancement', 'title' => 'Geo-Spatial Platform Enhancement'],
            ['slug' => 'foresight-institutionalization', 'title' => 'Foresight Institutionalization'],
        ];
    }
};
