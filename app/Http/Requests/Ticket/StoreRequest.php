<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ExistsWithType;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Implicit Bindingで渡されたProjectモデル
        $project = $this->route('project');

        return [
            'ticket_name' => 'required|string|max:100',
            't_responsible_person_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'content' => 'required|string|max:1000',
            'user_id.*' => [
                (new ExistsWithType('project_user', 'user_id'))
                    ->withCondition('project_id', '=', $project->id)
            ],
        ];
    }
}
