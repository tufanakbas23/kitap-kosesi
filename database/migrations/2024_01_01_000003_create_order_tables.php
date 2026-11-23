<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sipariş tarafı: siparişler, sipariş kalemleri, iade/değişim talepleri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('beklemede');

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('coupon_code', 30)->nullable();

            // Teslimat adresi (sipariş anındaki kopya)
            $table->string('shipping_name');
            $table->string('shipping_phone', 20);
            $table->string('shipping_city', 50);
            $table->string('shipping_district', 50);
            $table->text('shipping_address');

            // Ödeme (kart numarasının tamamı ASLA saklanmaz)
            $table->string('payment_method', 20)->default('kredi_karti');
            $table->string('card_holder')->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->unsignedTinyInteger('installment')->default(1);

            $table->string('cargo_company', 50)->nullable();
            $table->string('tracking_number', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('author_name')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['iade', 'degisim']);
            $table->string('reason', 50);
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status', 20)->default('beklemede');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
