<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_tickets_with_category_name(): void
    {
        $category = Category::factory()->create(['name' => '決済']);
        Ticket::factory()->create(['category_id' => $category->id, 'title' => 'カード決済が失敗する']);

        $response = $this->get(route('tickets.index'));

        $response->assertOk();
        $response->assertSee('カード決済が失敗する');
        $response->assertSee('決済');
    }

    public function test_index_status_filter_only_shows_matching_tickets(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->create(['category_id' => $category->id, 'title' => '未対応チケット', 'status' => 'open']);
        Ticket::factory()->create(['category_id' => $category->id, 'title' => '解決済みチケット', 'status' => 'resolved']);

        $response = $this->get(route('tickets.index', ['status' => 'resolved']));

        $response->assertOk();
        $response->assertSee('解決済みチケット');
        $response->assertDontSee('未対応チケット');
    }

    public function test_store_creates_a_ticket_and_redirects_to_its_show_page(): void
    {
        $category = Category::factory()->create();

        $response = $this->post(route('tickets.store'), [
            'category_id' => $category->id,
            'title' => 'ログインできない',
            'description' => 'パスワードを入力してもログインできない。',
            'priority' => 'high',
            'assignee_name' => null,
        ]);

        $ticket = Ticket::sole();
        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertSame('ログインできない', $ticket->title);
        $this->assertSame('open', $ticket->status);
    }

    public function test_store_rejects_missing_title(): void
    {
        $category = Category::factory()->create();

        $response = $this->post(route('tickets.store'), [
            'category_id' => $category->id,
            'description' => '本文のみ',
            'priority' => 'normal',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertSame(0, Ticket::count());
    }

    public function test_store_rejects_unknown_category(): void
    {
        $response = $this->post(route('tickets.store'), [
            'category_id' => 9999,
            'title' => '存在しないカテゴリ',
            'description' => '本文',
            'priority' => 'normal',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_show_displays_ticket_detail(): void
    {
        $category = Category::factory()->create(['name' => 'ログイン']);
        $ticket = Ticket::factory()->create([
            'category_id' => $category->id,
            'title' => '二段階認証が届かない',
            'assignee_name' => '田中',
        ]);

        $response = $this->get(route('tickets.show', $ticket));

        $response->assertOk();
        $response->assertSee('二段階認証が届かない');
        $response->assertSee('田中');
    }

    public function test_show_returns_404_for_unknown_ticket(): void
    {
        $response = $this->get('/tickets/9999');

        $response->assertNotFound();
    }

    public function test_store_validation_errors_are_japanese(): void
    {
        $response = $this->post(route('tickets.store'), []);

        $response->assertSessionHasErrors([
            'category_id' => 'カテゴリを選択してください。',
            'title' => 'タイトルを入力してください。',
            'description' => '内容を入力してください。',
            'priority' => '優先度を選択してください。',
        ]);
    }

    public function test_index_ignores_array_status_filter(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->create(['category_id' => $category->id, 'title' => '配列フィルタ確認']);

        $this->get('/?status[]=open')->assertOk()->assertSee('配列フィルタ確認');
        $this->get('/?status=bogus')->assertOk()->assertSee('配列フィルタ確認');
    }

    public function test_priority_selection_is_restored_after_validation_error(): void
    {
        $response = $this->followingRedirects()
            ->from(route('tickets.index'))
            ->post(route('tickets.store'), ['priority' => 'high']);

        $response->assertOk();
        $response->assertSee('<option value="high" selected>', false);
        $response->assertDontSee('<option value="normal" selected>', false);
    }
}
