<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsWithType implements Rule
{
    protected $table;
    protected $column;
    protected $addConditions;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
        $this->addConditions = [];
    }

    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)
            ->whereRaw("BINARY {$this->column} = ?", [$value]);

        foreach ($this->addConditions as $condition) {
            $query->where($condition['column'], $condition['operator'], $condition['value']);
        }
        
        return $query->exists();
    }

    public function withCondition($column, $operator, $value)
    {
        $this->addConditions[] = compact('column', 'operator', 'value');
        return $this;
    }

    public function message()
    {
        return '選択された:attributeは正しくありません。';
    }
}
