<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifiedPhonenumber extends Model
{
    use HasFactory;

    protected $table = 'verified_phonenumbers';

    protected $fillable = ['phonenumber', 'verified', 'isOnWhatsapp'];

    public function verifies()
    {
        return $this->hasMany(VerifiedPhonenumberCheck::class, 'verify_id', 'id');
    }

    public function checks()
    {
        return $this->hasManyThrough(
            related: PhonenumberCheck::class,
            through: VerifiedPhonenumberCheck::class,
            firstKey: 'verify_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'check_id'
        );
    }
}
