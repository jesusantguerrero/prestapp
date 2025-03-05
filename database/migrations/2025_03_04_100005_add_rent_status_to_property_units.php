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
        Schema::table('property_units', function (Blueprint $table) {
            // Add indexes for optimizing joins
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'property_id']);
            $table->index(['team_id', 'owner_id']);
        });

        Schema::table('rents', function (Blueprint $table) {
            // Add indexes for optimizing joins and status filtering
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'unit_id']);
            $table->index(['team_id', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_units', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'status']);
            $table->dropIndex(['team_id', 'property_id']);
            $table->dropIndex(['team_id', 'owner_id']);
        });

        Schema::table('rents', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'status']);
            $table->dropIndex(['team_id', 'property_unit_id']);
            $table->dropIndex(['team_id', 'owner_id']);
        });
    }
}; 