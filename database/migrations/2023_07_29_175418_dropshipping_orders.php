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
      Schema::create('dropshipping_orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id');
        $table->foreignId('user_id');
        $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();

        // accounts
        $table->foreignId('category_id')->nullable();
        $table->foreignId('source_account_id')->nullable();
        $table->foreignId('account_id')->nullable();
        $table->foreignId('client_account_id')->nullable();

        $table->string('reference')->nullable();
        $table->text('notes')->nullable();

        $table->date('date');
        $table->date('arrival_date')->nullable();

        $table->decimal('total', 14, 4)->default(0.00);


        $table->enum('source_type', ['SMALL_BOX', 'BANK', 'DEBT', 'UNREGISTERED'])->default('UNREGISTERED');

        $table->string('cancel_type')->nullable();
        $table->text('cancel_reason')->nullable();
        $table->decimal('cancel_debt', 14, 4)->default(0.00);
        $table->date('sent_at')->nullable();
        $table->date('cancelled_at')->nullable();
        $table->date('returned_at')->nullable();
        $table->date('received_at')->nullable();
        $table->string('status');
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
        //
    }
};
