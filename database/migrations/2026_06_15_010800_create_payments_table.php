<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider', 50)->default('stripe');
            $table->string('provider_payment_id')->nullable();
            $table->string('provider_session_id')->nullable();
            $table->string('provider_event_id')->nullable()->unique();
            $table->string('status', 50)->default('pending');
            $table->decimal('amount', 10, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->json('raw_payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('provider_session_id');
            $table->index('provider_payment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
