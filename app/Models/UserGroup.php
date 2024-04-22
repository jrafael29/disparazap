<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'user_groups';

    protected $fillable = ['user_id', 'name', 'description', 'active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userContacts()
    {
        return $this->hasManyThrough(
            related: UserContact::class,
            through: UserContactGroup::class,
            firstKey: 'user_group_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'user_contact_id'
        );
    }

    public function checks()
    {
        return $this->hasManyThrough(
            related: PhonenumberCheck::class,
            through: CheckGroup::class,
            firstKey: 'group_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'check_id'
        );
    }
}
