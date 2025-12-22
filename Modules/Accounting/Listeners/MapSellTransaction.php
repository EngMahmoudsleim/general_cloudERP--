<?php

namespace Modules\Accounting\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\SellCreatedOrModified;
use App\BusinessLocation;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Utils\AccountingValidator;
use Modules\Accounting\Utils\PostingEngine;

class MapSellTransaction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SellCreatedOrModified $event)
    {
        $util = new Util();
        if (!$util->isModuleEnabled('account') || !$util->isModuleEnabled('sales')) {
            return;
        }

        //get location setting and check if default is set or not, if set the proceed.
        $business_location = BusinessLocation::find($event->transaction->location_id);
        $accounting_default_map = json_decode($business_location->accounting_default_map, true);

        $deposit_to = isset($accounting_default_map['sale']['deposit_to']) ? $accounting_default_map['sale']['deposit_to'] : null;
        $payment_account = isset($accounting_default_map['sale']['payment_account']) ? $accounting_default_map['sale']['payment_account'] : null;

        //if purchase is deleted then delete the mapping
        if(isset($event->isDeleted) && $event->isDeleted){
            $accountingUtil = new \Modules\Accounting\Utils\AccountingUtil();
            $accountingUtil->deleteMap($event->transaction->id, null);
        } else {

            if(!is_null($deposit_to) && !is_null($payment_account)){

                $type = 'sell';
                $id = $event->transaction->id;
                $user_id = request()->session()->get('user.id');
                $business_id = $event->transaction->business_id;
                $context = [
                    'location_id' => $event->transaction->location_id,
                    'default_map' => $accounting_default_map['sale'] ?? [],
                    'transaction' => $event->transaction,
                ];
                
                $accountingUtil = new \Modules\Accounting\Utils\AccountingUtil();
                $accountingUtil->saveMap($type, $id, $user_id, $business_id, $deposit_to, $payment_account, null, $context);
            }
        }

        if (($event->transaction->status ?? null) !== 'final') {
            return;
        }

        $validator = new AccountingValidator();
        $strict = $validator->isStrictMode((int) $event->transaction->business_id);

        try {
            (new PostingEngine())->postSellInvoice($event->transaction);
        } catch (\Throwable $e) {
            if ($strict) {
                throw $e;
            }

            Log::warning('Accounting sell posting failed', [
                'business_id' => $event->transaction->business_id,
                'transaction_id' => $event->transaction->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
