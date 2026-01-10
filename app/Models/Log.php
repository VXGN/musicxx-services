<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "logs";
    protected $primaryKey = 'log_id';
    protected $fillable = ["log_method", "log_ip", "log_useragent", "log_url"];
    protected $hidden = ["updated_at"];
}
