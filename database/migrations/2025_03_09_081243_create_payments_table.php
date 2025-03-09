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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->string('reference_no', 100)->nullable();
            $table->string('status', 50);
            $table->timestamp('payment_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->string('month', 20);
            $table->smallInteger('year');
            $table->tinyInteger('household_count');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for common query patterns
            $table->index(['user_id', 'status']);
            $table->index(['month', 'year']);
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
