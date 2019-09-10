<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','user_name','last_name', 'middle_name', 'first_name', 'msisdn','msisdn_verified', 'account_type', 'avatar', 'cover_photo', 'is_verified', 'is_trusted', 'account_status','email_verification_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * get the Campaigns for this user
     *
     */

    public function Campaign(){
        return $this->hasMany(Campaign::class ,'owner_id');
    }
    /**
     * get the Budgets for this user
     *
     */
    public function Budget(){
        return $this->hasMany(Budget::class);
    }
    /**
     * get the AccountType for this user
     *
     */
    public function AccountType(){
        return $this->belongsTo(AccountType::class , 'account_type');
    }
    /**
     * get the AccountStatus for this user
     *
     */
    public function AccountStatus(){
        return $this->belongsTo(AccountStatus::class, 'account_status');
    }
    /**
     * the wallet associated with this user
     *
     */
    public function Wallet(){
        return $this->hasOne(Wallet::class);
    }


    public function MpesaStkCallback(){
        return $this->hasMany(MpesaStkCallback::class);
    }
}
