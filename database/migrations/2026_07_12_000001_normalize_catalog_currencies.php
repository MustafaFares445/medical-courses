<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $currency = strtoupper((string) config('services.stripe.currency', 'usd'));

        if (Schema::hasTable('courses')) {
            DB::table('courses')
                ->where('currency', '!=', $currency)
                ->update(['currency' => $currency]);
        }

        if (Schema::hasTable('books')) {
            DB::table('books')
                ->where('currency', '!=', $currency)
                ->update(['currency' => $currency]);
        }
    }

    public function down(): void
    {
        // Previous per-record currencies cannot be restored safely.
    }
};
