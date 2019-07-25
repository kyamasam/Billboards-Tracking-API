<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{

    /**
     * Get the user who owns this wallet
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User(){
        return $this->belongsTo(User::class);
    }
}
