<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonenumberCheck extends Model
{
    use HasFactory;

    protected $table = 'phonenumber_checks';

    protected $fillable = ['user_id', 'description', 'done'];

    protected $casts = [
        'done' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifies()
    {
        return $this->hasManyThrough(
            related: VerifiedPhonenumber::class,
            through: VerifiedPhonenumberCheck::class,
            firstKey: 'check_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'verify_id'
        );
    }

    public function groups()
    {
        return $this->hasManyThrough(
            related: UserGroup::class,
            through: CheckGroup::class,
            firstKey: 'check_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'group_id'
        );
    }
}
