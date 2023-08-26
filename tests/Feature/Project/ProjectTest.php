<?php

namespace Tests\Feature\Project;

use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use App\Models\Project;
use Faker\Factory as FakerFactory;

class ProjectTest extends FeatureBaseTestCase
{
    // プロジェクト作成フォーム表示テスト
    public function test_can_create_project()
    {
        $user = User::factory()->create();
        $response =  $this->actingAs($user)->get('/project/create');
        $response->assertStatus(200);
    }

    // プロジェクトの保存テスト
    public function test_can_store_project()
    {
        $user = User::factory()->create();
        
        // プロジェクトへメンバー追加
        $prj_member_ids = [
            $user->id,
            User::factory()->create()->id,
            User::factory()->create()->id,
            User::factory()->create()->id
        ];
        
        $project_name = FakerFactory::create()->text(20);
        
        $response = $this->actingAs($user)->post('/project/store', [
            'project_name' => $project_name,
            'responsible_person_id' => $user->id,
            'user_id' => $prj_member_ids
        ]);
    
        $response->assertStatus(302);
    
        $this->assertDatabaseHas('projects', [
            'project_name' => $project_name,
            'responsible_person_id' => $user->id,
            'created_user_id' => $user->id,
            'updated_user_id' => $user->id
        ]);
        
        $project = Project::latest('id')->first();

        // 関連テーブルへのプロジェクトメンバーの保存結果を確認
        foreach ($prj_member_ids as $id) {
            $this->assertDatabaseHas('project_user', [
                'project_id' => $project->id,
                'user_id'=> $id
            ]);
        }
    }
}
