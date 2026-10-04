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

        // [カテゴリ, タイトル, 内容, 状態, 優先度, 担当]（すべて架空の内容・名字のみの架空担当者）
        $tickets = [
            ['ログイン', 'パスワード再設定メールが届かない', "「パスワードを忘れた方」から再設定を試みたが、メールが数時間経っても届かない。\n迷惑メールフォルダも確認済み。", 'open', 'high', null],
            ['決済', 'クレジットカード決済でエラーコード403', '購入手続きの最終確認画面で「決済エラー(403)」と表示され進めない。カード会社には問題ないと言われた。', 'in_progress', 'high', '田中'],
            ['表示崩れ', 'スマホ表示でメニューが画面からはみ出す', 'iPhone Safariでナビゲーションメニューを開くと、右端が画面外にはみ出して一部のリンクが押せない。', 'open', 'normal', null],
            ['その他', 'アカウント削除の手順を知りたい', '退会（アカウント削除）の手順がヘルプページに見当たらない。案内してほしい。', 'resolved', 'low', '佐藤'],
            ['ログイン', '二段階認証コードが期限内に届かない', 'SMSでの二段階認証コードが、有効期限(5分)を過ぎてから届くことが頻発している。', 'in_progress', 'normal', '田中'],
            ['決済', '請求書の宛名を変更したい', '先月分の請求書の宛名が旧社名のままになっている。再発行は可能か。', 'open', 'normal', null],
            ['決済', 'コンビニ払いの支払期限が過ぎてしまった', '支払期限を1日過ぎてしまった。期限の延長、または再度の払込票発行をお願いしたい。', 'resolved', 'normal', '高橋'],
            ['表示崩れ', 'ダークモードで文字が読めない', '端末をダークモードにすると、設定画面の見出しが背景と同じ色になり読めない。', 'in_progress', 'low', '伊藤'],
            ['ログイン', 'ログイン後に白い画面で止まる', 'ID・パスワード入力後、トップページに遷移せず白い画面のままになる。ブラウザはChromeの最新版。', 'open', 'high', '鈴木'],
            ['その他', '利用明細をCSVでダウンロードしたい', '経理提出用に、月ごとの利用明細をCSV形式で取得できる機能はあるか知りたい。', 'resolved', 'low', '佐藤'],
            ['決済', '二重に請求されている', '同じ注文について、カードの明細に同額の請求が2件並んでいる。確認をお願いしたい。', 'in_progress', 'high', '高橋'],
            ['表示崩れ', '一覧画面の表が横にスクロールできない', 'タブレットの縦向きで一覧を開くと、右側の列が切れて見えない。スクロールもできない。', 'open', 'normal', null],
            ['その他', '通知メールの配信頻度を減らしたい', '毎日届くお知らせメールが多すぎる。週1回のまとめ配信などに変更できないか。', 'open', 'low', null],
            ['ログイン', 'メールアドレスを変更したい', '登録メールアドレスを新しいものに変更したいが、設定画面に変更項目が見つからない。', 'resolved', 'normal', '伊藤'],
            ['表示崩れ', '画像のアップロード後にプレビューが歪む', '縦長の画像をアップロードすると、プレビューが横に引き伸ばされて表示される。', 'in_progress', 'normal', '鈴木'],
            ['その他', '利用規約の改定内容を確認したい', '先日の利用規約の改定で、何が変わったのか差分を教えてほしい。', 'resolved', 'low', '田中'],
            ['決済', '領収書が発行できない', '決済完了後のページに領収書のボタンがない。PDFで発行する方法を知りたい。', 'open', 'normal', null],
        ];

        // created_at をずらして、一覧の並び順（新しい順）が見て分かるようにする
        foreach ($tickets as $i => [$category, $title, $description, $status, $priority, $assignee]) {
            $ticket = new Ticket([
                'category_id' => $categories[$category],
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'assignee_name' => $assignee,
            ]);
            $ticket->created_at = now()->subHours((count($tickets) - $i) * 7);
            $ticket->save();
        }
    }
}
