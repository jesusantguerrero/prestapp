<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_unit_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_unit_id')->constrained('property_units');
            $table->foreignId('property_id')->constrained('properties');
            $table->date('snapshot_date');
            $table->string('status');
            $table->string('rent_status')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->decimal('monthly_rent', 12, 2)->nullable();
            $table->string('invoice_status')->nullable();
            $table->decimal('invoice_amount', 12, 2)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['property_unit_id', 'snapshot_date']);
            $table->index(['property_id', 'snapshot_date']);
            $table->index('status');
            $table->index('snapshot_date');
            $table->index('client_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_unit_snapshots');
    }
}; 