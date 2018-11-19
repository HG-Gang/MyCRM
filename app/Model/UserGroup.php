<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model {

    protected $table = 'user_group';
    protected $primaryKey = 'user_group_id';
 //   protected $guarded = [];
    public $timestamps = FALSE;

}
