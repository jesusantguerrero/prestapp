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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable();
            $table->foreignId('user_id')->nullable();

            // Basic
            $table->string('display_name');
            $table->string('dni')->nullable();
            $table->string('dni_type')->nullable();
            $table->text('profile_photo_path')->nullable();

            // main contact
            $table->string('names');
            $table->string('lastnames')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('cellphone')->nullable();
            $table->text('notes')->nullable();

            // direction
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('sector')->nullable();
            $table->string('street')->nullable();
            $table->string('ext_number')->nullable();
            $table->text('address_details')->nullable();

            $table->string('work_name');
            $table->string('lastnames')->nullable();
            $table->string('work_email')->nullable();
            $table->string('work_phone')->nullable();
            $table->text('work_address_details')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->integer('owner_distribution_date')->nullable();
            $table->json('generated_distribution_dates')->default('[]');

            //
            $table->boolean('is_company')->default(false);
            $table->boolean('is_tenant')->default(false);
            $table->boolean('is_lender')->default(false);
            $table->boolean('is_owner')->default(false);
            $table->enum('type', ['PROSPECT', 'CONTACT', 'CUSTOMER'])->default('PROSPECT');

            // state
            $table->string('status')->default("INACTIVE");
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
        Schema::dropIfExists('clients');
    }
};
