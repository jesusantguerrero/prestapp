<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_unit_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_unit_id')->constrained()->onDelete('cascade');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->enum('condition_status', ['EXCELLENT', 'GOOD', 'FAIR', 'POOR'])->default('GOOD');
            $table->decimal('maintenance_cost', 14, 4)->nullable();
            $table->string('maintenance_type')->nullable();
            $table->string('maintenance_provider')->nullable();
            $table->json('maintenance_documents')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_unit_maintenance');
    }
}; 