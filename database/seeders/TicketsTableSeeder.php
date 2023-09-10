<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TicketsTableSeeder extends Seeder
{
    public function run()
    {
        $ticketsData = [
            [
                'ticket_name' => '新機能の追加',
                'responsible_person_id' => 1,
                'project_id' => 1,
                'status_code' => 'active',
                'content' => "新機能をプロジェクトに統合するためのチケットです。詳細な仕様は以下の通りです。\n\nこの新機能はユーザがアカウントを作成する際の手続きを簡素化します。\nユーザが必要な情報を提供しやすくし、エラーメッセージを改善します。\n画面上に新しいボタンを追加し、新機能へのアクセスを提供します。",
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'created_at' => now(),
                'created_user_id' => 1,
                'updated_at' => now(),
                'updated_user_id' => 1,
                'del_flg' => false,
            ],
            [
                'ticket_name' => 'バグ修正: ユーザ登録エラー',
                'responsible_person_id' => 2,
                'project_id' => 1,
                'status_code' => 'active',
                'content' => "ユーザ登録時にエラーが発生しているため、修正が必要です。問題の詳細は以下の通りです。\n\nユーザ登録フォームの送信時にエラー画面が表示される問題です。\nエラーメッセージは「必須フィールドが未入力です」と表示されますが、すべての必須フィールドは正しく入力されています。\nエラーの再現手順は以下の通りです。",
                'start_date' => now(),
                'end_date' => now()->addDays(5),
                'created_at' => now(),
                'created_user_id' => 2,
                'updated_at' => now(),
                'updated_user_id' => 2,
                'del_flg' => false,
            ],
            [
                'ticket_name' => 'プロジェクトドキュメンテーションの更新',
                'responsible_person_id' => 3,  // 責任者のユーザID (事前に作成済み)
                'project_id' => 1,  // プロジェクトID (事前に作成済み)
                'status_code' => 'not-started',
                'content' => "プロジェクトのドキュメンテーションを最新の状態に保つためのチケットです。更新が必要な内容は以下の通りです。\n\nユーザガイドのセクション1.2を更新し、新機能の利用方法に関する情報を追加します。\nAPIドキュメントのエンドポイント一覧を最新のプロジェクト構成に合わせて更新します。\nドキュメンテーションの画像とスクリーンショットを最新のバージョンに差し替えます。",
                'start_date' => now(),
                'end_date' => now()->addDays(3),
                'created_at' => now(),
                'created_user_id' => 3,
                'updated_at' => now(),
                'updated_user_id' => 3,
                'del_flg' => false,
            ],
            // 他のチケットデータも同様に追加
        ];

        foreach ($ticketsData as $ticket) {
            DB::table('tickets')->insert($ticket);
        }
    }
}
