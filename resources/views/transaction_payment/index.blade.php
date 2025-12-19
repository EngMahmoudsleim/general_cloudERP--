@extends('layouts.app')
@section('title', 'سندات القبض والدفع')

@section('content')
<section class="content-header">
    <h1>سندات القبض والدفع</h1>
</section>

<section class="content">
    {{-- الإحصائيات --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="total_receipts">0</h3>
                    <p>إجمالي القبض</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="total_payments">0</h3>
                    <p>إجمالي الدفع</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="today_receipts">0</h3>
                    <p>قبض اليوم</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="today_payments">0</h3>
                    <p>دفع اليوم</p>
                </div>
            </div>
        </div>
    </div>

    {{-- التابات --}}
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#receipt_tab" data-toggle="tab">سندات القبض</a></li>
            <li><a href="#payment_tab" data-toggle="tab">سندات الدفع</a></li>
        </ul>
        
        <div class="tab-content">
            {{-- تاب القبض --}}
            <div class="tab-pane active" id="receipt_tab">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>العميل:</label>
                        {!! Form::select('receipt_contact_id', $customer_contacts, null, [
                            'class' => 'form-control select2', 
                            'placeholder' => 'اختر عميل',
                            'id' => 'receipt_contact_id'
                        ]); !!}
                    </div>
                    <div class="col-md-2">
                        <label>من تاريخ:</label>
                        <input type="text" class="form-control datepicker" id="receipt_date_from" placeholder="من">
                    </div>
                    <div class="col-md-2">
                        <label>إلى تاريخ:</label>
                        <input type="text" class="form-control datepicker" id="receipt_date_to" placeholder="إلى">
                    </div>
                    <div class="col-md-3">
                        <label>طريقة الدفع:</label>
                        {!! Form::select('receipt_method', $payment_types, null, [
                            'class' => 'form-control',
                            'placeholder' => 'الكل',
                            'id' => 'receipt_method'
                        ]); !!}
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block" id="filter_receipts">
                            <i class="fa fa-filter"></i> تصفية
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">ملخص الحساب</h3>
                            </div>
                            <div class="box-body" id="receipt_summary">
                                <p class="text-center text-muted">اختر عميل لعرض الملخص</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">سندات القبض</h3>
                                <button class="btn btn-success btn-sm pull-right" id="add_receipt">
                                    <i class="fa fa-plus"></i> سند قبض جديد
                                </button>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>رقم السند</th>
                                                <th>العميل</th>
                                                <th>المبلغ</th>
                                                <th>الطريقة</th>
                                                <th>الفاتورة</th>
                                                <th>إجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="receipt_tbody">
                                            <tr>
                                                <td colspan="7" class="text-center">اختر عميل لعرض السندات</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray">
                                                <td colspan="3" class="text-right"><strong>الإجمالي:</strong></td>
                                                <td><strong><span id="receipt_total">0</span></strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- تاب الدفع - نفس التصميم --}}
            <div class="tab-pane" id="payment_tab">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>المورد:</label>
                        {!! Form::select('payment_contact_id', $supplier_contacts, null, [
                            'class' => 'form-control select2', 
                            'placeholder' => 'اختر مورد',
                            'id' => 'payment_contact_id'
                        ]); !!}
                    </div>
                    <div class="col-md-2">
                        <label>من تاريخ:</label>
                        <input type="text" class="form-control datepicker" id="payment_date_from" placeholder="من">
                    </div>
                    <div class="col-md-2">
                        <label>إلى تاريخ:</label>
                        <input type="text" class="form-control datepicker" id="payment_date_to" placeholder="إلى">
                    </div>
                    <div class="col-md-3">
                        <label>طريقة الدفع:</label>
                        {!! Form::select('payment_method', $payment_types, null, [
                            'class' => 'form-control',
                            'placeholder' => 'الكل',
                            'id' => 'payment_method'
                        ]); !!}
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block" id="filter_payments">
                            <i class="fa fa-filter"></i> تصفية
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="box box-warning">
                            <div class="box-header">
                                <h3 class="box-title">ملخص الحساب</h3>
                            </div>
                            <div class="box-body" id="payment_summary">
                                <p class="text-center text-muted">اختر مورد لعرض الملخص</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="box box-danger">
                            <div class="box-header">
                                <h3 class="box-title">سندات الدفع</h3>
                                <button class="btn btn-danger btn-sm pull-right" id="add_payment">
                                    <i class="fa fa-plus"></i> سند دفع جديد
                                </button>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>رقم السند</th>
                                                <th>المورد</th>
                                                <th>المبلغ</th>
                                                <th>الطريقة</th>
                                                <th>الفاتورة</th>
                                                <th>إجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payment_tbody">
                                            <tr>
                                                <td colspan="7" class="text-center">اختر مورد لعرض السندات</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray">
                                                <td colspan="3" class="text-right"><strong>الإجمالي:</strong></td>
                                                <td><strong><span id="payment_total">0</span></strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade payment_modal"></div>
