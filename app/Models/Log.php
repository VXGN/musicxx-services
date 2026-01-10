<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "logs";
    protected $primaryKey = 'log_id';
    protected $fillable = ["log_userid", "log_method", "log_ip", "log_useragent", "log_url"];
    protected $hidden = ["updated_at"];
    public function user()
    {
        return $this->belongsTo(User::class, 'log_userid', 'id');
    }
}
