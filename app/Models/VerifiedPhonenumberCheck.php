<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifiedPhonenumberCheck extends Model
{
    use HasFactory;

    protected $table = 'verified_phonenumber_checks';

    protected $fillable = ['check_id', 'verify_id', 'done'];

    public function verify()
    {
        return $this->belongsTo(VerifiedPhonenumber::class);
    }
    public function check()
    {
        return $this->belongsTo(PhonenumberCheck::class);
    }
}
