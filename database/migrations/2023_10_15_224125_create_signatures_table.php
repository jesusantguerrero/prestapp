<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signatures', function (Blueprint $table) {
          $table->id();
          $table->foreignId('team_id');
          $table->foreignId('user_id');
          $table->foreignId('client_id')->nullable();
          $table->foreignId('signable_id')->nullable();

          $table->string('signable_type');
          $table->string('entity_id');
          $table->string('title');
          $table->string('subtitle')->nullable();
          $table->string('text')->nullable();
          $table->string('image_url')->nullable();
          $table->timestamp('signed_at')->nullable();
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
      Schema::dropIfExists('signatures');
    }
};
