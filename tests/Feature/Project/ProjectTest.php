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
    protected $project_name;

    protected $project;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // プロジェクトへメンバー追加
        $this->prj_member_ids = [
            $this->user->id,
            User::factory()->create()->id,
            User::factory()->create()->id,
            User::factory()->create()->id
        ];
        
        $this->project_name = FakerFactory::create()->text(20);

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
        $response = $this->actingAs($this->user)->post('/project/store', [
            'project_name' => $this->project_name,
            'responsible_person_id' => $this->user->id,
            'user_id' => $this->prj_member_ids
        ]);
    
        $response->assertStatus(302);
    
        $this->assertDatabaseHas('projects', [
            'project_name' => $this->project_name,
            'responsible_person_id' => $this->user->id,
            'created_user_id' => $this->user->id,
            'updated_user_id' => $this->user->id
        ]);
        
        $project = Project::latest('id')->first();

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
            'user_id' => $this->prj_member_ids
        ];
        
        $response = $this->actingAs($this->user)->put($url, $param);
        $response->assertStatus(302);
        
        $updatedProject = [
            'id' => $this->project->id,
            'project_name' => $param['project_name'],
            'responsible_person_id' => $param['responsible_person_id']
        ];

        $this->assertDatabaseHas('projects', $updatedProject);
    }
}
