<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->string('model')->nullable();
            $table->text('specification')->nullable();
            $table->string('pic')->nullable();
            $table->string('status')->default('active');
            $table->string('condition')->default('good');
            $table->date('purchase_date')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('qr_path')->nullable();
            $table->timestamps();

            $table->index(['status', 'condition']);
            $table->index('department_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
