<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = ['establishment_id', 'day_of_week', 'open_time', 'close_time', 'is_closed'];
}
