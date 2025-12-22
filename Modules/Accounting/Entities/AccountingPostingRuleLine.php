<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountingPostingRuleLine extends Model
{
    protected $guarded = [];

    public function rule()
    {
        return $this->belongsTo(AccountingPostingRule::class, 'posting_rule_id');
    }

    public function mappingKey()
    {
        return $this->belongsTo(AccountingMappingKey::class, 'mapping_key_id');
    }
}
