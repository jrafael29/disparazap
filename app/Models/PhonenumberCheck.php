<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonenumberCheck extends Model
{
    use HasFactory;

    protected $table = 'phonenumber_checks';

    protected $fillable = ['user_id', 'description', 'done'];

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
}
