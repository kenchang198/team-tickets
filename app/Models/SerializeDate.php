<?php

namespace App\Models;

use DateTimeInterface;

trait SerializeDate
{
    /**
     * JSONレスポンスした created_atのタイムゾーンがズレるので作成
     * 
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}