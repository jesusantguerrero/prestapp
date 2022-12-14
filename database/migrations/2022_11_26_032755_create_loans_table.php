<?php

use App\Domains\Loans\Models\Loan;
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
            $table->foreignId('fees_account_id')->nullable();
            $table->foreignId('late_fee_account_id')->nullable();

            // terms
            $table->date('first_installment_date');
            $table->integer('repayment_count');
            $table->enum('frequency', ['WEEKLY', 'BIWEEKLY', 'MONTHLY']);

            $table->decimal('amount', 11, 2)->default(0.00);
            $table->decimal('amount_paid', 11, 2)->default(0.00);
            $table->decimal('amount_due', 11, 2)->default(0.00);
            $table->decimal('total', 11, 2)->default(0.00);
            $table->decimal('interest_rate', 11, 2)->default(0.00);
            // Penalty config
            $table->decimal('late_fee', 11, 2)->default(0.00);
            $table->enum('late_fee_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');
            // state
            $table->enum('payment_status', [
                Loan::STATUS_DRAFT,
                Loan::STATUS_APPROVED,
                Loan::STATUS_DISPOSED,
                Loan::STATUS_PENDING,
                Loan::STATUS_PARTIALLY_PAID,
                Loan::STATUS_GRACE,
                Loan::STATUS_LATE,
                Loan::STATUS_PAID,
                Loan::STATUS_CANCELLED
            ])->default(Loan::STATUS_DRAFT);
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
