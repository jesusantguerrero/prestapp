<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_unit_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_unit_id')->constrained()->onDelete('cascade');
            $table->json('owner_preferences')->nullable();
            $table->integer('minimum_lease_term')->default(12);
            $table->integer('maximum_lease_term')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('pet_policy')->nullable();
            $table->json('smoking_policy')->nullable();
            $table->json('guest_policy')->nullable();
            $table->json('parking_policy')->nullable();
            $table->json('noise_policy')->nullable();
            $table->json('additional_rules')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_unit_preferences');
    }
}; 