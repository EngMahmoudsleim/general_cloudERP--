@extends('layouts.app')
@section('title', __('expense.add_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('expense.add_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action([\App\Http\Controllers\ExpenseController::class, 'store']), 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">

				@if(count($business_locations) == 1)
					@php 
						$default_location = current(array_keys($business_locations->toArray())) 
					@endphp
				@else
					@php $default_location = null; @endphp
				@endif
				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
						{!! Form::select('expense_category_id', $expense_categories, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="form-group">
			            {!! Form::label('expense_sub_category_id', __('product.sub_category') . ':') !!}
			              {!! Form::select('expense_sub_category_id', [],  null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
			          </div>
				</div>
				
				<div class="clearfix"></div>
				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
						<p class="help-block">
			                @lang('lang_v1.leave_empty_to_autogenerate')
			            </p>
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
						</div>
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_for', __('expense.expense_for').':') !!} @show_tooltip(__('tooltip.expense_for'))
						{!! Form::select('expense_for', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				
				<div class="clearfix"></div>
				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!} 
						{!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				
				<div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                        @includeIf('components.document_help_text')</p></small>
                    </div>
                </div>
                
				<div class="col-md-4">
			    	<div class="form-group">
			            {!! Form::label('tax_id', __('product.applicable_tax') . ':' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('tax_id', $taxes['tax_rates'], null, ['class' => 'form-control'], $taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
							value="0">
			            </div>
			        </div>
			    </div>
			    
			    <div class="clearfix"></div>
			    
			    <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
						{!! Form::text('final_total', null, ['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']); !!}
					</div>
				</div>
				
				<div class="col-sm-8">
					<div class="form-group">
						{!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
						{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
				</div>
				
				<div class="clearfix"></div>
				
				<div class="col-md-4 col-sm-6">
					<br>
					<label>
		              {!! Form::checkbox('is_refund', 1, false, ['class' => 'input-icheck', 'id' => 'is_refund']); !!} @lang('lang_v1.is_refund')?
		            </label>@show_tooltip(__('lang_v1.is_refund_help'))
				</div>
			</div>
		</div>
	</div> <!--box end-->
	
	@include('expense.recur_expense_form_part')
	
	@component('components.widget', ['class' => 'box-solid', 'id' => "payment_rows_div", 'title' => __('purchase.add_payment')])
    
    <!-- Payment Method Selection -->
    <div class="payment-method-selection">
        <div class="row">
            <div class="col-sm-12">
                <h5><i class="fa fa-money"></i> @lang('lang_v1.select_payment_method'):</h5>
                <div class="btn-group btn-group-justified payment-type-selector" data-toggle="buttons">
                    <label class="btn btn-success active" data-payment-type="paid">
                        <input type="radio" name="payment_type" value="paid" checked>
                        <i class="fa fa-check-circle"></i> @lang('lang_v1.paid')
                    </label>
                    <label class="btn btn-warning" data-payment-type="partial">
                        <input type="radio" name="payment_type" value="partial">
                        <i class="fa fa-info-circle"></i> @lang('lang_v1.partial')
                    </label>
                    <label class="btn btn-danger" data-payment-type="due">
                        <input type="radio" name="payment_type" value="due">
                        <i class="fa fa-clock-o"></i> @lang('lang_v1.due')
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Explanation -->
    <div class="row payment-explanation">
        <div class="col-sm-12">
            <div class="alert alert-info">
                <div class="payment-status-info paid-status">
                    <h5><i class="fa fa-check-circle text-green"></i> @lang('lang_v1.paid'):</h5>
                    <p>@lang('lang_v1.paid_expense_explanation')</p>
                </div>
                <div class="payment-status-info partial-status" style="display: none;">
                    <h5><i class="fa fa-info-circle text-yellow"></i> @lang('lang_v1.partial'):</h5>
                    <p>@lang('lang_v1.partial_expense_explanation')</p>
                </div>
                <div class="payment-status-info due-status" style="display: none;">
                    <h5><i class="fa fa-clock-o text-red"></i> @lang('lang_v1.due'):</h5>
                    <p>@lang('lang_v1.due_expense_explanation')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Section -->
    <div class="payment-details-section">
        <div class="payment_row" id="paid_payment_section">
            <h5><i class="fa fa-credit-card"></i> @lang('lang_v1.payment_details'):</h5>
            
            <!-- Amount Information -->
            <div class="row amount-info">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@lang('sale.total_amount'):</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <input type="text" class="form-control" id="display_final_total" value="0.00" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@lang('lang_v1.amount_to_pay'):</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-credit-card"></i>
                            </span>
                            <input type="text" class="form-control input_number payment-amount-input" 
                                   id="payment_amount_input" value="0.00" data-original-value="0.00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Row Form (shown for paid and partial) -->
            <div id="payment_form_section">
                @include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true])
            </div>

            <!-- Due Date Section (shown for due and partial) -->
            <div class="due-date-section" id="due_date_section" style="display: none;">
                <div class="form-group">
                    <label for="due_date">@lang('lang_v1.payment_due_date'):</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('payment_due_date', null, ['class' => 'form-control', 'id' => 'payment_due_date', 'placeholder' => __('lang_v1.select_due_date')]); !!}
                    </div>
                    <small class="text-muted">@lang('lang_v1.due_date_help')</small>
                </div>
            </div>

            <!-- Payment Summary -->
            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <div class="payment-summary-card">
                        <div class="row">
                            <div class="col-sm-4 text-center">
                                <div class="summary-item">
                                    <h5><i class="fa fa-money"></i> @lang('sale.total_amount')</h5>
                                    <h3 class="text-primary" id="summary_total">0.00</h3>
                                </div>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="summary-item">
                                    <h5><i class="fa fa-credit-card"></i> @lang('lang_v1.paid_amount')</h5>
                                    <h3 class="text-green" id="summary_paid">0.00</h3>
                                </div>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="summary-item">
                                    <h5><i class="fa fa-clock-o"></i> @lang('purchase.payment_due')</h5>
                                    <h3 class="text-red" id="summary_due">0.00</h3>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Status Badge -->
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <div class="payment-status-badge">
                                    <span class="badge bg-green" id="payment_status_badge">
                                        <i class="fa fa-check-circle"></i> @lang('lang_v1.fully_paid')
                                    </span>
                                    <p class="payment-status-message text-muted" id="payment_status_message">
                                        @lang('lang_v1.fully_paid_message')
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recurring Expense Note -->
    <div class="row recurring-note">
        <div class="col-sm-12">
            <div class="alert alert-warning">
                <h5><i class="fa fa-repeat"></i> @lang('lang_v1.note_about_recurring'):</h5>
                <p>@lang('lang_v1.recurring_expense_note')</p>
                <ul>
                    <li>@lang('lang_v1.recurring_note_point1')</li>
                    <li>@lang('lang_v1.recurring_note_point2')</li>
                    <li>@lang('lang_v1.recurring_note_point3')</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endcomponent
	
	<div class="col-sm-12 text-center">
		<button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
	</div>
	
{!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		$('.paid_on').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });
        
        // Initialize due date picker
        $('#payment_due_date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true,
        });
        
        // Auto-calculate and update when total amount changes
        $('#final_total').on('keyup change', function() {
            var total = $(this).val();
            if (total) {
                var formatted = parseFloat(total).toFixed(2);
                $('#display_final_total').val(formatted);
                $('#summary_total').text(formatted);
                
                // Update payment amount based on selected payment type
                var selectedType = $('.payment-type-selector .btn.active').data('payment-type');
                updatePaymentAmount(selectedType);
                
                calculatePaymentSummary();
            }
        });
	});
	
	__page_leave_confirmation('#add_expense_form');
	
	// Payment Type Selection Logic
	$(document).ready(function() {
		// Payment type selection
		$('.payment-type-selector label').on('click', function() {
			var paymentType = $(this).data('payment-type');
			
			// Update active state
			$('.payment-type-selector label').removeClass('active');
			$(this).addClass('active');
			
			// Show/hide sections based on payment type
			updatePaymentSections(paymentType);
			
			// Update payment amount based on type
			updatePaymentAmount(paymentType);
			
			// Update explanation
			updatePaymentExplanation(paymentType);
			
			// Update summary
			calculatePaymentSummary();
		});
		
		// Payment amount input change
		$('#payment_amount_input').on('keyup change', function() {
			calculatePaymentSummary();
		});
		
		// Initialize
		calculatePaymentSummary();
	});
	
	function updatePaymentSections(paymentType) {
		// Reset all sections
		$('#payment_form_section').show();
		$('#due_date_section').hide();
		$('.payment-amount-input').prop('readonly', false);
		
		switch(paymentType) {
			case 'paid':
				// Show payment form, hide due date
				$('#payment_form_section').show();
				$('#due_date_section').hide();
				$('.payment-amount-input').prop('readonly', true); // Readonly because full amount
				$('#payment_amount_input').addClass('readonly-input');
				break;
				
			case 'partial':
				// Show both payment form and due date
				$('#payment_form_section').show();
				$('#due_date_section').show();
				$('.payment-amount-input').prop('readonly', false);
				$('#payment_amount_input').removeClass('readonly-input');
				break;
				
			case 'due':
				// Hide payment form, show due date
				$('#payment_form_section').hide();
				$('#due_date_section').show();
				$('.payment-amount-input').prop('readonly', true); // Readonly because no payment
				$('#payment_amount_input').addClass('readonly-input');
				break;
		}
	}
	
	function updatePaymentAmount(paymentType) {
		var totalAmount = parseFloat($('#final_total').val()) || 0;
		var paymentInput = $('#payment_amount_input');
		
		switch(paymentType) {
			case 'paid':
				// Set payment amount to total amount
				paymentInput.val(totalAmount.toFixed(2));
				paymentInput.data('original-value', totalAmount.toFixed(2));
				break;
				
			case 'partial':
				// Set payment amount to half of total (or keep current if already set)
				var currentVal = paymentInput.val();
				if (currentVal === '0.00' || currentVal === '') {
					paymentInput.val((totalAmount / 2).toFixed(2));
				}
				paymentInput.data('original-value', paymentInput.val());
				break;
				
			case 'due':
				// Set payment amount to 0
				paymentInput.val('0.00');
				paymentInput.data('original-value', '0.00');
				break;
		}
		
		// Update the actual payment amount in the payment form
		$('input.payment-amount').val(paymentInput.val());
	}
	
	function updatePaymentExplanation(paymentType) {
		// Hide all explanations first
		$('.payment-status-info').hide();
		
		// Show the selected one
		$('.' + paymentType + '-status').show();
	}
	
	function calculatePaymentSummary() {
		var totalAmount = parseFloat($('#final_total').val()) || 0;
		var paymentAmount = parseFloat($('#payment_amount_input').val()) || 0;
		var dueAmount = totalAmount - paymentAmount;
		
		// Ensure payment amount doesn't exceed total amount
		if (paymentAmount > totalAmount) {
			paymentAmount = totalAmount;
			$('#payment_amount_input').val(paymentAmount.toFixed(2));
		}
		
		// Update summary display
		$('#summary_total').text(totalAmount.toFixed(2));
		$('#summary_paid').text(paymentAmount.toFixed(2));
		$('#summary_due').text(dueAmount.toFixed(2));
		
		// Update actual payment amount in the payment form
		$('input.payment-amount').val(paymentAmount.toFixed(2));
		
		// Update payment status badge and message
		updatePaymentStatusBadge(paymentAmount, dueAmount);
		
		// Update the payment due display
		$('#payment_due').text(__currency_trans_from_en(dueAmount, true, false));
	}
	
	function updatePaymentStatusBadge(paymentAmount, dueAmount) {
		var badge = $('#payment_status_badge');
		var message = $('#payment_status_message');
		
		badge.removeClass('bg-green bg-yellow bg-red');
		message.removeClass('text-green text-yellow text-red');
		
		if (dueAmount === 0) {
			// Fully paid
			badge.addClass('bg-green').html('<i class="fa fa-check-circle"></i> @lang("lang_v1.fully_paid")');
			message.addClass('text-green').text('@lang("lang_v1.fully_paid_message")');
		} else if (paymentAmount === 0) {
			// Not paid
			badge.addClass('bg-red').html('<i class="fa fa-clock-o"></i> @lang("lang_v1.due")');
			message.addClass('text-red').text('@lang("lang_v1.not_paid_message")');
		} else {
			// Partially paid
			badge.addClass('bg-yellow').html('<i class="fa fa-info-circle"></i> @lang("lang_v1.partial")');
			message.addClass('text-yellow').text('@lang("lang_v1.partially_paid_message")');
		}
	}

	$(document).on('change', 'input#final_total, input.payment-amount', function() {
		calculatePaymentSummary();
	});

	$(document).on('change', '#recur_interval_type', function() {
	    if ($(this).val() == 'months') {
	        $('.recur_repeat_on_div').removeClass('hide');
	    } else {
	        $('.recur_repeat_on_div').addClass('hide');
	    }
	});

	$('#is_refund').on('ifChecked', function(event){
		$('#recur_expense_div').addClass('hide');
		$('.recurring-note').addClass('hide');
		// Disable payment type selection for refunds
		$('.payment-type-selector label').addClass('disabled');
		$('.payment-type-selector input').prop('disabled', true);
		$('.payment-details-section').addClass('disabled-section');
	});
	
	$('#is_refund').on('ifUnchecked', function(event){
		$('#recur_expense_div').removeClass('hide');
		$('.recurring-note').removeClass('hide');
		// Enable payment type selection
		$('.payment-type-selector label').removeClass('disabled');
		$('.payment-type-selector input').prop('disabled', false);
		$('.payment-details-section').removeClass('disabled-section');
	});

	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
	    var default_accounts = $('select#location_id').length ? 
	                $('select#location_id')
	                .find(':selected')
	                .data('default_payment_accounts') : [];
	    var payment_types_dropdown = $('.payment_types_dropdown');
	    var payment_type = payment_types_dropdown.val();
	    if (payment_type) {
	        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
	            default_accounts[payment_type]['account'] : '';
	        var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
	        if (account_dropdown.length && default_accounts) {
	            account_dropdown.val(default_account);
	            account_dropdown.change();
	        }
	    }
	});
</script>

<style>
/* Payment Type Selector */
.payment-method-selection {
    margin-bottom: 20px;
}

.payment-method-selection h5 {
    margin-bottom: 10px;
    font-weight: bold;
    color: #333;
}

.payment-type-selector .btn {
    padding: 15px 5px;
    font-size: 14px;
    font-weight: bold;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.payment-type-selector .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.payment-type-selector .btn.active {
    border-color: #333 !important;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    transform: scale(1.02);
}

.payment-type-selector .btn-success {
    background-color: #00a65a;
    border-color: #008d4c;
}

.payment-type-selector .btn-success:hover {
    background-color: #008d4c;
}

.payment-type-selector .btn-warning {
    background-color: #f39c12;
    border-color: #e08e0b;
}

.payment-type-selector .btn-warning:hover {
    background-color: #e08e0b;
}

.payment-type-selector .btn-danger {
    background-color: #dd4b39;
    border-color: #d73925;
}

.payment-type-selector .btn-danger:hover {
    background-color: #d73925;
}

.payment-type-selector .btn i {
    margin-right: 5px;
    font-size: 16px;
}

.payment-type-selector .btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Payment Explanation */
.payment-explanation {
    margin-bottom: 20px;
}

.payment-explanation .alert {
    border-left: 4px solid #3c8dbc;
    background-color: #f5f5f5;
    border-radius: 4px;
}

.payment-explanation h5 {
    margin-top: 0;
    font-weight: bold;
    margin-bottom: 10px;
}

.payment-explanation p {
    margin-bottom: 0;
    line-height: 1.6;
}

.text-green { color: #00a65a !important; }
.text-yellow { color: #f39c12 !important; }
.text-red { color: #dd4b39 !important; }

/* Payment Details Section */
.payment-details-section h5 {
    margin-bottom: 20px;
    font-weight: bold;
    color: #333;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.amount-info {
    margin-bottom: 20px;
}

.amount-info .form-group {
    margin-bottom: 15px;
}

.amount-info .form-group label {
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
}

.amount-info .input-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.amount-info .input-group-addon {
    background-color: #f8f9fa;
    border-color: #d2d6de;
    min-width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.amount-info .input-group-addon i {
    color: #666;
}

.amount-info .form-control {
    border-color: #d2d6de;
}

.amount-info .form-control:focus {
    border-color: #3c8dbc;
    box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
}

.amount-info .readonly-input {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

/* Due Date Section */
.due-date-section {
    margin-top: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 4px;
    border: 1px solid #eee;
}

.due-date-section .form-group label {
    font-weight: bold;
    margin-bottom: 8px;
}

.due-date-section .input-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.due-date-section .input-group-addon {
    background-color: #f8f9fa;
    border-color: #d2d6de;
}

.due-date-section .text-muted {
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

/* Payment Summary Card */
.payment-summary-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.summary-item {
    padding: 15px;
}

.summary-item h5 {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
    font-weight: 600;
}

.summary-item h5 i {
    margin-right: 8px;
}

.summary-item h3 {
    margin: 0;
    font-weight: bold;
    font-size: 28px;
}

.text-primary { color: #3c8dbc !important; }
.text-green { color: #00a65a !important; }
.text-red { color: #dd4b39 !important; }

/* Payment Status Badge */
.payment-status-badge {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.payment-status-badge .badge {
    font-size: 16px;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.bg-green { background-color: #00a65a !important; }
.bg-yellow { background-color: #f39c12 !important; }
.bg-red { background-color: #dd4b39 !important; }

.payment-status-badge .badge i {
    margin-right: 8px;
}

.payment-status-message {
    margin-top: 10px;
    font-size: 14px;
    font-style: italic;
}

/* Recurring Note */
.recurring-note {
    margin-top: 25px;
}

.recurring-note .alert {
    border-left: 4px solid #f39c12;
    background-color: #fff8e1;
    border-radius: 4px;
}

.recurring-note h5 {
    margin-top: 0;
    font-weight: bold;
    color: #32325d;
    margin-bottom: 10px;
}

.recurring-note h5 i {
    margin-right: 8px;
}

.recurring-note p {
    margin-bottom: 10px;
    line-height: 1.6;
}

.recurring-note ul {
    margin-bottom: 0;
    padding-left: 20px;
}

.recurring-note li {
    margin-bottom: 5px;
    line-height: 1.5;
}

/* Disabled State for Refunds */
.disabled-section {
    opacity: 0.6;
    pointer-events: none;
}

.disabled-section .form-control {
    background-color: #f5f5f5;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .payment-type-selector .btn {
        padding: 12px 5px;
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .summary-item h3 {
        font-size: 22px;
    }
    
    .payment-summary-card {
        padding: 15px;
    }
    
    .payment-status-badge .badge {
        font-size: 14px;
        padding: 10px 20px;
    }
    
    .amount-info .col-sm-6 {
        margin-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .payment-type-selector {
        display: flex;
        flex-direction: column;
    }
    
    .payment-type-selector .btn {
        margin-bottom: 10px;
        width: 100%;
    }
    
    .summary-item {
        margin-bottom: 15px;
    }
    
    .summary-item h3 {
        font-size: 20px;
    }
}
</style>
@endsection