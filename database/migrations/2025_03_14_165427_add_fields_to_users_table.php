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
            $table->text('address')->nullable()->after('email');
            $table->string('phone', 20)->nullable()->after('address');
            $table->string('identification_number', 20)->nullable()->after('phone');
            $table->boolean('is_admin')->default(false)->after('identification_number');
            $table->string('language', 10)->default('ms')->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'phone',
                'identification_number',
                'is_admin',
                'language',
            ]);
        });
    }
};
