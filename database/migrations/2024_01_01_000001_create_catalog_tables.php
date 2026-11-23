<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Katalog tabloları: kategoriler, yazarlar, yayınevleri, kitaplar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 10)->nullable();          // emoji ikon (ör: 📖)
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publisher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('isbn', 20)->nullable();
            $table->decimal('price', 10, 2);                 // satış fiyatı
            $table->decimal('list_price', 10, 2)->nullable(); // liste fiyatı (indirim için)
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('page_count')->nullable();
            $table->unsignedSmallInteger('publish_year')->nullable();
            $table->string('language', 30)->default('Türkçe');
            $table->string('cover_type', 30)->default('Karton Kapak');
            $table->string('paper_type', 30)->nullable();
            $table->string('dimensions', 30)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_color', 7)->default('#5b6c8f');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sales_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
    }
};
