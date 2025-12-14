<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('requested_date');
            $table->date('expiry_date')->nullable();
            $table->date('fulfilled_date')->nullable();
            $table->foreignId('fulfilled_by_copy_id')->nullable()->constrained('copies')->onDelete('set null');
            $table->enum('status', ['pending', 'ready', 'fulfilled', 'expired', 'cancelled'])->default('pending');
            $table->integer('position')->default(1); // Queue position
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holds');
    }
};
