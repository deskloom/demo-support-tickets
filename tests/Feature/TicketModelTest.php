<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_belongs_to_category(): void
    {
        $category = Category::factory()->create(['name' => 'その他']);
        $ticket = Ticket::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($ticket->category->is($category));
    }

    public function test_category_has_many_tickets(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->tickets);
    }

    public function test_is_open_returns_false_only_when_resolved(): void
    {
        $open = Ticket::factory()->make(['status' => 'open']);
        $inProgress = Ticket::factory()->make(['status' => 'in_progress']);
        $resolved = Ticket::factory()->make(['status' => 'resolved']);

        $this->assertTrue($open->isOpen());
        $this->assertTrue($inProgress->isOpen());
        $this->assertFalse($resolved->isOpen());
    }

    public function test_status_scope_with_null_returns_all_tickets(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->create(['category_id' => $category->id, 'status' => 'open']);
        Ticket::factory()->create(['category_id' => $category->id, 'status' => 'resolved']);

        $this->assertCount(2, Ticket::status(null)->get());
    }
}
