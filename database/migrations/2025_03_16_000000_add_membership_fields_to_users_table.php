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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('remember_token');
            $table->date('membership_expires_at')->nullable()->after('is_active');
            $table->string('membership_type')->nullable()->after('membership_expires_at');
            $table->date('last_payment_date')->nullable()->after('membership_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('membership_expires_at');
            $table->dropColumn('membership_type');
            $table->dropColumn('last_payment_date');
        });
    }
};
