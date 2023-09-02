<?php

namespace Tests\Feature\Project;

use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use App\Models\Project;
use Faker\Factory as FakerFactory;

class ProjectTest extends FeatureBaseTestCase
{
    protected $user;
    protected $prj_member_ids;
    
    protected $project;

    protected $members;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->members = User::factory(4)->create();
        
        // プロジェクトへメンバー追加
        $this->prj_member_ids = [
            $this->user->id,
            $this->members[0]->id,
            $this->members[2]->id
        ];
        
        // 更新対象のプロジェクト作成
        $this->project = Project::factory()->create([
            'project_name' => 'Project',
            'responsible_person_id' => $this->user->id,
            'created_user_id' => $this->user->id,
            'updated_user_id' => $this->user->id,
        ]);

        $this->project->users()->attach($this->prj_member_ids);
    }

    // プロジェクト作成フォーム表示テスト
    public function test_can_create_project()
    {
        $response =  $this->actingAs($this->user)->get('/project/create');
        $response->assertStatus(200);
    }

    // プロジェクトの保存テスト
    public function test_can_store_project()
    {
        $param = [
            'project_name' => FakerFactory::create()->text(20),
            'responsible_person_id' => $this->user->id,
            'user_id' => $this->prj_member_ids
        ];

        $response = $this->actingAs($this->user)->post('/project/store', $param);
    
        $response->assertStatus(302);
        
        $project = Project::latest('id')->first();
        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'project_name' => $param['project_name'],
            'responsible_person_id' => $param['responsible_person_id'],
            'created_user_id' => $this->user->id,
            'updated_user_id' => $this->user->id
        ]);
        
        // 関連テーブルへのプロジェクトメンバーの保存結果を確認
        foreach ($this->prj_member_ids as $id) {
            $this->assertDatabaseHas('project_user', [
                'project_id' => $project->id,
                'user_id'=> $id
            ]);
        }
    }

    // プロジェクトの編集フォーム表示テスト
    public function test_can_edit_project()
    {
        $url = route('project.edit', $this->project);
        
        // ユーザーがプロジェクトを編集できる場合（ポリシーに従う場合）
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);
    }

    // プロジェクトの編集完了テスト
    public function test_can_update_project()
    {
        $url = route('project.edit.put', $this->project);
        
        $param = [
            'project_name' => 'New Project Name',
            'responsible_person_id' => $this->user->id,
            'user_id' => [
                $this->user->id,
                $this->members[1]->id,
                $this->members[2]->id
            ]
        ];
        
        $response = $this->actingAs($this->user)->put($url, $param);
        $response->assertStatus(302);
        
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
            if (in_array($member->id, $param['user_id'])) {
                $this->assertDatabaseHas('project_user', $projectMembers);
            } else {
                $this->assertDatabaseMissing('project_user', $projectMembers);
            }
        }
    }
}
