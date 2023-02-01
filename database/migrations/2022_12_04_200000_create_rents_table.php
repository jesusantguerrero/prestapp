<?php

use App\Domains\Properties\Models\Rent;
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
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->foreignId('client_id');
            $table->foreignId('owner_id');
            $table->foreignId('property_id');
            $table->foreignId('unit_id');
            // accounts
            $table->foreignId('account_id')->nullable();
            $table->foreignId('client_account_id')->nullable();
            $table->foreignId('commission_account_id')->nullable();
            $table->foreignId('late_fee_account_id')->nullable();

            $table->string('client_name')->nullable();
            $table->string('owner_name')->nullable();
            $table->text('address')->nullable();
            // terms
            $table->date('deposit_due');
            $table->decimal('deposit', 14, 4)->default(0.00);

            $table->date('date');
            $table->date('end_date')->nullable();
            $table->date('first_invoice_date');
            $table->date('next_invoice_date')->nullable();

            $table->decimal('amount', 14, 4)->default(0.00);
            $table->decimal('amount_paid', 14, 4)->default(0.00);
            $table->decimal('amount_due', 14, 4)->default(0.00);
            $table->decimal('total', 14, 4)->default(0.00);

            // Penalty config
            $table->decimal('commission', 14, 4)->default(0.00);
            $table->enum('commission_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');

            $table->decimal('late_fee', 14, 4)->default(0.00);
            $table->enum('late_fee_type', ['PERCENTAGE', 'PERCENTAGE_OUTSTANDING', 'FIXED'])->default('PERCENTAGE');
            $table->integer('grace_days')->default(0);

            // config
            $table->json('generated_invoice_dates')->default('[]');
            $table->json('additional_fees')->default('[]');

            $table->text('notes')->nullable();
            $table->date('move_out_at')->nullable();
            $table->text('move_out_notice')->nullable();
            // state
            $table->enum('status', [
                Rent::STATUS_ACTIVE,
                Rent::STATUS_PARTIALLY_PAID,
                Rent::STATUS_PAID,
                Rent::STATUS_GRACE,
                Rent::STATUS_LATE,
                Rent::STATUS_CANCELLED
            ])->default(Rent::STATUS_ACTIVE);
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
        Schema::dropIfExists('rents');
    }
};
