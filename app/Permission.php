<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    protected $fillable = ['name','entity_name','record_id'];

    /**
     * UserType who have this permission.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function AccountTypes(){
        return $this->belongsToMany(AccountType::class , 'account_type_permissions', 'permission_id', 'account_type_id');
    }
}
