<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_index_returns_json_with_category(): void
    {
        $category = Category::factory()->create(['name' => '表示崩れ']);
        Ticket::factory()->create(['category_id' => $category->id, 'title' => 'メニューが崩れる']);

        $response = $this->getJson('/api/tickets');

        $response->assertOk();
        $response->assertJsonPath('data.0.title', 'メニューが崩れる');
        $response->assertJsonPath('data.0.category.name', '表示崩れ');
    }

    public function test_api_index_filters_by_status(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->create(['category_id' => $category->id, 'status' => 'open']);
        Ticket::factory()->create(['category_id' => $category->id, 'status' => 'resolved']);

        $response = $this->getJson('/api/tickets?status=resolved');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.status', 'resolved');
    }

    public function test_api_store_creates_ticket_and_returns_201(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/tickets', [
            'category_id' => $category->id,
            'title' => '新規チケット',
            'description' => 'APIから作成',
            'priority' => 'low',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.title', '新規チケット');
        $this->assertDatabaseHas('tickets', ['title' => '新規チケット', 'status' => 'open']);
    }

    public function test_api_store_response_reflects_default_status_immediately(): void
    {
        // Regression test: Ticket::create() previously returned an in-memory model with
        // status/priority = null (Eloquent doesn't refresh attributes left to the DB default),
        // even though the DB row itself was correct. This asserts the API *response*, which is
        // what a real client sees right after creating a ticket -- not just the DB state.
        $category = Category::factory()->create();

        $response = $this->postJson('/api/tickets', [
            'category_id' => $category->id,
            'title' => '既定値の確認',
            'description' => '本文',
            'priority' => 'normal',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'open');
    }

    public function test_api_store_returns_422_for_invalid_priority(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/tickets', [
            'category_id' => $category->id,
            'title' => '不正な優先度',
            'description' => '本文',
            'priority' => 'urgent',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('priority');
    }

    public function test_api_store_returns_422_for_missing_description(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/tickets', [
            'category_id' => $category->id,
            'title' => '本文なし',
            'priority' => 'normal',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('description');
    }

    public function test_api_validation_errors_are_japanese(): void
    {
        $response = $this->postJson('/api/tickets', []);

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.category_id.0', 'カテゴリを選択してください。');
        $response->assertJsonPath('errors.title.0', 'タイトルを入力してください。');
    }

    public function test_api_index_ignores_array_status_filter(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->create(['category_id' => $category->id]);

        $this->getJson('/api/tickets?status[]=open')->assertOk()->assertJsonCount(1, 'data');
    }
}
