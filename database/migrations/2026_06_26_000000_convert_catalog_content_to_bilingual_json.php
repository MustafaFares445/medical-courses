<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, list<string>> */
    private array $columns = [
        'categories' => ['name', 'description'],
        'courses' => ['title', 'short_description', 'description'],
        'course_sections' => ['title', 'description'],
        'lessons' => ['title', 'summary', 'content'],
        'books' => ['title', 'short_description', 'description'],
        'articles' => ['title', 'excerpt', 'body'],
    ];

    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column) {
                $this->wrapExistingText($table, $column);
                DB::statement("ALTER TABLE {$table} MODIFY {$column} JSON NULL");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement("ALTER TABLE {$table} MODIFY {$column} LONGTEXT NULL");
            }
        }
    }

    private function wrapExistingText(string $table, string $column): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->orderBy('id')
            ->select(['id', $column])
            ->chunkById(100, function ($rows) use ($table, $column): void {
                foreach ($rows as $row) {
                    $value = $row->{$column};
                    if (is_string($value) && json_decode($value, true) === null) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => json_encode(['en' => $value, 'ar' => null], JSON_UNESCAPED_UNICODE)]);
                    }
                }
            });
    }
};
