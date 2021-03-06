<?php

namespace App\Models;

class Module extends BaseModel
{
    protected $fillable = ['identifier', 'type', 'name', 'status'];

    public function getModuleByIdentifier($identifier)
    {
        return $this->where('identifier', '=', $identifier)->get()->first();
    }
}
