<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $commentsData = [
            [
                'ticket_id' => 1,  // 対応するチケットのID
                'user_id' => 1,  // ユーザID
                'comment' => 'この新機能の優先度はどれくらいですか？',
                'created_at' => now()->subMinutes(30),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 1,  // 対応するチケットのID
                'user_id' => 2,  // ユーザID
                'comment' => '優先度は高いです。ユーザの要望が多い機能ですので、できるだけ早く進めましょう。',
                'created_at' => now()->subMinutes(20),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 1,  // 対応するチケットのID
                'user_id' => 3,  // ユーザID
                'comment' => 'この新機能の開発にはどのくらいの時間がかかると思いますか？',
                'created_at' => now()->subMinutes(10),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 1,  // 対応するチケットのID
                'user_id' => 1,  // ユーザID
                'comment' => '開発時間の見積もりは2週間です。スケジュールに合わせて進めてください。',
                'created_at' => now()->subMinutes(5),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 1,  // 対応するチケットのID
                'user_id' => 4,  // ユーザID
                'comment' => 'この新機能のテストに協力できます。どのようにテストすべきですか？',
                'created_at' => now(),  // 現在のコメント
                'updated_at' => null,
            ],
            // チケットIDが2のコメント（バグ修正: ユーザ登録エラーに関するコメント）
            [
                'ticket_id' => 2,
                'user_id' => 4,  // ユーザID
                'comment' => 'この問題はどのブラウザで発生していますか？',
                'created_at' => now()->subMinutes(30),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 2,
                'user_id' => 5,  // ユーザID
                'comment' => 'この問題は主にChromeブラウザで発生しています。他のブラウザでも確認しますが、まずはChromeを重点的に修正します。',
                'created_at' => now()->subMinutes(20),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 2,
                'user_id' => 6,  // ユーザID
                'comment' => 'エラーログを確認しましたが、原因が不明です。再現手順を教えていただけますか？',
                'created_at' => now()->subMinutes(10),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 2,
                'user_id' => 4,  // ユーザID
                'comment' => '再現手順は以下の通りです。...',
                'created_at' => now()->subMinutes(5),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 2,
                'user_id' => 5,  // ユーザID
                'comment' => '再現手順をもとに、デベロッパーが調査を進めています。修正には少々お時間をいただくかもしれません。',
                'created_at' => now(),  // 現在のコメント
                'updated_at' => null,
            ],

            // チケットIDが3のコメント（プロジェクトドキュメンテーションの更新に関するコメント）
            [
                'ticket_id' => 3,
                'user_id' => 7,  // ユーザID
                'comment' => 'どの部分のドキュメントを更新する必要がありますか？',
                'created_at' => now()->subMinutes(30),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 3,
                'user_id' => 1,  // ユーザID
                'comment' => '主にユーザガイドとAPIドキュメントを更新する必要があります。',
                'created_at' => now()->subMinutes(20),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 3,
                'user_id' => 8,  // ユーザID
                'comment' => '更新に必要な情報やリソースを提供していただけますか？',
                'created_at' => now()->subMinutes(10),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 3,
                'user_id' => 1,  // ユーザID
                'comment' => 'もちろんです。更新が必要な情報とリンクをまとめてお送りします。',
                'created_at' => now()->subMinutes(5),  // 過去のコメント
                'updated_at' => null,
            ],
            [
                'ticket_id' => 3,
                'user_id' => 7,  // ユーザID
                'comment' => '更新が完了したら通知をいただけますか？',
                'created_at' => now(),  // 現在のコメント
                'updated_at' => null,
            ],
        ];

        foreach ($commentsData as $comment) {
            DB::table('comments')->insert($comment);
        }
    }
}
