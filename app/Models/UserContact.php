<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    use HasFactory;

    protected $table = 'user_contacts';

    protected $fillable = ['user_id', 'contact_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
