<?php

namespace Src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseHeaders extends Model
{
    use HasFactory;

    protected $fillable = ['request_id', 'header_key', 'header_value'];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
