<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(array $attrs = []): Ticket
    {
        return Ticket::factory()->create(['category_id' => Category::factory()->create()->id] + $attrs);
    }

    public function test_web_update_changes_status_and_assignee_and_flashes_message(): void
    {
        $ticket = $this->ticket();

        $response = $this->patch(route('tickets.update', $ticket), [
            'status' => 'in_progress',
            'assignee_name' => '鈴木',
        ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $response->assertSessionHas('status', 'チケットを更新しました');
        $ticket->refresh();
        $this->assertSame('in_progress', $ticket->status);
        $this->assertSame('鈴木', $ticket->assignee_name);

        $this->followRedirects($response)->assertSee('チケットを更新しました');
    }

    public function test_web_update_can_clear_assignee(): void
    {
        $ticket = $this->ticket(['assignee_name' => '田中']);

        $this->patch(route('tickets.update', $ticket), ['status' => 'open', 'assignee_name' => ''])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertNull($ticket->refresh()->assignee_name);
    }

    public function test_web_update_rejects_invalid_status_with_japanese_error(): void
    {
        $ticket = $this->ticket();

        $response = $this->from(route('tickets.show', $ticket))
            ->patch(route('tickets.update', $ticket), ['status' => 'bogus']);

        $response->assertRedirect(route('tickets.show', $ticket));
        $response->assertSessionHasErrors(['status' => '状態は未対応・対応中・解決済みのいずれかを選択してください。']);
        $this->assertSame('open', $ticket->refresh()->status);
    }

    public function test_web_update_returns_404_for_missing_ticket(): void
    {
        $this->patch('/tickets/9999', ['status' => 'open'])->assertNotFound();
    }

    public function test_show_page_has_update_form(): void
    {
        $ticket = $this->ticket();

        $this->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('name="_method" value="PATCH"', false)
            ->assertSee('name="assignee_name"', false);
    }

    public function test_api_update_returns_json_with_new_status(): void
    {
        $ticket = $this->ticket();

        $response = $this->patchJson("/api/tickets/{$ticket->id}", ['status' => 'resolved', 'assignee_name' => '佐藤']);

        $response->assertOk();
        $response->assertJsonPath('data.status', 'resolved');
        $response->assertJsonPath('data.assignee_name', '佐藤');
    }

    public function test_api_update_rejects_invalid_status_with_422(): void
    {
        $ticket = $this->ticket();

        $this->patchJson("/api/tickets/{$ticket->id}", ['status' => 'bogus'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_api_update_returns_404_for_missing_ticket(): void
    {
        $this->patchJson('/api/tickets/9999', ['status' => 'open'])->assertNotFound();
    }

    public function test_index_is_paginated_and_keeps_status_filter_in_links(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->count(12)->create(['category_id' => $category->id, 'status' => 'open']);
        Ticket::factory()->count(3)->create(['category_id' => $category->id, 'status' => 'resolved']);

        $response = $this->get(route('tickets.index', ['status' => 'open']));

        $response->assertOk();
        $response->assertSee('status=open&amp;page=2', false);
        $this->assertCount(10, $response->viewData('tickets')->items());
        $this->assertSame(12, $response->viewData('tickets')->total());

        $this->get(route('tickets.index', ['status' => 'open', 'page' => 2]))
            ->assertOk()
            ->assertViewHas('tickets', fn ($p) => count($p->items()) === 2);
    }

    public function test_api_index_is_paginated(): void
    {
        Ticket::factory()->count(12)->create(['category_id' => Category::factory()->create()->id]);

        $this->getJson('/api/tickets')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 12);
    }
}
