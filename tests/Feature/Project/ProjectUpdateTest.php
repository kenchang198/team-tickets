<?php

namespace Tests\Feature\Project;

use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use Database\Factories\UserFactory;
use Tests\Feature\ProjectTestCommonSetup;

class ProjectUpdateTest extends FeatureBaseTestCase
{
    protected $user;
    protected $prj_member_ids;
    
    protected $project;

    protected $members;
    
    use ProjectTestCommonSetup;

    public function setUp(): void
    {
        parent::setUp();
        $this->projectSetup();
    }

    // プロジェクトの編集フォーム表示テスト
    public function test_can_edit_project()
    {
        $url = route('project.edit', $this->project);
        
        // ユーザーがプロジェクトを編集できる場合（ポリシーに従う場合）
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);

        // プロジェクトの責任者によるアクセス
        $response = $this->actingAs($this->responsible_person)->get($url);
        $response->assertStatus(200);
        
        // プロジェクト作成者とは異なる管理者ユーザによるアクセス
        $admin = User::factory()->create(['admin' => 1]);
        $response = $this->actingAs($admin)->get($url);
        $response->assertStatus(200);
        
        // 責任者、作成者でない通常のプロジェクトメンバー
        $response = $this->actingAs($this->members[2])->get($url);
        $response->assertStatus(403);
        
        // プロジェクトに属さない一般ユーザによるアクセス
        $other = User::factory()->create(['admin' => 0]);
        $response = $this->actingAs($other)->get($url);
        $response->assertStatus(403);
    }

    // プロジェクトの編集完了テスト
    public function test_can_update_project()
    {
        $url = route('project.edit.put', $this->project);
        
        $param = [
            'project_name' => 'New Project Name',
            'responsible_person_id' => $this->responsible_person->id,
            'user_id' => [
                $this->user->id,
                $this->members[1]->id,
                $this->members[2]->id,
            ]
        ];
        
        // プロジェクト作成者
        $response = $this->actingAs($this->user)->put($url, $param);
        $response->assertStatus(302);
        
        // プロジェクトの責任者による更新
        $response = $this->actingAs($this->responsible_person)->put($url, $param);
        $response->assertStatus(302);
        
        // プロジェクト作成者以外の管理者ユーザ
        $admin = User::factory()->create(['admin' => 1]);
        $response = $this->actingAs($admin)->put($url, $param);
        $response->assertStatus(302);

        // 責任者、作成者でない通常のプロジェクトメンバー
        $response = $this->actingAs($this->members[1])->put($url, $param);
        $response->assertStatus(403);
        
        // プロジェクトに属さないユーザ
        $other = User::factory()->create(['admin' => 0]);
        $response = $this->actingAs($other)->put($url, $param);
        $response->assertStatus(403);

        $updatedProject = [
            'id' => $this->project->id,
            'project_name' => $param['project_name'],
            'responsible_person_id' => $param['responsible_person_id']
        ];

        $this->assertDatabaseHas('projects', $updatedProject);

        // プロジェクト - メンバーの関連付けが更新されているかの確認
        foreach ($this->members as $member) {
            
            $projectMembers = [
                'project_id' => $this->project->id,
                'user_id' => $member->id
            ];

            // パラメータで指定したプロジェクトメンバーidである
            // （プロジェクトの責任者は自動的にメンバーに含まれる）
            if (in_array($member->id, $param['user_id']) 
                || $member->id === $this->responsible_person->id
            ) {
                $this->assertDatabaseHas('project_user', $projectMembers);
            } else {
                $this->assertDatabaseMissing('project_user', $projectMembers);
            }
        }
    }
}
