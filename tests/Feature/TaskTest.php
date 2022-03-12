<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testgetAllRecords()
    {
        $response = $this->get('api/tasks/get_all_records');

        $response->assertStatus(200);
    }

    public function testgetTaskByKey()
    {
        $response = $this->get('api/tasks/dummydata');

        $response->assertStatus(404);
    }

    public function testcreateTask()
    {
        $data = [
            'key' => $this->faker->name,
            'value' => $this->faker->name,
        ];
        $response = $this->post('api/tasks', $data);

        $response->assertStatus(201);
    }
    public function testlistRecords()
    {
        $response = $this->get('api/tasks/get_all_records');

        $response->assertStatus(200)->assertJsonStructure([
                '*' => ['key','value','datetime'],
            ]);
    }
}
