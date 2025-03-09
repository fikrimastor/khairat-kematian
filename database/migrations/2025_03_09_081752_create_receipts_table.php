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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Payment::class)->constrained()->onDelete('cascade');
            $table->string('receipt_number', 50)->unique();
            $table->string('receipt_path', 255)->nullable()->comment('Path to the stored receipt file');
            $table->timestamp('generated_at');
            $table->timestamps();

            // Index for quick lookups
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
