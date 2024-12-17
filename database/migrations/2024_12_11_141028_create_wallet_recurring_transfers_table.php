<?php

declare(strict_types=1);

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
        Schema::create('wallet_recurring_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('wallets');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('frequency');
            $table->string('recipient_email');
            $table->integer('amount');
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_recurring_transfers');
    }
};
