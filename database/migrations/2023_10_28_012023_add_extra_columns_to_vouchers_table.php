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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('voucher_series')->nullable()->after('id');
            $table->string('voucher_number')->nullable()->after('voucher_series');
            $table->string('voucher_type')->nullable()->after('voucher_number');
            $table->string('currency')->nullable()->after('voucher_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['voucher_series', 'voucher_number', 'voucher_type', 'currency']);
        });
    }
};
