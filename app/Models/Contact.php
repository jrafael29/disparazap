<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = ['phonenumber', 'description', 'active'];

    public function users()
    {
        return $this->hasManyThrough(User::class, UserContact::class, 'contact_id', 'id', 'id', 'user_id');
    }
}
