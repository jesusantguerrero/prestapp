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
      Schema::table('teams', function (Blueprint $table) {
        $table->text('profile_photo_path')->after('personal_team')->nullable();
        $table->string('app_profile_name')->after('profile_photo_path')->nullable();
      });
    }
};
