<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_number', 50)->unique();
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'expired'])->default('pending');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->string('checkout_session_id')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
