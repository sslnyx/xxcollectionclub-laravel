<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SayHello extends Model
{
    public $table = 'say_hello';

    protected $fillable = ["name","email","message"];
}
