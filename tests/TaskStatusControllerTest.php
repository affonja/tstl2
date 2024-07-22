<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\TaskStatus;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

class TaskStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
        $this->taskStatus = TaskStatus::factory()->create();
    }

    public static function pathProvider(): array
    {
        return [
            ['/task_statuses', 200, 'taskStatuses.index'],
            ['/task_statuses/create', 302],
            ['/task_statuses/edit', 302]
        ];
    }

    #[DataProvider('pathProvider')]
    public function testAccessGuest($path, $code, $view = null)
    {
        auth()->logout();
        $response = $this->get($path);
        $response->assertStatus($code);
        if ($path === '/task_statuses') {
            $response->assertViewIs($view);
            $response->assertViewHas('taskStatuses');
        }
    }

    public function testIndex()
    {
        $response = $this->get('/task_statuses');

        $response->assertStatus(200);
        $response->assertViewIs('taskStatuses.index');
        $response->assertViewHas('taskStatuses');
    }

    public function testCreate()
    {
        $response = $this->get('/task_statuses/create');
        $response->assertStatus(200);
        $response->assertViewIs('taskStatuses.create');
    }

    public function testEdit()
    {
        $response = $this->get("/task_statuses/{$this->taskStatus->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('taskStatuses.edit');
        $response->assertViewHas('taskStatus', $this->taskStatus);
    }

    public function testStore()
    {
        $taskStatusName = fake()->word;
        $response = $this->post('/task_statuses', ['name' => $taskStatusName]);
        $this->assertDatabaseHas('task_statuses', ['name' => $taskStatusName]);
        $response->assertRedirectToRoute('task_statuses.index');
    }

    public function testUpdate()
    {
        $updatedData = ['name' => fake()->word];

        $response = $this->patch("/task_statuses/{$this->taskStatus->id}", $updatedData);

        $response->assertRedirect('/task_statuses');
        $this->assertDatabaseHas('task_statuses', $updatedData);
    }

    public function testDestroy()
    {
        $response = $this->delete("/task_statuses/{$this->taskStatus->id}");

        $response->assertRedirect('/task_statuses');
        $this->assertDatabaseMissing('task_statuses', ['id' => $this->taskStatus->id]);
    }

    public function testValidate()
    {
        $validateProvider = [
            ['post', '/task_statuses', ['name' => $this->taskStatus->name]],
            ['patch', "/task_statuses/{$this->taskStatus->id}", ['name' => $this->taskStatus->name]],
//            ['delete', "/task_statuses/100", []],
        ];

        foreach ($validateProvider as [$method, $path, $param]) {
            $response = $this->$method($path, $param);
            $response->assertStatus(302);
            $response->assertRedirect('/');
            $flashMessages = session('flash_notification');
            $this->assertStringContainsString('Статус с таким именем уже существует', $flashMessages[0]['message']);
        }
    }
}
