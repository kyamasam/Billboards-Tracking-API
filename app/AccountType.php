<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $fillable =['name','description',];


    /**
     * Users of this AccountType
     *
     */
    public function Users(){
        return $this->hasMany(User::class);
    }

    /**
     * Permissions allowed for this UserType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function Permissions(){
        return $this->belongsToMany(AccountTypePermission::class , 'account_type_permissions', 'account_type_id', 'permission_id');
    }
}
