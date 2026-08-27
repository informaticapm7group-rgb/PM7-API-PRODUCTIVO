<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'rut',
        'email',
        'phone',
        'company',
        'message',
    ];

    protected function trackingNumber(): Attribute
    {
        return Attribute::get(fn () => sprintf('PM7-%06d', $this->id));
    }
}
