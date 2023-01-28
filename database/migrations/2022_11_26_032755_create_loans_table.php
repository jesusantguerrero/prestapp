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
            $table->foreignId('category_id')->nullable();
            $table->foreignId('source_account_id')->nullable();
            $table->foreignId('account_id')->nullable();
            $table->foreignId('client_account_id')->nullable();
            $table->foreignId('fees_account_id')->nullable();
            $table->foreignId('late_fee_account_id')->nullable();

            $table->string('name')->nullable();
            $table->text('notes')->nullable();
            // terms
            $table->string('client_name')->nullable();
            $table->string('client_address')->nullable();
            $table->date('date');
            $table->date('first_installment_date');
            $table->date('disbursement_date');
            $table->integer('repayment_count');
            $table->enum('frequency', ['WEEKLY', 'BIWEEKLY', 'MONTHLY']);

            $table->decimal('repayment', 14, 4)->default(0.00);
            $table->decimal('amount', 14, 4)->default(0.00);
            $table->decimal('amount_paid', 14, 4)->default(0.00);
            $table->decimal('amount_due', 14, 4)->default(0.00);
            $table->decimal('total', 14, 4)->default(0.00);
            $table->decimal('interest_rate', 14, 4)->default(0.00);
            // Advanced options
            // Penalty config
            $table->integer('grace_days')->default(0);
            $table->decimal('late_fee', 14, 4)->default(0.00);
            $table->enum('late_fee_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');
            $table->integer('installments_paid')->default(0);

            $table->decimal('closing_fees', 14, 4)->default(0.00);
            $table->enum('closing_fee_type', ['PERCENTAGE', 'FIXED'])->default('FIXED');
            $table->enum('source_type', ['SMALL_BOX', 'BANK', 'UNREGISTERED'])->default('UNREGISTERED');

            $table->string('cancel_type')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->decimal('cancel_at_debt', 14, 4)->default(0.00);
            $table->date('cancelled_at')->nullable();
            $table->json('write_off_amounts')->default('{
              "fee": 0,
              "interest": 0,
              "penalty": 0
            }');

            // state
            $table->date('last_paid_at')->nullable();
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
