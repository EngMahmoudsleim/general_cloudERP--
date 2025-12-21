<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountingMapping extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function mappingKey()
    {
        return $this->belongsTo(AccountingMappingKey::class, 'mapping_key_id');
    }

    public function account()
    {
        return $this->belongsTo(AccountingAccount::class, 'account_id');
    }
}
