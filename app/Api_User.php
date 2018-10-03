<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Api_User extends Model
{
    protected $fillable = [
        'bearer', 'is_active',
    ]; 

    protected $table = 'api_users';
}