<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['ログイン', '決済', '表示崩れ', 'その他'])
            ->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name])->id]);

        $tickets = [
            ['category' => 'ログイン', 'title' => 'パスワード再設定メールが届かない', 'description' => "「パスワードを忘れた方」から再設定を試みたが、メールが数時間経っても届かない。\n迷惑メールフォルダも確認済み。", 'status' => 'open', 'priority' => 'high', 'assignee_name' => null],
            ['category' => '決済', 'title' => 'クレジットカード決済でエラーコード403', 'description' => "購入手続きの最終確認画面で「決済エラー(403)」と表示され進めない。カード会社には問題ないと言われた。", 'status' => 'in_progress', 'priority' => 'high', 'assignee_name' => '田中'],
            ['category' => '表示崩れ', 'title' => 'スマホ表示でメニューが画面からはみ出す', 'description' => "iPhone Safariでナビゲーションメニューを開くと、右端が画面外にはみ出して一部のリンクが押せない。", 'status' => 'open', 'priority' => 'normal', 'assignee_name' => null],
            ['category' => 'その他', 'title' => 'アカウント削除の手順を知りたい', 'description' => "退会（アカウント削除）の手順がヘルプページに見当たらない。案内してほしい。", 'status' => 'resolved', 'priority' => 'low', 'assignee_name' => '佐藤'],
            ['category' => 'ログイン', 'title' => '二段階認証コードが期限内に届かない', 'description' => "SMSでの二段階認証コードが、有効期限(5分)を過ぎてから届くことが頻発している。", 'status' => 'in_progress', 'priority' => 'normal', 'assignee_name' => '田中'],
        ];

        foreach ($tickets as $t) {
            Ticket::create([
                'category_id' => $categories[$t['category']],
                'title' => $t['title'],
                'description' => $t['description'],
                'status' => $t['status'],
                'priority' => $t['priority'],
                'assignee_name' => $t['assignee_name'],
            ]);
        }
    }
}
