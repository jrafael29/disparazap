<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckGroup extends Model
{
    use HasFactory;

    protected $table = 'check_groups';

    protected $fillable = ['check_id', 'group_id'];

    public function check()
    {
        return $this->belongsTo(PhonenumberCheck::class);
    }
    public function group()
    {
        return $this->belongsTo(UserGroup::class);
    }
}
