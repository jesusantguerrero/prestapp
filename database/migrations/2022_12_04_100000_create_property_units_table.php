<?php

use App\Domains\Properties\Models\Property;
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
        Schema::create('property_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->foreignId('owner_id');
            $table->foreignId('property_id')->nullable();

            // terms
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->decimal('price', 11, 2)->default(0.00);
            $table->decimal('commission', 11, 2)->default(0.00);
            $table->enum('commission_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');

            // state
            $table->enum('status', [
                Property::STATUS_BUILDING,
                Property::STATUS_AVAILABLE,
                Property::STATUS_RENTED,
                Property::STATUS_MAINTENANCE,
            ])->default(Property::STATUS_AVAILABLE);
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
        Schema::dropIfExists('property_units');
    }
};
