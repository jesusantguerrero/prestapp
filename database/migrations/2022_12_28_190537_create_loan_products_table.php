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
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');

            $table->string('name');
            $table->text('description')->nullable();
            $table->json('interest_rates')->default('[20]');
            $table->enum('frequency', ['WEEKLY', 'BIWEEKLY', 'MONTHLY']);
            $table->decimal('late_fee', 14, 4)->default(0.00);
            $table->enum('late_fee_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');
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
        Schema::dropIfExists('loan_products');
    }
};
