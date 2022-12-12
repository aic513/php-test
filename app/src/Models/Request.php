<?php

namespace Src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $table = 'request';

    protected $fillable = ['request_url', 'body', 'status_code', 'curl_status'];

    public function response(): HasOne
    {
        return $this->hasOne(ResponseHeaders::class);
    }
}
