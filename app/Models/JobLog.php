<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    protected $table = 'job_logs';
    protected $fillable = ['name', 'status', 'message'];
}
