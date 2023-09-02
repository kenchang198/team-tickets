<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;

trait ProjectTestCommonSetup
{
    protected function projectSetup()
    {
        $this->user = User::factory()->create();
        $this->members = User::factory(4)->create();
        
        // プロジェクトへメンバー追加
        $this->prj_member_ids = [
            $this->user->id,
            $this->members[0]->id,
            $this->members[2]->id
        ];
        
        $this->project = Project::factory()->create([
            'project_name' => 'Project',
            'responsible_person_id' => $this->user->id,
            'created_user_id' => $this->user->id,
            'updated_user_id' => $this->user->id,
        ]);

        $this->project->users()->attach($this->prj_member_ids);
    }
}
