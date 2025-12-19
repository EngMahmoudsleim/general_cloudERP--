@forelse($payments as $payment)
    <tr>
        <td>{{ @format_datetime($payment->paid_on) }}</td>
        <td><span class="badge badge-info">{{ $payment->payment_ref_no }}</span></td>
        <td>
            {{ $payment->contact_name }}
            @if(!empty($payment->supplier_business_name))
                <br><small class="text-muted">{{ $payment->supplier_business_name }}</small>
            @endif
        </td>
        <td>
            <span class="display_currency" data-orig-value="{{ $payment->amount }}" data-currency_symbol="true">
                {{ $payment->amount }}
            </span>
        </td>
        <td>{{ $payment_types[$payment->method] ?? $payment->method }}</td>
        <td>
            @if($payment->transaction_type == 'sell' && !empty($payment->invoice_no))
                {{ $payment->invoice_no }}
            @elseif($payment->transaction_type == 'purchase' && !empty($payment->ref_no))
                {{ $payment->ref_no }}
            @else
                -
            @endif
        </td>
        <td>
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-info view_payment" 
                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'viewPayment'], [$payment->id]) }}">
                    <i class="fas fa-eye"></i>
                </button>
                
                @if(auth()->user()->can('edit_sell_payment') || auth()->user()->can('edit_purchase_payment'))
                <button type="button" class="btn btn-xs btn-warning edit_payment" 
                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'edit'], [$payment->id]) }}">
                    <i class="fas fa-edit"></i>
                </button>
                @endif
                
                @if(auth()->user()->can('delete_sell_payment') || auth()->user()->can('delete_purchase_payment'))
                <button type="button" class="btn btn-xs btn-danger delete_payment" 
                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'destroy'], [$payment->id]) }}">
                    <i class="fas fa-trash"></i>
                </button>
                @endif
            </div>
        </td>
    </tr>
    
    {{-- عرض الدفعات الفرعية --}}
    @if($payment->child_payments->count() > 0)
        @foreach($payment->child_payments as $child)
            <tr class="bg-light">
                <td><small>{{ @format_datetime($child->paid_on) }}</small></td>
                <td><small class="badge badge-secondary">{{ $child->payment_ref_no }}</small></td>
                <td colspan="2"><small class="text-muted">دفعة فرعية من {{ $payment->payment_ref_no }}</small></td>
                <td colspan="3"></td>
            </tr>
        @endforeach
    @endif
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p>لا توجد سندات لعرضها</p>
        </td>
    </tr>
@endforelse