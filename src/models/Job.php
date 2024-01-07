<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = ['name', 'payload', 'attempts', 'success_at', 'failed_at'];
}
