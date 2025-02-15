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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('booking_trx_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pricing_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('sub_total_amount');
            $table->unsignedInteger('grand_total_amount');
            $table->unsignedInteger('total_tax_amount');
            $table->boolean('is_paid');
            $table->string('payment_type');
            $table->string('proof')->nullable();
            $table->date('started_at');
            $table->date('ended_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
