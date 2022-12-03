<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            // accounts
            $table->foreignId('account_id')->nullable();
            $table->foreignId('client_account_id')->nullable();
            $table->foreignId('interest_account_id')->nullable();
            $table->foreignId('fees_account_id')->nullable();
            $table->foreignId('penalty_account_id')->nullable();
            // terms
            $table->decimal('amount', 11, 2)->default(0.00);
            $table->decimal('total', 11, 2)->default(0.00);
            $table->enum('frequency', ['WEEKLY', 'BIWEEKLY', 'MONTHLY']);
            $table->decimal('interest_rate', 11, 2)->default(0.00);
            // Penalty config
            $table->decimal('penalty', 11, 2)->default(0.00);
            $table->enum('penalty_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');
            // state
            $table->enum('payment_status', ['draft','unpaid','partial','overdue', 'paid', 'canceled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
