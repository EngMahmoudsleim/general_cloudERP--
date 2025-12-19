+30
-0

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $contact->name }}</h3>
        <div class="box-tools pull-right">
            @php
                $voucherHref = action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$contact->id]) . '?type=' . ($type === 'customer' ? 'sell' : 'purchase');
            @endphp
            <a href="{{ $voucherHref }}" class="btn btn-primary btn-sm add-voucher-payment" data-href="{{ $voucherHref }}">
                @if($type === 'customer')
                    @lang('lang_v1.add_receipt_voucher')
                @else
                    @lang('lang_v1.add_payment_voucher')
                @endif
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                @include('contact.contact_basic_info')
            </div>
            <div class="col-md-4">
                @include('contact.contact_more_info')
            </div>
            <div class="col-md-4">
                @include('contact.contact_payment_info')
            </div>
        </div>
    </div>
</div>