<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Required mapping matrix per operation
    |--------------------------------------------------------------------------
    | Keys correspond to accounting_default_map entries (e.g., payment_account, deposit_to).
    | The value labels are descriptive only for logs/errors.
    */
    'sell' => [
        'required_keys' => [
            'deposit_to' => 'AR',
            'payment_account' => 'REVENUE',
        ],
    ],
    'sell_payment' => [
        'required_keys' => [
            'deposit_to' => 'AR',
            'payment_account' => 'CASH/BANK',
        ],
    ],
    'purchase' => [
        'required_keys' => [
            'payment_account' => 'AP',
        ],
        'variants' => [
            'inventory' => [
                'required_keys' => [
                    'deposit_to' => 'INVENTORY',
                ],
            ],
            'expense' => [
                'required_keys' => [
                    'deposit_to' => 'EXPENSE',
                ],
            ],
        ],
    ],
    'purchase_payment' => [
        'required_keys' => [
            'deposit_to' => 'AP',
            'payment_account' => 'CASH/BANK',
        ],
    ],
    'expense' => [
        'required_keys' => [
            'deposit_to' => 'EXPENSE',
            'payment_account' => 'CASH/AP',
        ],
    ],
];
