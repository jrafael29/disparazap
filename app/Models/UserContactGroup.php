<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContactGroup extends Model
{
    use HasFactory;

    protected $table = 'user_contact_groups';

    protected $fillable = ['user_contact_id', 'user_group_id', 'active'];

    // public function contacts()
    // {
    //     return $this->hasManyThrough(Contact::class, UserContact::class,);
    // }

    public function userContact()
    {
        return $this->belongsTo(UserContact::class, 'user_contact_id', 'id');
    }
    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id', 'id');
    }
}
