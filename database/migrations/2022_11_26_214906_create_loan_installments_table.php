<?php

use App\Domains\Loans\Models\LoanInstallment;
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
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id');

            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('last_paid_at')->nullable();
            $table->integer('number');
            $table->integer('days')->default(0);
            // Basic
            $table->decimal('amount', 14, 4)->default(0.00);
            $table->decimal('amount_due', 14, 4)->default(0.00);
            $table->decimal('amount_paid', 14, 4)->default(0.00);

            $table->decimal('principal', 14, 4)->default(0.00);
            $table->decimal('principal_paid', 14, 4)->default(0.00);

            $table->decimal('interest', 14, 4)->default(0.00);
            $table->decimal('interest_paid', 14, 4)->default(0.00);

            $table->decimal('fees', 14, 4)->default(0.00);
            $table->decimal('fees_paid', 14, 4)->default(0.00);

            $table->decimal('late_fee', 14, 4)->default(0.00);
            $table->decimal('late_fee_paid', 14, 4)->default(0.00);


            $table->decimal('initial_balance', 14, 4)->default(0.00);
            $table->decimal('final_balance', 14, 4)->default(0.00);

            // state
            $table->enum('payment_status', [
                LoanInstallment::STATUS_PENDING,
                LoanInstallment::STATUS_PAID,
                LoanInstallment::STATUS_PARTIALLY_PAID,
                LoanInstallment::STATUS_GRACE,
                LoanInstallment::STATUS_LATE,
            ])->default(LoanInstallment::STATUS_PENDING);

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
        Schema::dropIfExists('loan_installments');
    }
};
