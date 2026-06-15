<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('item_type', ['course', 'book']);
            $table->unsignedBigInteger('item_id');
            $table->string('title_snapshot');
            $table->decimal('price_snapshot', 10, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->timestamps();

            $table->index('order_id');
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
