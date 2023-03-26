<?php

namespace Tests\Feature\Property;

use App\Domains\Properties\Models\PropertyUnit;
use Tests\Feature\Property\Helpers\PropertyBase;

class PropertyUnitTest extends PropertyBase
{

  public function testItShouldGetPropertyUnits()
  {
      $this->actingAs($this->user);

      $response = $this->get('/units');

      $response->assertStatus(200);
  }

  public function testItShouldCreatePropertyUnit() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post("/properties/{$this->property->id}/units/", $this->unitData);

    $response->assertStatus(200);
    $this->assertCount(4, $this->property->fresh()->units);
  }

  public function testItShouldUpdatePropertyUnit() {
    $this->actingAs($this->user);
    $unit = $this->createUnit();

    $this->put("/properties/{$this->property->id}/units/{$unit->id}", [
      "description" => 'Updated description'
    ]);

    $this->assertEquals($unit->fresh()->description, 'Updated description');
  }

  public function testItShouldDeletePropertyUnit() {
    $this->actingAs($this->user);
    $unit = $this->createUnit();

    $this->delete("/properties/{$this->property->id}/units/{$unit->id}");

    $this->assertCount(2, PropertyUnit::all());
  }
}

