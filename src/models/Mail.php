<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $fillable = ['from', 'to', 'cc', 'bcc', 'subject', 'body', 'user_id'];
}