<div class="modal fade view_modal"></div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize
    $('.select2').select2();
    $('.datepicker').datepicker({
        autoclose: true,
        format: moment_date_format
    });

    // Load stats
    function loadStats() {
        $.get('/payments/dashboard-stats', function(data) {
            if (data.success) {
                $('#total_receipts').text(data.total_receipts);
                $('#total_payments').text(data.total_payments);
                $('#today_receipts').text(data.today_receipts);
                $('#today_payments').text(data.today_payments);
            }
        });
    }

    // Load payments
    function loadPayments(type) {
        const contactId = $('#' + type + '_contact_id').val();
        const dateFrom = $('#' + type + '_date_from').val();
        const dateTo = $('#' + type + '_date_to').val();
        const method = $('#' + type + '_method').val();

        if (!contactId) {
            $('#' + type + '_tbody').html('<tr><td colspan="7" class="text-center">اختر ' + (type === 'receipt' ? 'عميل' : 'مورد') + ' لعرض السندات</td></tr>');
            return;
        }

        $('#' + type + '_tbody').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> جاري التحميل...</td></tr>');

        $.get('/payments/get-payments', {
            voucher_type: type,
            contact_id: contactId,
            date_from: dateFrom,
            date_to: dateTo,
            method: method
        }, function(data) {
            if (data.success) {
                $('#' + type + '_tbody').html(data.html);
                $('#' + type + '_total').text(data.total);
                __currency_convert_recursively($('#' + type + '_tbody'));
            } else {
                toastr.error(data.msg);
            }
        });
    }

    // Load contact summary
    function loadSummary(contactId, type) {
        if (!contactId) {
            $('#' + type + '_summary').html('<p class="text-center text-muted">اختر ' + (type === 'receipt' ? 'عميل' : 'مورد') + ' لعرض الملخص</p>');
            return;
        }

        $.get('/payments/contact-summary/' + contactId, {type: type === 'receipt' ? 'sell' : 'purchase'}, function(data) {
            const html = `
                <dl class="dl-horizontal">
                    <dt>الاسم:</dt><dd>${data.name}</dd>
                    <dt>الإجمالي:</dt><dd>${data.total}</dd>
                    <dt>المدفوع:</dt><dd class="text-success">${data.paid}</dd>
                    <dt>المتبقي:</dt><dd class="text-danger">${data.due}</dd>
                    ${data.opening_balance_due ? `<dt>رصيد افتتاحي:</dt><dd>${data.opening_balance_due}</dd>` : ''}
                </dl>
            `;
            $('#' + type + '_summary').html(html);
        });
    }

    // Events
    $('#receipt_contact_id').change(function() {
        loadPayments('receipt');
        loadSummary($(this).val(), 'receipt');
    });

    $('#payment_contact_id').change(function() {
        loadPayments('payment');
        loadSummary($(this).val(), 'payment');
    });

    $('#filter_receipts').click(() => loadPayments('receipt'));
    $('#filter_payments').click(() => loadPayments('payment'));

    $('#add_receipt').click(function() {
        const id = $('#receipt_contact_id').val();
        if (!id) return toastr.error('اختر عميل أولاً');
        window.location.href = '/payments/pay-contact-due/' + id + '?type=sell';
    });

    $('#add_payment').click(function() {
        const id = $('#payment_contact_id').val();
        if (!id) return toastr.error('اختر مورد أولاً');
        window.location.href = '/payments/pay-contact-due/' + id + '?type=purchase';
    });

    // Modal events
    $(document).on('click', '.view_payment, .edit_payment', function(e) {
        e.preventDefault();
        $.get($(this).data('href'), function(result) {
            ($(e.target).hasClass('view_payment') ? $('.view_modal') : $('.payment_modal')).html(result).modal('show');
        });
    });

    $(document).on('click', '.delete_payment', function(e) {
        e.preventDefault();
        const url = $(this).data('href');
        swal({title: LANG.sure, icon: "warning", buttons: true, dangerMode: true})
        .then((ok) => {
            if (ok) {
                $.ajax({url, method: 'DELETE', data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    success: (r) => {
                        if (r.success) {
                            toastr.success(r.msg);
                            loadPayments('receipt');
                            loadPayments('payment');
                            loadStats();
                        }
                    }
                });
            }
        });
    });

    loadStats();
});
</script>
@endsection