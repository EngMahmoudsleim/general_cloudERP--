@extends('layouts.app')
@section('title', __('inventoryreset::lang.inventory_reset'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('inventoryreset::lang.inventory_reset') }}
		<small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">{{ __('inventoryreset::lang.dashboard_subtitle') }}</small>
	</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">

        <!-- Summary Widgets -->
		<div class="col-md-12">
			<div class="tw-pb-6">
				<div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-grid-cols-2 xl:tw-grid-cols-4 sm:tw-gap-5">
					<div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl tw-ring-1 tw-ring-gray-200">
						<div class="tw-p-4 sm:tw-p-5">
							<div class="tw-flex tw-items-center tw-gap-4">
								<div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0 !tw-bg-blue-100 !tw-text-blue-500" style="background-color: #dbeafe !important; color: #3b82f6 !important;">
									<svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none" />
										<path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
										<path d="M12 12l8 -4.5" />
										<path d="M12 12l0 9" />
										<path d="M12 12l-8 -4.5" />
									</svg>
								</div>
								<div class="tw-flex-1 tw-min-w-0">
									<p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
										{{ __('inventoryreset::lang.total_products') }}
									</p>
									<p class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono" id="total-products">
										{{ $stats['total_products'] }}
									</p>
								</div>
							</div>
						</div>
					</div>

					<div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
						<div class="tw-p-4 sm:tw-p-5">
							<div class="tw-flex tw-items-center tw-gap-4">
								<div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0 tw-bg-yellow-100 tw-text-yellow-500">
									<svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none" />
										<path d="M12 9v4" />
										<path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
										<path d="M12 16h.01" />
									</svg>
								</div>
								<div class="tw-flex-1 tw-min-w-0">
									<p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
										{{ __('inventoryreset::lang.products_with_stock') }}
									</p>
									<p class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono" id="products-with-stock">
										{{ $stats['products_with_stock'] }}
									</p>
								</div>
							</div>
						</div>
					</div>

					<div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
						<div class="tw-p-4 sm:tw-p-5">
							<div class="tw-flex tw-items-center tw-gap-4">
								<div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0 tw-bg-red-100 tw-text-red-500">
									<svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none" />
										<path d="M3 12h3m12 0h3" />
										<path d="M12 3v3m0 12v3" />
										<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
										<path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
									</svg>
								</div>
								<div class="tw-flex-1 tw-min-w-0">
									<p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
										{{ __('inventoryreset::lang.products_without_stock') }}
									</p>
									<p class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono" id="products-without-stock">
										{{ $stats['products_without_stock'] }}
									</p>
								</div>
							</div>
						</div>
					</div>

					<div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
						<div class="tw-p-4 sm:tw-p-5">
							<div class="tw-flex tw-items-center tw-gap-4">
								<div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0 !tw-bg-emerald-100 !tw-text-emerald-500" style="background-color: #d1fae5 !important; color: #10b981 !important;">
									<svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none" />
										<path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
										<path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
										<path d="M12 6v10" />
									</svg>
								</div>
								<div class="tw-flex-1 tw-min-w-0">
									<p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
										{{ __('inventoryreset::lang.total_stock_value') }}
									</p>
									<p class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono display_currency" data-currency_symbol="true" id="total-stock-value">
										{{ $stats['total_stock_value'] }}
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

        <!-- Main Control Panel -->
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-sync"></i> {{ __('inventoryreset::lang.inventory_reset_form') }}
                    </h3>
                </div>

                <div class="box-body">
                    @if($stats['products_with_stock'] > 0)
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-circle"></i>
                        {{ __('inventoryreset::lang.products_with_stock_warning', ['count' => $stats['products_with_stock']]) }}
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        {{ __('inventoryreset::lang.all_stock_zero_info') }}
                    </div>
                    @endif

                    <form id="reset-form">
                        @csrf

                        <!-- New Clear Structure -->

                        <!-- 1. Reset Type Selection -->
                        <div class="form-group">
                            <i class="fa fa-cubes"></i> <strong>{{ __('inventoryreset::lang.reset_type') }}:</strong>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="reset_type" value="all_products" checked id="type_all">
                                    <strong>{{ __('inventoryreset::lang.all_products') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.all_products_desc') }}</small>
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="reset_type" value="selected_products" id="type_selected">
                                    <strong>{{ __('inventoryreset::lang.selected_products_type') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.selected_products_type_desc') }}</small>
                                </label>
                            </div>
                        </div>

                        <!-- 2. Reset Mode Selection -->
                        <div class="form-group">
                            <i class="fa fa-filter"></i> <strong>{{ __('inventoryreset::lang.reset_mode') }}:</strong>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="reset_mode" value="all_levels" checked id="mode_all">
                                    <strong>{{ __('inventoryreset::lang.all_stock_levels') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.all_stock_levels_desc') }}</small>
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="reset_mode" value="positive_only" id="mode_positive">
                                    <strong>{{ __('inventoryreset::lang.positive_stock_only') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.positive_stock_only_desc') }}</small>
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="reset_mode" value="negative_only" id="mode_negative">
                                    <strong>{{ __('inventoryreset::lang.negative_stock_only') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.negative_stock_only_desc') }}</small>
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="reset_mode" value="zero_only" id="mode_zero">
                                    <strong>{{ __('inventoryreset::lang.zero_stock_only') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.zero_stock_only_desc') }}</small>
                                </label>
                            </div>
                        </div>

                        <!-- 3. Operation Type Selection -->
                        <div class="form-group">
                            <i class="fa fa-cogs"></i> <strong>{{ __('inventoryreset::lang.operation_type') }}:</strong>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="operation_type" value="reset_to_zero" checked id="op_reset">
                                    <strong>{{ __('inventoryreset::lang.reset_to_zero') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.reset_to_zero_desc') }}</small>
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="operation_type" value="set_to_quantity" id="op_set">
                                    <strong>{{ __('inventoryreset::lang.set_to_quantity') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.set_to_quantity_desc') }}</small>
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="operation_type" value="add_quantity" id="op_add">
                                    <strong>{{ __('inventoryreset::lang.add_quantity') }}</strong>
                                    <br><small class="text-muted">{{ __('inventoryreset::lang.add_quantity_desc') }}</small>
                                </label>
                            </div>
                        </div>

                        <!-- 4. Target Quantity (for set and add operations) -->
                        <div class="form-group" id="target_quantity_group" style="display: none;">
                            <label id="target_quantity_label"><i class="fa fa-hashtag"></i> <strong>{{ __('inventoryreset::lang.target_quantity') }} <span class="text-danger">*</span></strong></label>
                            <input type="number" name="target_quantity" class="form-control" min="0" placeholder="{{ __('inventoryreset::lang.target_quantity_placeholder') }}">
                            <small class="text-muted" id="target_quantity_help">{{ __('inventoryreset::lang.target_quantity_desc') }}</small>
                        </div>

                        <!-- Location Selection - shown for all reset types -->
                        <div class="form-group">
                            <label>{{ __('inventoryreset::lang.select_location') }}:</label>
                            <select name="location_id" class="form-control select2">
                                <option value="">{{ __('report.all_locations') }}</option>
                                @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Product Selection - shown for selected products reset types only -->
                        <div id="product-selection" class="form-group hidden">
                            <label>{{ __('inventoryreset::lang.select_products') }}:</label>
                            <select id="product_ids" class="form-control select2" style="width: 100%;">
                                <option></option>
                            </select>
                        </div>


                        <!-- Selected Products Table -->
                        <div id="selected-products-container" class="margin-top" style="display: none;">
                            <label>{{ __('inventoryreset::lang.selected_products') }}</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="selected-products-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">{{ __('sale.product') }}</th>
                                            <th style="width: 15%;">{{ __('product.sku') }}</th>
                                            <th style="width: 20%;">{{ __('report.current_stock') }}</th>
                                            <th style="width: 20%;">{{ __('inventoryreset::lang.locations') }}</th>
                                            <th style="width: 10%;">{{ __('messages.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected-products-tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="alert alert-info" style="margin-bottom: 15px;">
                                <div class="checkbox" style="margin: 0;">
                                    <label>
                                        <input type="checkbox" name="fix_stock_mismatches" id="fix_stock_mismatches"
                                            value="1" checked disabled>
                                        <!-- Hidden input to ensure value is submitted when checkbox is disabled -->
                                        <input type="hidden" name="fix_stock_mismatches" value="1">
                                        <strong><i class="fa fa-wrench"></i> {{ __('inventoryreset::lang.fix_stock_mismatches') }}</strong>
                                        <br><small class="text-muted">{{
                                            __('inventoryreset::lang.fix_stock_mismatches_desc') }}</small>
                                        <br><small class="text-primary"><i class="fa fa-info-circle"></i> {{ __('inventoryreset::lang.fix_stock_mismatches_help')
                                            }}</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('inventoryreset::lang.reason') }}: <span class="text-danger">*</span></label>
                            <textarea name="reason" rows="3" required class="form-control"
                                placeholder="{{ __('inventoryreset::lang.reason_placeholder') }}"></textarea>
                            <small class="help-block">{{ __('inventoryreset::lang.reason_help') }}</small>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="confirm_reset" required>
                                {{ __('inventoryreset::lang.confirm_reset_checkbox') }}
                            </label>
                        </div>
                    </form>
                </div>

                <div class="box-footer text-center">
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        {{ __('inventoryreset::lang.danger_zone_description') }}
                    </div>
                    <button type="button" id="execute-reset-btn" class="tw-dw-btn tw-dw-btn-error tw-text-white"
                        @if(!auth()->user()->can('inventory_reset.create')) disabled @endif>
                        <i class="fa fa-trash"></i> {{ __('inventoryreset::lang.execute_reset') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Help Panel -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-question-circle"></i> {{ __('inventoryreset::lang.help') }}
                    </h3>
                </div>
                <div class="box-body">
                    <ul class="list-unstyled">
                        <li class="margin-bottom">
                            <i class="fa fa-info-circle text-blue"></i>
                            {{ __('inventoryreset::lang.help_tip_1') }}
                        </li>
                        <li class="margin-bottom">
                            <i class="fa fa-info-circle text-blue"></i>
                            {{ __('inventoryreset::lang.help_tip_2') }}
                        </li>
                        <li class="margin-bottom">
                            <i class="fa fa-info-circle text-blue"></i>
                            {{ __('inventoryreset::lang.help_tip_3') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Last Reset Info -->
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-clock"></i> {{ __('inventoryreset::lang.last_reset_date') }}
                    </h3>
                </div>
                <div class="box-body">
                    @if($stats['last_reset_date'])
                    <p>{{ @format_datetime($stats['last_reset_date']) }}</p>
                    @else
                    <p class="text-muted">{{ __('inventoryreset::lang.never') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reset History -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-history"></i> {{ __('inventoryreset::lang.recent_reset_history') }}
                    </h3>
                </div>
                <div class="box-body">
                    @if($recentResets->count() > 0)
                    <!-- Filters -->
                    <div class="row margin-bottom">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-user">{{ __('inventoryreset::lang.filter_by_user') }}:</label>
                                <select id="filter-user" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('lang_v1.all') }}</option>
                                    @foreach($recentResets->pluck('user')->unique('id')->filter() as $user)
                                    <option value="{{ $user->first_name }} {{ $user->last_name }}">{{ $user->first_name
                                        }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-status">{{ __('inventoryreset::lang.filter_by_status') }}:</label>
                                <select id="filter-status" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('lang_v1.all') }}</option>
                                    <option value="completed">{{ __('inventoryreset::lang.completed') }}</option>
                                    <option value="processing">{{ __('inventoryreset::lang.processing') }}</option>
                                    <option value="failed">{{ __('inventoryreset::lang.failed') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-type">{{ __('inventoryreset::lang.filter_by_type') }}:</label>
                                <select id="filter-type" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('lang_v1.all') }}</option>
                                    <option value="all_products">{{ __('inventoryreset::lang.all_products') }}</option>
                                    <option value="selected_products">{{ __('inventoryreset::lang.selected_products_type') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-reset-mode">{{ __('inventoryreset::lang.filter_by_reset_mode') }}:</label>
                                <select id="filter-reset-mode" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('lang_v1.all') }}</option>
                                    <option value="all_levels">{{ __('inventoryreset::lang.all_stock_levels') }}</option>
                                    <option value="positive_only">{{ __('inventoryreset::lang.positive_stock_only') }}</option>
                                    <option value="negative_only">{{ __('inventoryreset::lang.negative_stock_only') }}</option>
                                    <option value="zero_only">{{ __('inventoryreset::lang.zero_stock_only') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-operation-type">{{ __('inventoryreset::lang.filter_by_operation_type') }}:</label>
                                <select id="filter-operation-type" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('lang_v1.all') }}</option>
                                    <option value="reset_to_zero">{{ __('inventoryreset::lang.reset_to_zero') }}</option>
                                    <option value="set_to_quantity">{{ __('inventoryreset::lang.set_to_quantity') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" id="clear-filters"
                                    class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white hover:tw-text-white">
                                    <i class="fa fa-eraser"></i> {{ __('inventoryreset::lang.clear_filters') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="reset-history-table">
                            <thead>
                                <tr>
                                    <th>{{ __('inventoryreset::lang.reset_id') }}</th>
                                    <th>{{ __('inventoryreset::lang.reset_type') }}</th>
                                    <th>{{ __('inventoryreset::lang.reset_mode') }}</th>
                                    <th>{{ __('inventoryreset::lang.operation_type') }}</th>
                                    <th>{{ __('inventoryreset::lang.reason') }}</th>
                                    <th>{{ __('inventoryreset::lang.items_reset') }}</th>
                                    <th>{{ __('inventoryreset::lang.performed_by') }}</th>
                                    <th>{{ __('inventoryreset::lang.completed_at') }}</th>
                                    <th>{{ __('inventoryreset::lang.status') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentResets as $reset)
                                <tr>
                                    <td>#{{ $reset->id }}</td>
                                    <td>
                                        <span
                                            class="label {{ $reset->reset_type === 'all_products' ? 'label-info' : 'label-warning' }}">{{
                                            $reset->formatted_reset_type }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="label {{ $reset->reset_mode === 'all_levels' ? 'label-info' : 'label-warning' }}">{{
                                            $reset->formatted_reset_mode }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="label {{ $reset->target_quantity == 0 ? 'label-info' : 'label-warning' }}">{{
                                            $reset->formatted_target_quantity }}</span>
                                    </td>
                                    <td>{{ Str::limit($reset->reason, 50) }}</td>
                                    <td>{{ $reset->items_reset }}</td>
                                    <td>{{ $reset->user->first_name }} {{ $reset->user->last_name }}</td>
                                    <td>{{ $reset->completed_at ? @format_datetime($reset->completed_at) : '-' }}</td>
                                    <td>
                                        <span class="label {{ $reset->status_badge_class }}">
                                            {{ __("inventoryreset::lang.{$reset->status}") }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(auth()->user()->can('inventory_reset.view'))
                                        <a href="{{ route('inventory-reset.show-reset', $reset->id) }}"
                                            class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-xs tw-text-white hover:tw-text-white">
                                            {{ __('messages.view') }}
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center">
                        <i class="fa fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                        <h4>{{ __('inventoryreset::lang.no_reset_history') }}</h4>
                        <p class="text-muted">{{ __('inventoryreset::lang.no_reset_history_desc') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('css')
<style>
    /* Selected Products Table Styling */
    #selected-products-container {
        background-color: #f9f9f9;
        border: 1px solid #e3e3e3;
        border-radius: 5px;
        padding: 15px;
        margin-top: 15px;
    }

    #selected-products-container label {
        color: #333;
        font-weight: 600;
        margin-bottom: 10px;
    }

    #selected-products-table {
        background-color: #ffffff;
        margin-bottom: 0;
    }

    #selected-products-table thead {
        background-color: #f5f5f5;
    }

    #selected-products-table thead th {
        background-color: #e8f4f8;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #3c8dbc;
        padding: 4px 6px;
        font-size: 12px;
        line-height: 1.2;
    }

    #selected-products-table tbody tr {
        background-color: #ffffff;
    }

    #selected-products-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    #selected-products-table tbody tr td {
        vertical-align: middle;
        padding: 3px 6px;
        font-size: 12px;
        line-height: 1.2;
    }

    #selected-products-table tbody tr td:last-child {
        text-align: center;
    }

    #selected-products-table thead th:last-child {
        text-align: center;
    }

    .remove-product {
        transition: all 0.2s ease;
        padding: 4px;
        font-size: 11px;
        line-height: 1;
        min-width: 24px;
        height: 24px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .remove-product:hover {
        background-color: #d73925;
        border-color: #d73925;
    }
</style>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
    // Localized strings
    var lang_not_available = "{{ __('inventoryreset::lang.not_available') }}";

    // Store selected products data
    var selectedProductsData = {};

    // Initialize Select2 for product selection
    $('#product_ids').select2({
        ajax: {
            url: '{{ route("inventory-reset.search-products") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term || '', // search term
                    page: params.page || 1,
                    location_id: $('select[name="location_id"]').val() // send selected location
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.products.map(function(product) {
                        return {
                            id: product.id,
                            text: product.name + (product.sku ? ' (' + product.sku + ')' : '') +
                                  (product.current_stock !== undefined ? ' - {{ __('lang_v1.in_stock') }}: ' + product.current_stock + (product.unit ? ' ' + product.unit : '') : ''),
                            name: product.name,
                            sku: product.sku,
                            current_stock: product.current_stock,
                            unit: product.unit
                        };
                    }),
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 1,
        templateResult: function(product) {
            if (product.loading) return product.text;
            return product.text;
        },
        templateSelection: function(product) {
            return product.text;
        },
        placeholder: '{{ __('lang_v1.search_product_placeholder') }}',
        allowClear: true
    });

    // Handle product selection
    $('#product_ids').on('select2:select', function (e) {
        var data = e.params.data;
        var productId = data.id;

        // Store product data
        selectedProductsData[productId] = {
            id: productId,
            name: data.name,
            sku: data.sku || lang_not_available,
            current_stock: data.current_stock || 0,
            unit: data.unit || ''
        };

        // Add to table
        addProductToTable(selectedProductsData[productId]);

        // Clear the select2 selection to allow adding more products
        $('#product_ids').val(null).trigger('change');

        // Show the table container
        $('#selected-products-container').show();

        // Update hidden form field with all selected product IDs
        updateFormProductIds();
    });

    function addProductToTable(product) {
        // Check if product already exists in table
        if ($('#selected-products-tbody tr[data-product-id="' + product.id + '"]').length > 0) {
            return; // Product already in table
        }

        // Get current location name for display
        var selectedLocationId = $('select[name="location_id"]').val();
        var locationName = selectedLocationId ? $('select[name="location_id"] option:selected').text() : '{{ __('report.all_locations') }}';

        var row = '<tr data-product-id="' + product.id + '">' +
            '<td>' + product.name + '</td>' +
            '<td>' + (product.sku || lang_not_available) + '</td>' +
            '<td class="text-danger"><strong>' + product.current_stock + (product.unit ? ' ' + product.unit : '') + '</strong></td>' +
            '<td>' + locationName + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-xs remove-product" data-product-id="' + product.id + '" title="{{ __('lang_v1.delete_selected') }}">' +
                    '<i class="fa fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';

        $('#selected-products-tbody').append(row);
    }

    // Handle product removal
    $(document).on('click', '.remove-product', function() {
        var productId = $(this).data('product-id');

        // Remove from stored data
        delete selectedProductsData[productId];

        // Remove row from table
        $(this).closest('tr').remove();

        // Hide table if no products remain
        if (Object.keys(selectedProductsData).length === 0) {
            $('#selected-products-container').hide();
        }

        // Update hidden form field
        updateFormProductIds();
    });

    function updateFormProductIds() {
        var productIds = Object.keys(selectedProductsData);

        // Remove existing hidden inputs
        $('input[name="product_ids[]"]').remove();

        // Add hidden inputs for selected products
        productIds.forEach(function(productId) {
            $('#reset-form').append('<input type="hidden" name="product_ids[]" value="' + productId + '">');
        });
    }

    // Handle reset type changes
    $('input[name="reset_type"]').change(function() {
        var resetType = $(this).val();
        var resetMode = $('input[name="reset_mode"]:checked').val();

        // Show/hide product selection based on type
        if (resetType === 'selected_products') {
            $('#product-selection').removeClass('hidden');

			// Clear and refresh the Select2 dropdown to reflect current location
            $('#product_ids').val(null).trigger('change');

            // Clear any auto-populated negative products
            clearSelectedProducts();
        } else {
            $('#product-selection').addClass('hidden');

            // If switching to all products with negative mode, auto-populate
            if (resetMode === 'negative_only') {
                loadNegativeProductsToSelectedTable();
            } else {
                // Clear selected products
                clearSelectedProducts();
            }
        }
    });

    // Handle reset mode changes
    $('input[name="reset_mode"]').change(function() {
        var resetType = $('input[name="reset_type"]:checked').val();
        var resetMode = $(this).val();

        // Auto-populate selected products table with negative stock products
        if (resetType === 'all_products' && resetMode === 'negative_only') {
            loadNegativeProductsToSelectedTable();
        } else {
            // Clear the auto-populated products when switching away from negative mode
            if (resetType === 'all_products') {
                clearSelectedProducts();
            }
        }
    });

    // Handle operation type changes
    $('input[name="operation_type"]').change(function() {
        var operationType = $(this).val();

        // Show/hide target quantity field and update labels
        if (operationType === 'set_to_quantity') {
            $('#target_quantity_group').show();
            $('input[name="target_quantity"]').prop('required', true);
            $('#target_quantity_label').html('<i class="fa fa-hashtag"></i> <strong>{{ __('inventoryreset::lang.target_quantity') }} <span class="text-danger">*</span></strong>');
            $('#target_quantity_help').text('{{ __('inventoryreset::lang.target_quantity_desc') }}');
            $('input[name="target_quantity"]').attr('placeholder', '{{ __('inventoryreset::lang.target_quantity_placeholder') }}');
        } else if (operationType === 'add_quantity') {
            $('#target_quantity_group').show();
            $('input[name="target_quantity"]').prop('required', true);
            $('#target_quantity_label').html('<i class="fa fa-plus"></i> <strong>{{ __('inventoryreset::lang.add_quantity') }} <span class="text-danger">*</span></strong>');
            $('#target_quantity_help').text('{{ __('inventoryreset::lang.add_quantity_desc') }}');
            $('input[name="target_quantity"]').attr('placeholder', '{{ __('inventoryreset::lang.add_quantity_placeholder') }}');
        } else {
            $('#target_quantity_group').hide();
            $('input[name="target_quantity"]').prop('required', false);
        }
    });

    // Handle location changes
    $('select[name="location_id"]').change(function() {
		var resetType = $('input[name="reset_type"]:checked').val();
        var resetMode = $('input[name="reset_mode"]:checked').val();

        // Auto-reload negative products if in negative mode
        if (resetType === 'all_products' && resetMode === 'negative_only') {
            loadNegativeProductsToSelectedTable();
        }

		// for selected products - clear and refresh the product search
        if (resetType === 'selected_products') {
            // Clear previously selected products since location changed
            selectedProductsData = {};
            $('#selected-products-tbody').empty();
            $('#selected-products-container').hide();
            $('input[name="product_ids[]"]').remove();

            // Clear and refresh the Select2 dropdown to reflect new location
            $('#product_ids').val(null).trigger('change');
        }
    });

    // Function to load negative products into selected table
    function loadNegativeProductsToSelectedTable() {
        var locationId = $('select[name="location_id"]').val();
        var locationName = locationId ? $('select[name="location_id"] option:selected').text() : '{{ __('report.all_locations') }}';

        $.ajax({
            url: '{{ route("inventory-reset.negative-products") }}',
            method: 'GET',
            data: {
                location_id: locationId
            },
            success: function(response) {
                if (response.success && response.products.length > 0) {
                    // Clear existing selected products
                    clearSelectedProducts();

                    // Add each negative product to the selected table
                    response.products.forEach(function(product) {
                        // Store in selectedProductsData
                        selectedProductsData[product.id] = {
                            id: product.id,
                            name: product.name,
                            sku: product.sku || lang_not_available,
                            current_stock: product.total_negative_qty,
                            unit: product.unit || ''
                        };

                        // Add to table with location info
                        var locationsList = product.locations.map(function(loc) {
                            return loc.location_name;
                        }).join(', ');

                        var row = '<tr data-product-id="' + product.id + '">' +
                            '<td>' + product.name + '</td>' +
                            '<td>' + (product.sku || lang_not_available) + '</td>' +
                            '<td class="text-danger"><strong>' + product.total_negative_qty + ' ' + (product.unit || '') + '</strong></td>' +
                            '<td>' + locationsList + '</td>' +
                            '<td>' +
                                '<button type="button" class="btn btn-danger btn-xs remove-product" data-product-id="' + product.id + '" title="{{ __('lang_v1.delete_selected') }}">' +
                                    '<i class="fa fa-trash"></i>' +
                                '</button>' +
                            '</td>' +
                        '</tr>';

                        $('#selected-products-tbody').append(row);
                    });

                    // Show the table and update form
                    $('#selected-products-container').show();
                    updateFormProductIds();

                    // Update the label to indicate these are negative stock products
                    $('#selected-products-container label').html('<i class="fa fa-exclamation-triangle text-danger"></i> {{ __('inventoryreset::lang.products_with_negative_inventory') }} (' + response.products.length + ')');
                } else {
                    // No negative products found
                    clearSelectedProducts();
                    $('#selected-products-container').show();
                    $('#selected-products-container label').html('<i class="fa fa-check-circle text-success"></i> {{ __('inventoryreset::lang.no_negative_inventory_found') }}');
                }
            },
            error: function(xhr) {
                console.error('Error loading negative products:', xhr.responseText);
                clearSelectedProducts();
            }
        });
    }

    // Function to clear selected products
    function clearSelectedProducts() {
        selectedProductsData = {};
        $('#selected-products-tbody').empty();
        $('#selected-products-container').hide();
        $('input[name="product_ids[]"]').remove();
        // Reset label to default
        $('#selected-products-container label').html('{{ __('inventoryreset::lang.selected_products') }}');
    }


    // Handle reset execution
    $('#execute-reset-btn').click(function() {
        if (!$('#reset-form')[0].checkValidity()) {
            $('#reset-form')[0].reportValidity();
            return;
        }

        // Additional validation for selected products
        var resetType = $('input[name="reset_type"]:checked').val();
        if (resetType === 'selected_products') {
            var selectedProductIds = Object.keys(selectedProductsData);
            if (selectedProductIds.length === 0) {
				swal('{{ __('inventoryreset::lang.error') }}', '{{ __('inventoryreset::lang.select_atleast_product') }}', 'error');
                return;
            }
        }

        // Get operation details for confirmation message
        var resetType = $('input[name="reset_type"]:checked').val();
        var resetMode = $('input[name="reset_mode"]:checked').val();
        var operationType = $('input[name="operation_type"]:checked').val();
        var targetQuantity = $('input[name="target_quantity"]').val() || 0;
        var selectedLocationId = $('select[name="location_id"]').val();
        var locationName = selectedLocationId ? $('select[name="location_id"] option:selected').text() : '';

        // Build descriptive confirmation message
        var typeText = (resetType === 'selected_products') ? '{{ __('inventoryreset::lang.selected_products_type') }}' : '{{ __('inventoryreset::lang.all_products') }}';
        var modeText = {
            'all_levels': '{{ __('inventoryreset::lang.all_stock_levels') }}',
            'positive_only': '{{ __('inventoryreset::lang.positive_stock_only') }}',
            'negative_only': '{{ __('inventoryreset::lang.negative_stock_only') }}',
            'zero_only': '{{ __('inventoryreset::lang.zero_stock_only') }}'
        }[resetMode] || '{{ __('inventoryreset::lang.all_stock_levels') }}';

        var operationText = '';
        if (operationType === 'set_to_quantity') {
            operationText = '{{ __('inventoryreset::lang.set_to_quantity') }}' + `: ${targetQuantity}`;
        } else if (operationType === 'add_quantity') {
            operationText = '{{ __('inventoryreset::lang.add_quantity') }}' + `: +${targetQuantity}`;
        } else {
            operationText = '{{ __('inventoryreset::lang.reset_to_zero') }}';
        }

        var locationText = locationName ? `${locationName}` : '{{ __('report.all_locations') }}';

        var confirmTitle = "{{ __('inventoryreset::lang.warning_irreversible') }}";
		var confirmText = "{{ __('inventoryreset::lang.info_alert', ['operation' => ':operation', 'type' => ':type', 'mode' => ':mode', 'location' => ':location']) }}"
            .replace(':operation', operationText)
            .replace(':type', typeText)
            .replace(':mode', modeText)
            .replace(':location', locationText);
        var htmlContent = null;

        // Add product details for selected products
        if (resetType === 'selected_products') {
            var selectedProductIds = Object.keys(selectedProductsData);
            var productsList = Object.values(selectedProductsData).map(function(product) {
                return `<li><strong>${product.name}</strong> (${product.sku}) - {{ __('lang_v1.in_stock') }}: ${product.current_stock} ${product.unit}</li>`;
            }).join('');

            htmlContent = `
                <div style="text-align: left; margin-top: 10px;">
                    <h4>{{ __('inventoryreset::lang.selected_products_type') }}: ${selectedProductIds.length}</h4>
                    <ul style="max-height: 200px; overflow-y: auto;">
                        ${productsList}
                    </ul>
                </div>
            `;
        } else {
            // Show warning for bulk operations
            htmlContent = '<div style="text-align: center; margin-top: 15px;"><strong style="color: #dc3545; font-size: 16px;">{{ __('inventoryreset::lang.confirm_alert') }}</strong></div>';
        }

        swal({
            title: confirmTitle,
            text: confirmText,
            content: {
                element: 'div',
                attributes: {
                    innerHTML: htmlContent
                }
            },
            icon: 'warning',
            buttons: {
                cancel: {
                    text: '{{ __('messages.cancel') }}',
                    visible: true,
                    className: 'btn btn-secondary',
                    closeModal: true,
                },
                confirm: {
                    text: '{{ __('inventoryreset::lang.execute_reset') }}',
                    className: 'btn btn-danger',
                    closeModal: false,
                }
            },
            dangerMode: true,
        }).then((confirmed) => {
            if (confirmed) {
                // Show second confirmation for extra security
                swal({
                    title: '{{ __('inventoryreset::lang.final_confirmation_alert') }}',
                    text: '{{ __('inventoryreset::lang.type_reset_hint') }}',
                    icon: 'error',
                    content: {
                        element: 'input',
                        attributes: {
                            placeholder: '{{ __('inventoryreset::lang.reset_placeholder') }}',
                            type: 'text',
                            style: 'text-align: center; text-transform: uppercase; font-weight: bold;'
                        }
                    },
                    buttons: {
                        cancel: {
                            text: '{{ __('messages.cancel') }}',
                            visible: true,
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: '{{ __('inventoryreset::lang.confirm_reset') }}',
                            className: 'btn btn-danger',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                }).then((value) => {
                    if (value && value.toUpperCase() === 'RESET') {
                        executeInventoryReset();
                    } else {
                        swal('{{ __('inventoryreset::lang.error') }}', '{{ __('inventoryreset::lang.must_type_reset_error') }}', 'error');
                    }
                });
            }
        });
    });

    function executeInventoryReset() {
        var formData = $('#reset-form').serialize();
        console.log('Form data being sent:', formData);

        // Show loading with SweetAlert
        swal({
            title: '{{ __('inventoryreset::lang.processing_status') }}',
            text: '{{ __('inventoryreset::lang.processing_plz_wait') }}',
            icon: 'info',
            buttons: false,
            closeOnClickOutside: false,
            closeOnEsc: false
        });

        $.ajax({
            url: "{{ route('inventory-reset.execute-reset') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                swal.close();
                if (response.success) {
                    swal({
                        title: '{{ __('inventoryreset::lang.success') }}',
                        text: '{{ __('inventoryreset::lang.reset_completed_successfully') }}' + '\n\n{{ __('inventoryreset::lang.reset_id') }}: #' + response.reset_id + '\n{{ __('inventoryreset::lang.items_reset') }}: ' + response.items_reset,
                        icon: 'success',
                        button: {
                            text: '{{ __('inventoryreset::lang.reload_page') }}',
                            className: 'btn btn-success'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    swal({
                        title: '{{ __('inventoryreset::lang.error') }}',
                        text: response.message,
                        icon: 'error',
                        button: {
                            text: '{{ __('messages.close') }}',
                            className: 'btn btn-danger'
                        }
                    });
                }
            },
            error: function(xhr) {
                swal.close();
                console.log('AJAX Error:', xhr.responseJSON);

                var message = xhr.responseJSON?.message || 'An error occurred';

                // If there are validation errors, show them
                if (xhr.responseJSON?.errors) {
                    var errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join('\n');
                }

                swal({
                    title: '{{ __('inventoryreset::lang.error') }}',
                    text: message,
                    icon: 'error',
                    button: {
                        text: '{{ __('messages.close') }}',
                        className: 'btn btn-danger'
                    }
                });
            }
        });
    }

    // Initialize DataTable for Reset History
    @if($recentResets->count() > 0)
    var resetHistoryTable = $('#reset-history-table').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 25,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "targets": [1], "className": "text-center" },
            { "targets": [2], "className": "text-center" },
            { "targets": [3], "className": "text-center" },
            { "targets": [8], "className": "text-center" },
            { "targets": [9], "className": "text-center", "orderable": false }
        ]
    });

    // Initialize filter dropdowns
    $('#filter-user, #filter-status, #filter-type, #filter-reset-mode, #filter-operation-type').select2();

    // Filter functionality
    $('#filter-user').on('change', function() {
        var filterValue = this.value;
        resetHistoryTable.column(5).search(filterValue).draw();
    });

    $('#filter-status').on('change', function() {
        var filterValue = this.value;
        if (filterValue === 'completed') {
            resetHistoryTable.column(7).search('{{ __('inventoryreset::lang.completed') }}').draw();
        } else if (filterValue === 'processing') {
            resetHistoryTable.column(7).search('{{ __('inventoryreset::lang.processing') }}').draw();
        } else if (filterValue === 'failed') {
            resetHistoryTable.column(7).search('{{ __('inventoryreset::lang.failed') }}').draw();
        } else {
            resetHistoryTable.column(7).search('').draw();
        }
    });

    $('#filter-type').on('change', function() {
        var filterValue = this.value;
        if (filterValue === 'all_products') {
            resetHistoryTable.column(1).search('{{ __('inventoryreset::lang.all_products') }}').draw();
        } else if (filterValue === 'selected_products') {
            resetHistoryTable.column(1).search('{{ __('inventoryreset::lang.selected_products_type') }}').draw();
        } else {
            resetHistoryTable.column(1).search('').draw();
        }
    });

    $('#filter-reset-mode').on('change', function() {
        var filterValue = this.value;
        if (filterValue === 'all_levels') {
            resetHistoryTable.column(2).search('{{ __('inventoryreset::lang.all_stock_levels') }}').draw();
        } else if (filterValue === 'positive_only') {
            resetHistoryTable.column(2).search('{{ __('inventoryreset::lang.positive_stock_only') }}').draw();
        } else if (filterValue === 'negative_only') {
            resetHistoryTable.column(2).search('{{ __('inventoryreset::lang.negative_stock_only') }}').draw();
        } else if (filterValue === 'zero_only') {
            resetHistoryTable.column(2).search('{{ __('inventoryreset::lang.zero_stock_only') }}').draw();
        } else {
            resetHistoryTable.column(2).search('').draw();
        }
    });

    $('#filter-operation-type').on('change', function() {
        var filterValue = this.value;
        if (filterValue === 'reset_to_zero') {
            resetHistoryTable.column(3).search('{{ __('inventoryreset::lang.reset_to_zero') }}').draw();
        } else if (filterValue === 'set_to_quantity') {
            resetHistoryTable.column(3).search('{{ __('inventoryreset::lang.set_to_quantity') }}').draw();
        } else {
            resetHistoryTable.column(3).search('').draw();
        }
    });

    // Clear filters
    $('#clear-filters').on('click', function() {
        $('#filter-user, #filter-status, #filter-type, #filter-reset-mode, #filter-operation-type').val('').trigger('change');
        resetHistoryTable.columns().search('').draw();
    });
    @endif

});
</script>
@endsection