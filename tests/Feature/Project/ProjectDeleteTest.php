<?php

namespace Tests\Feature\Project;

use Tests\Feature\FeatureBaseTestCase;

use Tests\Feature\ProjectTestCommonSetup;

class ProjectDeleteTest extends FeatureBaseTestCase
{
    protected $user;
    protected $members;
    protected $project;
    protected $prj_member_ids;
    
    use ProjectTestCommonSetup;

    public function setUp(): void
    {
        parent::setUp();
        $this->projectSetup();
    }

    public function test_can_delete_project()
    {
        $url = route('project.delete', $this->project);

        $members = $this->project->users;

        $response = $this->actingAs($this->user)->delete($url);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('projects', ['id' => $this->project->id]);

        foreach ($members as $member) {
            $project_member = [
                'project_id' => $this->project->id,
                'user_id' => $member->id
            ];
            // プロジェクト - メンバーの関連情報も削除されているか確認する
            $this->assertDatabaseMissing('project_user', $project_member);
        }
        
    }
}
