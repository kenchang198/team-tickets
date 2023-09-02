<?php

namespace Tests\Feature\Project;

use Tests\Feature\FeatureBaseTestCase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class ProjectDeleteTest extends FeatureBaseTestCase
{
    protected $user;
    protected $members;
    protected $project;
    protected $prj_member_ids;
    
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
        
        // 削除対象のプロジェクト作成
        $this->project = Project::factory()->create([
            'project_name' => 'Project',
            'responsible_person_id' => $this->user->id,
            'created_user_id' => $this->user->id,
            'updated_user_id' => $this->user->id,
        ]);

        $this->project->users()->attach($this->prj_member_ids);
    }

    public function test_can_delete_project()
    {
        $url = route('project.delete', $this->project);

        $response = $this->actingAs($this->user)->delete($url);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('projects', ['id' => $this->project->id]);
    }
}
