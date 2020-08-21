<?php

namespace App;
use App\Models\Traits\Setter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use Setter, SoftDeletes;
    //
}
