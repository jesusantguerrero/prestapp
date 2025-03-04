<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_unit_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_unit_id')->constrained()->onDelete('cascade');
            $table->json('photos')->nullable();
            $table->text('listing_description')->nullable();
            $table->json('features_highlights')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->json('virtual_tour')->nullable();
            $table->json('floor_plans')->nullable();
            $table->json('video_urls')->nullable();
            $table->json('social_media_links')->nullable();
            $table->json('seo_metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_unit_listings');
    }
}; 