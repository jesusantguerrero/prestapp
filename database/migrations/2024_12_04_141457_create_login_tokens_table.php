<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->foreignId('client_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
          $table->foreignId('client_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_tokens');

        Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('client_id')->nullable();
        });
    }
};
