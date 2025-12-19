@extends('layouts.app')

@section('title', __('messages.settings'))

@section('content')

@include('accounting::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'messages.settings' )</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#account_setting" data-toggle="tab" aria-expanded="true">
							@lang('accounting::lang.account_setting') / @lang('accounting::lang.map_transactions')
						</a>
					</li>

					<li>
						<a href="#sub_type_tab" data-toggle="tab" aria-expanded="true">
							@lang('accounting::lang.account_sub_type')
						</a>
					</li>
					<li>
						<a href="#detail_type_tab" data-toggle="tab" aria-expanded="true">
							@lang('accounting::lang.detail_type')
						</a>
					</li>
				</ul>
				<div class="tab-content">

					<div class="tab-pane active" id="account_setting">
						{!! Form::open(['action' => '\Modules\Accounting\Http\Controllers\SettingsController@saveSettings',
						'method' => 'post']) !!}
						<div class="row mb-12">
							<div class="col-md-4">
								<button type="button" class="tw-dw-btn tw-dw-btn-error tw-text-white tw-dw-btn-sm accounting_reset_data" data-href="{{action([\Modules\Accounting\Http\Controllers\SettingsController::class, 'resetData'])}}">
									@lang('accounting::lang.reset_data')
								</button>
							</div>
						</div>
						<br>

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									{!! Form::label('journal_entry_prefix', __('accounting::lang.journal_entry_prefix') . ':') !!}
									{!! Form::text('journal_entry_prefix',!empty($accounting_settings['journal_entry_prefix'])?
									$accounting_settings['journal_entry_prefix'] : '',
									['class' => 'form-control ', 'id' => 'journal_entry_prefix']); !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									{!! Form::label('transfer_prefix', __('accounting::lang.transfer_prefix') . ':') !!}
									{!! Form::text('transfer_prefix',!empty($accounting_settings['transfer_prefix'])?
									$accounting_settings['transfer_prefix'] : '',
									['class' => 'form-control ', 'id' => 'transfer_prefix']); !!}
								</div>
							</div>
						</div>

						<hr />

						{{-- 💡 تلميح للمستخدم --}}
						<div class="alert alert-info">
							<h4><i class="icon fas fa-info-circle"></i> @lang('accounting::lang.account_mapping_hint_title')</h4>
							<p>@lang('accounting::lang.account_mapping_hint_description')</p>
							<button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#recommended-accounts-guide">
								<i class="fas fa-book"></i> @lang('accounting::lang.show_recommended_accounts')
							</button>
						</div>

						{{-- 📚 دليل الحسابات الموصى بها --}}
						<div id="recommended-accounts-guide" class="collapse" style="margin-bottom: 20px;">
							<div class="box box-solid box-success">
								<div class="box-header with-border">
									<h3 class="box-title"><i class="fas fa-lightbulb"></i> @lang('accounting::lang.recommended_accounts_guide')</h3>
								</div>
								<div class="box-body">
									<div class="row">
										<div class="col-md-6">
											<h4 class="text-primary"><i class="fas fa-shopping-cart"></i> @lang('sale.sale')</h4>
											<ul>
												<li><strong>@lang('accounting::lang.payment_account'):</strong> <span class="label label-info">Sales</span> أو <span class="label label-info">Revenue - General</span></li>
												<li><strong>@lang('accounting::lang.deposit_to'):</strong> <span class="label label-success">Accounts Receivable (A/R)</span></li>
											</ul>
											
											<h4 class="text-primary"><i class="fas fa-money-bill-wave"></i> @lang('accounting::lang.sales_payments')</h4>
											<ul>
												<li><strong>@lang('accounting::lang.payment_account'):</strong> <span class="label label-info">Accounts Receivable (A/R)</span></li>
												<li><strong>@lang('accounting::lang.deposit_to'):</strong> <span class="label label-success">Cash and cash equivalents</span> أو <span class="label label-success">Undeposited Funds</span></li>
											</ul>

											<h4 class="text-warning"><i class="fas fa-box"></i> @lang('purchase.purchases')</h4>
											<ul>
												<li><strong>@lang('accounting::lang.payment_account'):</strong> <span class="label label-warning">Purchases</span> أو <span class="label label-warning">Cost of sales</span></li>
												<li><strong>@lang('accounting::lang.deposit_to'):</strong> <span class="label label-danger">Accounts Payable (A/P)</span></li>
											</ul>
										</div>

										<div class="col-md-6">
											<h4 class="text-warning"><i class="fas fa-hand-holding-usd"></i> @lang('accounting::lang.purchase_payments')</h4>
											<ul>
												<li><strong>@lang('accounting::lang.payment_account'):</strong> <span class="label label-danger">Accounts Payable (A/P)</span></li>
												<li><strong>@lang('accounting::lang.deposit_to'):</strong> <span class="label label-success">Cash and cash equivalents</span></li>
											</ul>

											<h4 class="text-danger"><i class="fas fa-file-invoice-dollar"></i> @lang('accounting::lang.expenses')</h4>
											<ul>
												<li><strong>@lang('accounting::lang.payment_account'):</strong> 
													<ul style="margin-top: 5px;">
														<li><span class="label label-default">Office expenses</span> (مصروفات مكتبية)</li>
														<li><span class="label label-default">Rent or lease payments</span> (إيجار)</li>
														<li><span class="label label-default">Utilities</span> (مرافق)</li>
														<li><span class="label label-default">Payroll Expenses</span> (رواتب)</li>
														<li>أو أي حساب مصروف آخر حسب النوع</li>
													</ul>
												</li>
												<li><strong>@lang('accounting::lang.deposit_to'):</strong> <span class="label label-success">Cash and cash equivalents</span></li>
											</ul>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="callout callout-warning">
												<h4><i class="fas fa-exclamation-triangle"></i> @lang('accounting::lang.important_note')</h4>
												<p>@lang('accounting::lang.mapping_note_1')</p>
												<p>@lang('accounting::lang.mapping_note_2')</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<h3>@lang('accounting::lang.map_transactions') @show_tooltip(__('accounting::lang.map_transactions_help'))</h3>

						@foreach($business_locations as $business_location)
						@component('components.widget', ['title' => $business_location->name])

						@php
						$default_map = json_decode($business_location->accounting_default_map, true);

						$sale_payment_account = isset($default_map['sale']['payment_account']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['sale']['payment_account']) : null;
						$sale_deposit_to = isset($default_map['sale']['deposit_to']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['sale']['deposit_to']) : null;
						$sales_payments_payment_account = isset($default_map['sell_payment']['payment_account']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['sell_payment']['payment_account']) : null;
						$sales_payments_deposit_to = isset($default_map['sell_payment']['deposit_to']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['sell_payment']['deposit_to']) : null;
						$purchases_payment_account = isset($default_map['purchases']['payment_account']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['purchases']['payment_account']) : null;
						$purchases_deposit_to = isset($default_map['purchases']['deposit_to']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['purchases']['deposit_to']) : null;
						$purchase_payments_payment_account = isset($default_map['purchase_payment']['payment_account']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['purchase_payment']['payment_account']) : null;
						$purchase_payments_deposit_to = isset($default_map['purchase_payment']['deposit_to']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['purchase_payment']['deposit_to']) : null;
						$expense_payment_account = isset($default_map['expense']['payment_account']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['expense']['payment_account']) : null;
						$expense_deposit_to = isset($default_map['expense']['deposit_to']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['expense']['deposit_to']) : null;
						@endphp

						{{-- 💰 المبيعات --}}
						<div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
							<strong><i class="fas fa-shopping-cart text-primary"></i> @lang('sale.sale')</strong>
							<small class="text-muted pull-right">
								<i class="fas fa-info-circle"></i> 
								@lang('accounting::lang.recommended'): Sales → Accounts Receivable (A/R)
							</small>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('payment_account', __('accounting::lang.payment_account') . ':' ) !!}
										{!! Form::select('payment_account', !is_null($sale_payment_account) ? [$sale_payment_account->id => $sale_payment_account->name] : [], $sale_payment_account->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.payment_account'), 'name' => "accounting_default_map[$business_location->id][sale][payment_account]",
										'id' => $business_location->id . 'sale_payment_account']); !!}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('deposit_to', __('accounting::lang.deposit_to') . ':' ) !!}
										{!! Form::select('deposit_to', !is_null($sale_deposit_to) ?
										[$sale_deposit_to->id => $sale_deposit_to->name] : [], $sale_deposit_to->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.deposit_to'), 'name' => "accounting_default_map[$business_location->id][sale][deposit_to]",
										'id' => $business_location->id . '_sale_deposit_to']); !!}
									</div>
								</div>
							</div>
						</div>

						{{-- 💵 دفعات المبيعات --}}
						<div style="background-color: #f3e5f5; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
							<strong><i class="fas fa-money-bill-wave text-purple"></i> @lang('accounting::lang.sales_payments')</strong>
							<small class="text-muted pull-right">
								<i class="fas fa-info-circle"></i> 
								@lang('accounting::lang.recommended'): A/R → Cash
							</small>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('payment_account', __('accounting::lang.payment_account') . ':' ) !!}
										{!! Form::select('payment_account', !is_null($sales_payments_payment_account) ? [$sales_payments_payment_account->id => $sales_payments_payment_account->name] : [], $sales_payments_payment_account->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.payment_account'), 'name' => "accounting_default_map[$business_location->id][sell_payment][payment_account]", 'id' => $business_location->id . 'sales_payments_payment_account']); !!}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('deposit_to', __('accounting::lang.deposit_to') . ':' ) !!}
										{!! Form::select('deposit_to', !is_null($sales_payments_deposit_to) ?
										[$sales_payments_deposit_to->id => $sales_payments_deposit_to->name] : [], $sales_payments_deposit_to->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.deposit_to'), 'name' => "accounting_default_map[$business_location->id][sell_payment][deposit_to]",
										'id' => $business_location->id . 'sales_payments_deposit_to']); !!}
									</div>
								</div>
							</div>
						</div>

						{{-- 📦 المشتريات --}}
						<div style="background-color: #fff3e0; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
							<strong><i class="fas fa-box text-warning"></i> @lang('purchase.purchases')</strong>
							<small class="text-muted pull-right">
								<i class="fas fa-info-circle"></i> 
								@lang('accounting::lang.recommended'): Purchases → Accounts Payable (A/P)
							</small>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('payment_account', __('accounting::lang.payment_account') . ':' ) !!}
										{!! Form::select('payment_account', !is_null($purchases_payment_account) ? [$purchases_payment_account->id => $purchases_payment_account->name] : [], $purchases_payment_account->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.payment_account'), 'name' => "accounting_default_map[$business_location->id][purchases][payment_account]",
										'id' => $business_location->id . 'purchases_payment_account']); !!}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('deposit_to', __('accounting::lang.deposit_to') . ':' ) !!}
										{!! Form::select('deposit_to', !is_null($purchases_deposit_to) ?
										[$purchases_deposit_to->id => $purchases_deposit_to->name] : [], $purchases_deposit_to->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.deposit_to'), 'name' => "accounting_default_map[$business_location->id][purchases][deposit_to]",
										'id' => $business_location->id . '_purchases_deposit_to']); !!}
									</div>
								</div>
							</div>
						</div>

						{{-- 💸 دفعات المشتريات --}}
						<div style="background-color: #ffebee; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
							<strong><i class="fas fa-hand-holding-usd text-danger"></i> @lang('accounting::lang.purchase_payments')</strong>
							<small class="text-muted pull-right">
								<i class="fas fa-info-circle"></i> 
								@lang('accounting::lang.recommended'): A/P → Cash
							</small>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('payment_account', __('accounting::lang.payment_account') . ':' ) !!}
										{!! Form::select('payment_account', !is_null($purchase_payments_payment_account) ? [$purchase_payments_payment_account->id => $purchase_payments_payment_account->name] : [], $purchase_payments_payment_account->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.payment_account'), 'name' => "accounting_default_map[$business_location->id][purchase_payment][payment_account]",
										'id' => $business_location->id . 'purchase_payments_payment_account']); !!}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('deposit_to', __('accounting::lang.deposit_to') . ':' ) !!}
										{!! Form::select('deposit_to', !is_null($purchase_payments_deposit_to) ?
										[$purchase_payments_deposit_to->id => $purchase_payments_deposit_to->name] : [], $purchase_payments_deposit_to->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.deposit_to'), 'name' => "accounting_default_map[$business_location->id][purchase_payment][deposit_to]",
										'id' => $business_location->id . '_purchase_payments_deposit_to']); !!}
									</div>
								</div>
							</div>
						</div>

						{{-- 📉 المصروفات --}}
						<div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
							<strong><i class="fas fa-file-invoice-dollar text-success"></i> @lang('accounting::lang.expenses')</strong>
							<small class="text-muted pull-right">
								<i class="fas fa-info-circle"></i> 
								@lang('accounting::lang.recommended'): Expense Account → Cash
							</small>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('payment_account', __('accounting::lang.payment_account') . ':' ) !!}
										{!! Form::select('payment_account', !is_null($expense_payment_account) ? [$expense_payment_account->id => $expense_payment_account->name] : [], $expense_payment_account->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.payment_account'), 'name' => "accounting_default_map[$business_location->id][expense][payment_account]",
										'id' => $business_location->id . 'expense_payment_account']); !!}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('deposit_to', __('accounting::lang.deposit_to') . ':' ) !!}
										{!! Form::select('deposit_to', !is_null($expense_deposit_to) ?
										[$expense_deposit_to->id => $expense_deposit_to->name] : [], $expense_deposit_to->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.deposit_to'), 'name' => "accounting_default_map[$business_location->id][expense][deposit_to]",
										'id' => $business_location->id . '_expense_deposit_to']); !!}
									</div>
								</div>
							</div>
	
							{{-- تصنيفات المصروفات --}}
							@foreach ($expence_categories as $expence_category)
							@php
								$dynamic_variable_payment_account = isset($default_map['expense_'.$expence_category->id]['payment_account']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['expense_'.$expence_category->id]['payment_account']) : null;
								$dynamic_variable_deposit_to = isset($default_map['expense_'.$expence_category->id]['deposit_to']) ? \Modules\Accounting\Entities\AccountingAccount::find($default_map['expense_'.$expence_category->id]['deposit_to']) : null;
							@endphp
							<div style="border-top: 1px dashed #ccc; margin-top: 10px; padding-top: 10px;">
								<strong style="color: #555;">
									<i class="fas fa-tag"></i> {{ $expence_category->name }}
								</strong>
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-6"> 
										<div class="form-group">
											{!! Form::label('payment_account', __('accounting::lang.payment_account') . ':' ) !!}
											{!! Form::select('payment_account', !is_null($dynamic_variable_payment_account) ? [$dynamic_variable_payment_account->id => $dynamic_variable_payment_account->name] : [], $dynamic_variable_payment_account->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.payment_account'), 'name' => "accounting_default_map[$business_location->id][expense_$expence_category->id][payment_account]", 'id' => $business_location->id . 'expense_'.$expence_category->id .'_payment_account']); !!}
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											{!! Form::label('deposit_to', __('accounting::lang.deposit_to') . ':' ) !!}
											{!! Form::select('deposit_to', !is_null($dynamic_variable_deposit_to) ?
											[$dynamic_variable_deposit_to->id => $dynamic_variable_deposit_to->name] : [], $dynamic_variable_deposit_to->id ?? null, ['class' => 'form-control accounts-dropdown width-100','placeholder' => __('accounting::lang.deposit_to'), 'name' => "accounting_default_map[$business_location->id][expense_$expence_category->id][deposit_to]",
											'id' => $business_location->id . '_expense_'.$expence_category->id.'_deposit_to']); !!}
										</div>
									</div>
								</div>
							</div>
							@endforeach
						</div>

						@endcomponent
						@endforeach

						<div class="row">
							<div class="col-md-12 text-center">
								<div class="form-group">
									{{Form::submit(__('messages.update'), ['class'=>"tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg"])}}
								</div>
							</div>
						</div>
						{!! Form::close() !!}
					</div>

					{{-- باقي التابات --}}
					<div class="tab-pane" id="sub_type_tab">
						{{-- ... الكود الحالي --}}
					</div>
					<div class="tab-pane" id="detail_type_tab">
						{{-- ... الكود الحالي --}}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@include('accounting::account_type.create')
<div class="modal fade" id="edit_account_type_modal" tabindex="-1" role="dialog">
</div>
@stop
@section('javascript')

@include('accounting::accounting.common_js')

<script type="text/javascript">
	$(document).ready(function() {
		account_sub_type_table = $('#account_sub_type_table').DataTable({
			processing: true,
			serverSide: true,
			ajax: "{{action([\Modules\Accounting\Http\Controllers\AccountTypeController::class, 'index'])}}?account_type=sub_type",
			columnDefs: [{
				targets: [2],
				orderable: false,
				searchable: false,
			}, ],
			columns: [{
					data: 'name',
					name: 'name'
				},
				{
					data: 'account_primary_type',
					name: 'account_primary_type'
				},
				{
					data: 'action',
					name: 'action'
				},
			],
		});

		detail_type_table = $('#detail_type_table').DataTable({
			processing: true,
			serverSide: true,
			ajax: "{{action([\Modules\Accounting\Http\Controllers\AccountTypeController::class, 'index'])}}?account_type=detail_type",
			columnDefs: [{
				targets: 3,
				orderable: false,
				searchable: false,
			}, ],
			columns: [{
					data: 'name',
					name: 'name'
				},
				{
					data: 'parent_type',
					name: 'parent_type'
				},
				{
					data: 'description',
					name: 'description'
				},
				{
					data: 'action',
					name: 'action'
				},
			],
		});

		$('#add_account_sub_type').click(function() {
			$('#account_type').val('sub_type')
			$('#account_type_title').text("{{__('accounting::lang.add_account_sub_type')}}");
			$('#description_div').addClass('hide');
			$('#parent_id_div').addClass('hide');
			$('#account_type_div').removeClass('hide');
			$('#create_account_type_modal').modal('show');
		});

		$('#add_detail_type').click(function() {
			$('#account_type').val('detail_type')
			$('#account_type_title').text("{{__('accounting::lang.add_detail_type')}}");
			$('#description_div').removeClass('hide');
			$('#parent_id_div').removeClass('hide');
			$('#account_type_div').addClass('hide');
			$('#create_account_type_modal').modal('show');
		})
	});
	$(document).on('hidden.bs.modal', '#create_account_type_modal', function(e) {
		$('#create_account_type_form')[0].reset();
	})
	$(document).on('submit', 'form#create_account_type_form', function(e) {
		e.preventDefault();
		var form = $(this);
		var data = form.serialize();

		$.ajax({
			method: 'POST',
			url: $(this).attr('action'),
			dataType: 'json',
			data: data,
			success: function(result) {
				if (result.success == true) {
					$('#create_account_type_modal').modal('hide');
					toastr.success(result.msg);
					if (result.data.account_type == 'sub_type') {
						account_sub_type_table.ajax.reload();
					} else {
						detail_type_table.ajax.reload();
					}
					$('#create_account_type_form').find('button[type="submit"]').attr('disabled', false);
				} else {
					toastr.error(result.msg);
				}
			},
		});
	});

	$(document).on('submit', 'form#edit_account_type_form', function(e) {
		e.preventDefault();
		var form = $(this);
		var data = form.serialize();

		$.ajax({
			method: 'PUT',
			url: $(this).attr('action'),
			dataType: 'json',
			data: data,
			success: function(result) {
				if (result.success == true) {
					$('#edit_account_type_modal').modal('hide');
					toastr.success(result.msg);
					if (result.data.account_type == 'sub_type') {
						account_sub_type_table.ajax.reload();
					} else {
						detail_type_table.ajax.reload();
					}

				} else {
					toastr.error(result.msg);
				}
			},
		});
	});

	$(document).on('click', 'button.delete_account_type_button', function() {
		swal({
			title: LANG.sure,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		}).then(willDelete => {
			if (willDelete) {
				var href = $(this).data('href');
				var data = $(this).serialize();

				$.ajax({
					method: 'DELETE',
					url: href,
					dataType: 'json',
					data: data,
					success: function(result) {
						if (result.success == true) {
							toastr.success(result.msg);
							account_sub_type_table.ajax.reload();
							detail_type_table.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					},
				});
			}
		});
	});

	$(document).on('click', 'button.accounting_reset_data', function() {
		swal({
			title: LANG.sure,
			icon: 'warning',
			text: "@lang('accounting::lang.reset_help_txt')",
			buttons: true,
			dangerMode: true,
		}).then(willDelete => {
			if (willDelete) {
				var href = $(this).data('href');
				window.location.href = href;
			}
		});
	});
</script>
@endsection