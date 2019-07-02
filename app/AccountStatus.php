<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    protected $fillable = [ 'status_name', 'description'];

    /**
     * get the Users with this status set
     *
     */
    public function Users(){
        return $this->belongsTo(User::class);
    }
}
