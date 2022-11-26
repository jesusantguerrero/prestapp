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
            $table->foreignId('contact_id');
            $table->foreignId('account_id')->nullable();

            $table->enum('frequency', ['WEEKLY', 'BIWEEKLY', 'MONTHLY']);
            // Basic
            $table->decimal('amount', 11, 2)->default(0.00);
            $table->decimal('interest_rate', 11, 2)->default(0.00);
            $table->decimal('total', 11, 2)->default(0.00);
            
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
