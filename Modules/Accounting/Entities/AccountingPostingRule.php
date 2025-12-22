<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountingPostingRule extends Model
{
    protected $guarded = [];

    public function lines()
    {
        return $this->hasMany(AccountingPostingRuleLine::class, 'posting_rule_id');
    }
}
