@extends('layouts.app')

@section('title', __('lang_v1.receipt_and_payment_vouchers'))

@section('content')
<section class="content no-print">
    <div class="row">
        <div class="col-md-8">
            <h3 class="mb-0">@lang('lang_v1.receipt_and_payment_vouchers')</h3>
            <p class="text-muted">@lang('lang_v1.voucher_center_description')</p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group" aria-label="Voucher type">
                <a href="{{ action([\App\Http\Controllers\ContactController::class, 'vouchers'], ['type' => 'customer']) }}" class="btn @if($type == 'customer') btn-primary @else btn-default @endif">
                    @lang('lang_v1.receipt_voucher')
                </a>
                <a href="{{ action([\App\Http\Controllers\ContactController::class, 'vouchers'], ['type' => 'supplier']) }}" class="btn @if($type == 'supplier') btn-primary @else btn-default @endif">
                    @lang('lang_v1.payment_voucher')
                </a>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('voucher_contact_id', $type == 'customer' ? __('report.customer') : __('report.supplier')) !!}
                {!! Form::select('voucher_contact_id', $contacts, $selected_contact_id, ['class' => 'form-control select2', 'id' => 'voucher_contact_id', 'placeholder' => __('messages.please_select')]) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="voucher-summary">
                @includeWhen(!empty($contact), 'contact.partials.voucher_summary', ['contact' => $contact, 'type' => $type])
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('sale.payments')</h3>
                </div>
                <div class="box-body" id="voucher-payments">
                    @if(!empty($payments))
                        @include('contact.partials.contact_payments_tab', ['payments' => $payments, 'payment_types' => $payment_types ?? []])
                    @else
                        <div class="text-center text-muted">@lang('messages.loading')</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    __select2($('#voucher_contact_id'));

    var voucherType = '{{ $type }}';
    var paymentsBaseUrl = '{{ url('/contacts/payments') }}';
    var summaryBaseUrl = '{{ url('/contacts/vouchers') }}';
    var payContactModal = $('.pay_contact_due_modal');

    function openVoucherPaymentModal(url) {
        if (!url) return;
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(result) {
                payContactModal
                    .html(result)
                    .modal('show');
                __currency_convert_recursively(payContactModal);
                payContactModal.find('#paid_on').datetimepicker({
                    format: moment_date_format + ' ' + moment_time_format,
                    ignoreReadonly: true,
                });
                payContactModal.find('form#pay_contact_due_form').validate();
            },
            error: function(xhr) {
                var message = LANG.something_went_wrong;
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            },
        });
    }

    function loadSummary(contactId) {
        if (!contactId) {
            $('#voucher-summary').empty();
            return;
        }
        $.ajax({
            url: summaryBaseUrl + '/' + contactId + '?type=' + voucherType,
            dataType: 'html',
            success: function(result) {
                $('#voucher-summary').html(result);
                __currency_convert_recursively($('#voucher-summary'));
            }
        });
    }

    function loadPayments(contactId, url) {
        var targetUrl = url || paymentsBaseUrl + '/' + contactId + '?type=' + voucherType;
        if (!contactId) {
            $('#voucher-payments').empty();
            return;
        }
        $.ajax({
            url: targetUrl,
            dataType: 'html',
            success: function(result) {
                $('#voucher-payments').html(result);
                __currency_convert_recursively($('#voucher-payments'));
            },
            error: function() {
                $('#voucher-payments').html('<div class="text-center text-danger">@lang('messages.something_went_wrong')</div>');
            }
        });
    }

    var initialContact = $('#voucher_contact_id').val();
    loadSummary(initialContact);
    if ($('#voucher-payments').find('table').length === 0) {
        loadPayments(initialContact);
    }

    $('#voucher_contact_id').on('change', function() {
        var contactId = $(this).val();
        loadSummary(contactId);
        loadPayments(contactId);
    });

    $(document).on('click', '#voucher-summary .add-voucher-payment', function(e) {
        e.preventDefault();
        var href = $(this).data('href') || $(this).attr('href');
        openVoucherPaymentModal(href);
    });

    $(document).on('click', '#voucher-payments #contact_payments_pagination a', function(e) {
        e.preventDefault();
        var contactId = $('#voucher_contact_id').val();
        loadPayments(contactId, $(this).attr('href'));
    });
});
</script>
@endsection