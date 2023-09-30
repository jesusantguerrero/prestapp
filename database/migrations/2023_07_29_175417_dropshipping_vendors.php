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
      Schema::create('dropshipping_vendors', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id');
        $table->foreignId('user_id');
        $table->foreignId('client_id')->constrained()->cascadeOnDelete();

        $table->string('site')->nullable();
        $table->string('endpoint')->nullable();
        $table->string('reference')->nullable();
        $table->text('notes')->nullable();

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
      Schema::dropIfExists('dropshipping_vendors');
    }
};
