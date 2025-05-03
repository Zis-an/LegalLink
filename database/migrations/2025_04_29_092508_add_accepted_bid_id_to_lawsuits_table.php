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
        Schema::table('lawsuits', function (Blueprint $table) {
            $table->unsignedBigInteger('accepted_bid_id')->nullable()->after('status');
            $table->foreign('accepted_bid_id')->references('id')->on('bids')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lawsuits', function (Blueprint $table) {
            $table->dropForeign(['accepted_bid_id']);
            $table->dropColumn('accepted_bid_id');
        });
    }
};
