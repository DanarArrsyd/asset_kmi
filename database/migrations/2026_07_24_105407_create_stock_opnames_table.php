<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('condition');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['asset_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
