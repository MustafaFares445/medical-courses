<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $oldDisk = 'media'.'_private';

        DB::table('media')
            ->where('disk', $oldDisk)
            ->update(['disk' => 'local']);

        DB::table('media')
            ->where('conversions_disk', $oldDisk)
            ->update(['conversions_disk' => 'local']);
    }

    public function down(): void
    {
        $oldDisk = 'media'.'_private';

        DB::table('media')
            ->where('disk', 'local')
            ->whereIn('collection_name', ['lesson-video', 'book-file'])
            ->update(['disk' => $oldDisk]);

        DB::table('media')
            ->where('conversions_disk', 'local')
            ->whereIn('collection_name', ['lesson-video', 'book-file'])
            ->update(['conversions_disk' => $oldDisk]);
    }
};
