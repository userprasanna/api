<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker,RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testgetAllRecordsNull()
    {
        $response = $this->get('api/tasks/get_all_records');
        $response->assertStatus(200)->assertJsonStructure([
                'data' => [
                    '*' => []
                ]
            ]);
    }

    public function testgetTaskByUnknownKey()
    {
        $response = $this->get('api/tasks/'.$this->faker->name);
        $response->assertStatus(404)->assertJsonStructure([
                'data' => [
                    '*' => []
                ]
            ]);
    }

    public function testgetTaskByNumbers()
    {
        $response = $this->get('api/tasks/'.$this->faker->unique()->numberBetween(1, 200));
        $response->assertStatus(400)
            ->assertJsonStructure(['error']);
    }

    public function testShowForMissingParam() {
        $payload = [
            'value' => "test"
        ];
        $this->json('post', "api/tasks")
             ->assertStatus(400)
             ->assertJsonStructure(['error']);
        
    }

    public function testgetTaskByKey()
    {
        $key = $this->faker->name;
        $payload = [
            'key' => $key,
            'value' => $this->faker->name,
        ];
        $create = $this->post('api/tasks',$payload);
        $response = $this->get('api/tasks/'.$key);
        $response->assertStatus(200)->assertJsonStructure([
                'data' => [
                    '*' => [
                        'key',
                        'value',
                        'datetime'
                    ]
                ]
            ]);
    }

    public function testcreateTaskByMissingParam()
    {
        $payload = [
            'key' => $this->faker->name
        ];
        $this->json('post', 'api/tasks', $payload)
        ->assertStatus(400)
             ->assertJsonStructure(['error']);
    }

    public function testcreateTaskByInvalidParamType()
    {
        $payload = [
            'key' => $this->faker->unique()->numberBetween(1, 200),
            'value'=>$this->faker->name
        ];
        $this->json('post', 'api/tasks', $payload)
        ->assertStatus(400)
             ->assertJsonStructure(['error']);
    }

    public function testcreateTask()
    {
        $payload = [
            'key' => $this->faker->name,
            'value' => $this->faker->name,
        ];
        $this->json('post', 'api/tasks', $payload)
        ->assertStatus(200)
        ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'key',
                        'value',
                        'datetime'
                    ]
                ]
            ]);
        $this->assertDatabaseHas('tasks', $payload);
    }

    public function testlistRecords()
    {
        $response = $this->get('api/tasks/get_all_records');
        $response->assertStatus(200)->assertJsonStructure([
                'data' => [
                    '*' => [
                        'key',
                        'value',
                        'datetime'
                    ]
                ]
            ]);
    }
}
