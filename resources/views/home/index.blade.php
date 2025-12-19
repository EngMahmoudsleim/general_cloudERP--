@extends('layouts.app')
@section('title', __('home.home'))

@section('content')
    @php
        $enabled_modules = session('business.enabled_modules') ?? [];
    @endphp

    <div class="tw-px-4 lg:tw-px-6 tw-pt-4 tw-space-y-6">
        <div class="tw-relative dashboard-hero tw-p-6 md:tw-p-8">
            <div class="tw-relative tw-z-10 tw-grid tw-gap-6 lg:tw-grid-cols-2 lg:tw-items-center">
                <div class="tw-space-y-2">
                    <p class="tw-inline-flex tw-items-center tw-gap-2 tw-text-sm tw-font-semibold tw-uppercase tw-tracking-wide tw-text-white/80">
                        <span class="tw-inline-flex tw-size-2.5 tw-rounded-full tw-bg-emerald-300"></span>
                        {{ __('messages.welcome') }}
                    </p>
                    <h1 class="tw-text-3xl md:tw-text-4xl tw-font-bold tw-text-white tw-leading-tight">
                        {{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
                    </h1>
                    <p class="tw-text-white/80 tw-max-w-2xl tw-text-sm md:tw-text-base">
                        {{ __('home.home') }}
                    </p>
                </div>

                @if (auth()->user()->can('dashboard.data') && $is_admin)
                    <div class="tw-grid tw-gap-3 md:tw-grid-cols-2 tw-bg-white/10 tw-rounded-2xl tw-backdrop-blur tw-p-3 md:tw-p-4 tw-shadow-lg">
                        @if (count($all_locations) > 1)
                            <div>
                                <label class="tw-text-xs tw-font-semibold tw-text-white/80 tw-block tw-mb-1" for="dashboard_location">
                                    {{ __('lang_v1.select_location') }}
                                </label>
                                {!! Form::select('dashboard_location', $all_locations, null, [
                                    'class' => 'form-control select2 tw-bg-white/15 tw-border-0 tw-text-white tw-placeholder-white/70 tw-rounded-xl tw-py-2',
                                    'placeholder' => __('lang_v1.select_location'),
                                    'id' => 'dashboard_location',
                                ]) !!}
                            </div>
                        @endif
                        <div class="tw-flex tw-items-end tw-justify-start md:tw-justify-end">
                            <button type="button" id="dashboard_date_filter"
                                class="date-filter-btn tw-inline-flex tw-items-center tw-gap-2 tw-justify-center tw-rounded-xl tw-bg-white tw-px-3 tw-py-2 tw-text-sm tw-font-semibold tw-text-slate-800 tw-shadow-lg tw-shadow-black/10 hover:tw-translate-y-[-1px] tw-transition">
                                <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M9 15h6" />
                                </svg>
                                <span>{{ __('messages.filter_by_date') }}</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="tw-relative tw-z-10 tw-mt-6">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                    <h2 class="tw-text-lg tw-font-semibold tw-text-white">{{ __('home.quick_shortcuts') }}</h2>
                </div>
                <div class="tw-grid tw-grid-cols-1 tw-gap-3 sm:tw-grid-cols-2 xl:tw-grid-cols-4">
                    @can('sell.create')
                        <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('sale.pos_sale') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('sale.add_sale') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v5.5A2.5 2.5 0 0 1 14.5 14H12l-1.4 1.4a1 1 0 0 1-1.4 0L8 14H5.5A2.5 2.5 0 0 1 3 11.5V6Zm3 1a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H6Z" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('sell.create')
                        <a href="{{ route('sells.create') }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80" style="color: black;">{{ __('sale.add_sale') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('lang_v1.add_sales_order') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14" />
                                    <path d="M5 12h14" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('purchase.create')
                        <a href="{{ route('purchases.create') }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('purchase.add_purchase') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('purchase.purchase') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 10l5-6 5 6" />
                                    <path d="M12 4v15" />
                                    <path d="M7 15h10" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('purchase_order.create')
                        <a href="{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('purchase.add_purchase') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('lang_v1.purchase_order') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 5h14" />
                                    <path d="M5 9h14" />
                                    <path d="M9 13h10" />
                                    <path d="M9 17h6" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('product.create')
                        <a href="{{ action([\App\Http\Controllers\ProductController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('product.add_product') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('product.product') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7l8-4 8 4-8 4-8-4z" />
                                    <path d="M4 17l8 4 8-4" />
                                    <path d="M4 12l8 4 8-4" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @if (in_array('stock_transfers', $enabled_modules) && auth()->user()->can('stock_transfer.create'))
                        <a href="{{ action([\App\Http\Controllers\StockTransferController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('lang_v1.stock_transfers') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('lang_v1.add_stock_transfer') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                    <path d="M12 5L5 12l7 7" />
                                </svg>
                            </span>
                        </a>
                    @endif

                    @if (in_array('stock_adjustment', $enabled_modules) && auth()->user()->can('stock_adjustment.create'))
                        <a href="{{ action([\App\Http\Controllers\StockAdjustmentController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('stock_adjustment.stock_adjustment') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('stock_adjustment.add') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7h16" />
                                    <path d="M4 12h12" />
                                    <path d="M4 17h8" />
                                </svg>
                            </span>
                        </a>
                    @endif

                    @can('expense.add')
                        <a href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('expense.add_expense') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('expense.expenses') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3v18" />
                                    <path d="M7 7h8a3 3 0 0 1 0 6h-6a3 3 0 0 0 0 6h9" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('contact.create')
                        <a href="{{ action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'customer']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('contact.add_customer') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('contact.customer') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 7a4 4 0 1 1-8 0a4 4 0 0 1 8 0" />
                                    <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('supplier.create')
                        <a href="{{ action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'supplier']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('contact.add_supplier') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('report.supplier') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 12a5 5 0 1 0 -5 -5" />
                                    <path d="M6 21v-2a4 4 0 0 1 4-4h2" />
                                    <path d="M15 21l2-2l2 2" />
                                    <path d="M17 19v-3" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('product.view')
                        <a href="{{ action([\App\Http\Controllers\ProductController::class, 'index']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('lang_v1.view_stock_details') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('lang_v1.manage_stock') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7l9 4l9-4l-9-4z" />
                                    <path d="M3 17l9 4l9-4" />
                                    <path d="M3 12l9 4l9-4" />
                                </svg>
                            </span>
                        </a>
                    @endcan

                    @can('user.create')
                        <a href="{{ action([\App\Http\Controllers\ManageUserController::class, 'create']) }}"
                            class="quick-action tw-group tw-flex tw-items-center tw-justify-between tw-gap-4 tw-p-4">
                            <div>
                                <p class="tw-text-xs tw-font-semibold tw-text-white/80">{{ __('user.add_user') }}</p>
                                <p class="tw-text-lg tw-font-semibold tw-text-white">{{ __('user.users') }}</p>
                            </div>
                            <span class="quick-action__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 7a3 3 0 1 1 -6 0a3 3 0 0 1 6 0" />
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" />
                                    <path d="M19 16v6" />
                                    <path d="M22 19h-6" />
                                </svg>
                            </span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        @if (auth()->user()->can('dashboard.data') && $is_admin)
            <div class="dashboard-grid">
                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-sky-100 tw-text-sky-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17h-11v-14h-2" />
                                <path d="M6 5l14 1l-1 7h-13" />
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-truncate tw-whitespace-nowrap">{{ __('home.total_sell') }}</p>
                            <p class="total_sell tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-emerald-100 tw-text-emerald-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"></path>
                                <path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path>
                                <path d="M12 6v10"></path>
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-truncate tw-whitespace-nowrap">{{ __('lang_v1.net') }} @show_tooltip(__('lang_v1.net_home_tooltip'))</p>
                            <p class="net tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-amber-100 tw-text-amber-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 7l1 0" />
                                <path d="M9 13l6 0" />
                                <path d="M13 17l2 0" />
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-truncate tw-whitespace-nowrap">{{ __('home.invoice_due') }}</p>
                            <p class="invoice_due tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-rose-100 tw-text-rose-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M21 7l-18 0" />
                                <path d="M18 10l3 -3l-3 -3" />
                                <path d="M6 20l-3 -3l3 -3" />
                                <path d="M3 17l18 0" />
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-truncate tw-whitespace-nowrap">{{ __('lang_v1.total_sell_return') }}
                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                    data-toggle="popover" data-placement="auto bottom" id="total_srp"
                                    data-value="{{ __('lang_v1.total_sell_return') }}-{{ __('lang_v1.total_sell_return_paid') }}"
                                    data-content="" data-html="true" data-trigger="hover"></i>
                            </p>
                            <p class="total_sell_return tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-indigo-100 tw-text-indigo-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 3v12"></path>
                                <path d="M16 11l-4 4l-4 -4"></path>
                                <path d="M3 12a9 9 0 0 0 18 0"></path>
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-truncate tw-whitespace-nowrap">{{ __('home.total_purchase') }}</p>
                            <p class="total_purchase tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-amber-50 tw-text-amber-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 9v4" />
                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                <path d="M12 16h.01" />
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500">{{ __('home.purchase_due') }}</p>
                            <p class="purchase_due tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-rose-50 tw-text-rose-600">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                                <path d="M15 14v-2a2 2 0 0 0 -2 -2h-4l2 -2m0 4l-2 -2" />
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-truncate tw-whitespace-nowrap">{{ __('lang_v1.total_purchase_return') }}
                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                    data-toggle="popover" data-placement="auto bottom" id="total_prp"
                                    data-value="{{ __('lang_v1.total_purchase_return') }}-{{ __('lang_v1.total_purchase_return_paid') }}"
                                    data-content="" data-html="true" data-trigger="hover"></i>
                            </p>
                            <p class="total_purchase_return tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>

                <div class="stat-card tw-p-5 sm:tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <span class="stat-card__icon tw-inline-flex tw-items-center tw-justify-center tw-size-14 tw-rounded-2xl tw-bg-blue-50 tw-text-blue-700">
                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 21v-13l-5 -4l-5 4v13" />
                                <path d="M7 13h10" />
                                <path d="M7 17h10" />
                                <path d="M9 21v-4" />
                                <path d="M15 21v-4" />
                            </svg>
                        </span>
                        <div class="tw-flex-1 tw-min-w-0">
                            <p 
                                                class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                {{ __('lang_v1.expense') }}
                                            </p>
<p class="expense total_expense tw-mt-0.5 tw-text-slate-900 tw-text-2xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono"></p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
    
    @if (auth()->user()->can('dashboard.data'))
        <div class="tw-px-5 tw-py-6">
            <div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-gap-5 lg:tw-grid-cols-2">
                @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                    <div
                        class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 9v4"></path>
                                        <path
                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                        </path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl fontas">
                                            {{ __('lang_v1.sales_payment_dues') }}
                                            @show_tooltip(__('lang_v1.tooltip_sales_payment_dues'))
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        {!! Form::select('sales_payment_dues_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'sales_payment_dues_location',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped" id="sales_payment_dues_table"
                                            style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('contact.customer')</th>
                                                    <th>@lang('sale.invoice_no')</th>
                                                    <th>@lang('home.due_amount')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @can('purchase.view')
                    <div
                        class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 9v4"></path>
                                        <path
                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                        </path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl fontas">
                                            {{ __('lang_v1.purchase_payment_dues') }}
                                            @show_tooltip(__('tooltip.payment_dues'))
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('purchase_payment_dues_location', $all_locations, null, [
                                                'class' => 'form-control select2 ',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'purchase_payment_dues_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped" id="purchase_payment_dues_table"
                                            style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('purchase.supplier')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('home.due_amount')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('stock_report.view')
                    <div
                        class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl fontas">
                                            {{ __('home.product_stock_alert') }}
                                            @show_tooltip(__('tooltip.product_stock_alert'))
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('stock_alert_location', $all_locations, null, [
                                                'class' => 'form-control select2',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'stock_alert_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped" id="stock_alert_table"
                                            style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('sale.product')</th>
                                                    <th>@lang('business.location')</th>
                                                    <th>@lang('report.current_stock')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (session('business.enable_product_expiry') == 1)
                        <div
                            class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                            <div class="tw-p-4 sm:tw-p-5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div
                                        class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                        <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 9v4"></path>
                                            <path
                                                d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                            </path>
                                            <path d="M12 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                        <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                            <h3 class="tw-font-bold tw-text-base lg:tw-text-xl fontas">
                                                {{ __('home.stock_expiry_alert') }}
                                                @show_tooltip(
                                                __('tooltip.stock_expiry_alert', [
                                                'days'
                                                =>session('business.stock_expiry_alert_days', 30) ]) )
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                    <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                        <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                            <input type="hidden" id="stock_expiry_alert_days"
                                                value="{{ \Carbon::now()->addDays(session('business.stock_expiry_alert_days', 30))->format('Y-m-d') }}">
                                            <table class="table table-bordered table-striped" id="stock_expiry_alert_table">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('business.product')</th>
                                                        <th>@lang('business.location')</th>
                                                        <th>@lang('report.stock_left')</th>
                                                        <th>@lang('product.expires_in')</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endcan
                @if (auth()->user()->can('so.view_all') || auth()->user()->can('so.view_own'))
                    <div
                        class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl fontas">
                                            {{ __('lang_v1.sales_order') }}
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('so_location', $all_locations, null, [
                                                'class' => 'form-control select2',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'so_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped ajax_view"
                                            id="sales_order_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('restaurant.order_no')</th>
                                                    <th>@lang('sale.customer_name')</th>
                                                    <th>@lang('lang_v1.contact_no')</th>
                                                    <th>@lang('sale.location')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.shipping_status')</th>
                                                    <th>@lang('lang_v1.quantity_remaining')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (
                    !empty($common_settings['enable_purchase_requisition']) &&
                        (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own')))
                    <div
                        class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 10v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-4"></path>
                                        <path d="M9 6h6"></path>
                                        <path d="M10 6v-2a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2"></path>
                                        <circle cx="12" cy="16" r="2"></circle>
                                        <path d="M5 20h14a2 2 0 0 0 2 -2v-10"></path>
                                        <path d="M15 16v4"></path>
                                        <path d="M9 20v-4"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            @lang('lang_v1.purchase_requisition')
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            @if (count($all_locations) > 1)
                                                {!! Form::select('pr_location', $all_locations, null, [
                                                    'class' => 'form-control select2',
                                                    'placeholder' => __('lang_v1.select_location'),
                                                    'id' => 'pr_location',
                                                ]) !!}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped ajax_view"
                                            id="purchase_requisition_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('purchase.location')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.required_by_date')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (
                    !empty($common_settings['enable_purchase_order']) &&
                        (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')))

                    <div
                        class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                        <line x1="4" y1="10" x2="20" y2="10" />
                                        <line x1="12" y1="4" x2="12" y2="20" />
                                        <line x1="12" y1="10" x2="16" y2="10" />
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            @lang('lang_v1.purchase_order')
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('po_location', $all_locations, null, [
                                                'class' => 'form-control select2',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'po_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped ajax_view"
                                            id="purchase_order_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('purchase.location')</th>
                                                    <th>@lang('purchase.supplier')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.quantity_remaining')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
                @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
                    <div
                        class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 9v4"></path>
                                        <path
                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                        </path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            @lang('lang_v1.payment_recovered_today')
                                        </h3>
                                    </div>

                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped" id="cash_flow_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('account.account')</th>
                                                    <th>@lang('lang_v1.description')</th>
                                                    <th>@lang('lang_v1.payment_method')</th>
                                                    <th>@lang('lang_v1.payment_details')</th>
                                                    <th>@lang('account.credit')</th>
                                                    <th>@lang('lang_v1.account_balance')
                                                        @show_tooltip(__('lang_v1.account_balance_tooltip'))</th>
                                                    <th>@lang('lang_v1.total_balance')
                                                        @show_tooltip(__('lang_v1.total_balance_tooltip'))</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="bg-gray font-17 footer-total text-center">
                                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                                    <td class="footer_total_credit"></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- @if (!empty($widgets['after_dashboard_reports']))
                    @foreach ($widgets['after_dashboard_reports'] as $widget)
                        {!! $widget !!}
                    @endforeach
                @endif --}}
            </div>
        </div>
    @endif

@endsection


<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@section('css')
    <style>
        .select2-container {
            width: 100% !important;
        }
        .fontas{
           font-family: "Cairo", sans-serif;
            font-style: normal;   
            font-weight: 120;
            }
    </style>
@endsection

@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    @includeIf('sales_order.common_js')
    @includeIf('purchase_order.common_js')
    <script type="text/javascript">
        $(document).ready(function() {
            sales_order_table = $('#sales_order_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                aaSorting: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": '{{ action([\App\Http\Controllers\SellController::class, 'index']) }}?sale_type=sales_order',
                    "data": function(d) {
                        d.for_dashboard_sales_order = true;

                        if ($('#so_location').length > 0) {
                            d.location_id = $('#so_location').val();
                        }
                    }
                },
                columnDefs: [{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'so_qty_remaining',
                        name: 'so_qty_remaining',
                        "searchable": false
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                ]
            });

            @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)

                // Cash Flow Table
                cash_flow_table = $('#cash_flow_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    "ajax": {
                        "url": "{{ action([\App\Http\Controllers\AccountController::class, 'cashFlow']) }}",
                        "data": function(d) {
                            d.type = 'credit';
                            d.only_payment_recovered = true;
                        }
                    },
                    "ordering": false,
                    "searching": false,
                    columns: [{
                            data: 'operation_date',
                            name: 'operation_date'
                        },
                        {
                            data: 'account_name',
                            name: 'account_name'
                        },
                        {
                            data: 'sub_type',
                            name: 'sub_type'
                        },
                        {
                            data: 'method',
                            name: 'TP.method'
                        },
                        {
                            data: 'payment_details',
                            name: 'payment_details',
                            searchable: false
                        },
                        {
                            data: 'credit',
                            name: 'amount'
                        },
                        {
                            data: 'balance',
                            name: 'balance'
                        },
                        {
                            data: 'total_balance',
                            name: 'total_balance'
                        },
                    ],
                    "fnDrawCallback": function(oSettings) {
                        __currency_convert_recursively($('#cash_flow_table'));
                    },
                    "footerCallback": function(row, data, start, end, display) {
                        var footer_total_credit = 0;

                        for (var r in data) {
                            footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(
                                data[r].credit).data('orig-value')) : 0;
                        }
                        $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
                    }
                });
            @endif

            $('#so_location').change(function() {
                sales_order_table.ajax.reload();
            });
            @if (!empty($common_settings['enable_purchase_order']))
                //Purchase table
                purchase_order_table = $('#purchase_order_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    aaSorting: [
                        [1, 'desc']
                    ],
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;

                            if ($('#po_location').length > 0) {
                                d.location_id = $('#po_location').val();
                            }
                        },
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'location_name',
                            name: 'BS.name'
                        },
                        {
                            data: 'name',
                            name: 'contacts.name'
                        },
                        {
                            data: 'status',
                            name: 'transactions.status'
                        },
                        {
                            data: 'po_qty_remaining',
                            name: 'po_qty_remaining',
                            "searchable": false
                        },
                        {
                            data: 'added_by',
                            name: 'u.first_name'
                        }
                    ]
                })

                $('#po_location').change(function() {
                    purchase_order_table.ajax.reload();
                });
            @endif

            @if (!empty($common_settings['enable_purchase_requisition']))
                //Purchase table
                purchase_requisition_table = $('#purchase_requisition_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    aaSorting: [
                        [1, 'desc']
                    ],
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;

                            if ($('#pr_location').length > 0) {
                                d.location_id = $('#pr_location').val();
                            }
                        },
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'location_name',
                            name: 'BS.name'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'delivery_date',
                            name: 'delivery_date'
                        },
                        {
                            data: 'added_by',
                            name: 'u.first_name'
                        },
                    ]
                })

                $('#pr_location').change(function() {
                    purchase_requisition_table.ajax.reload();
                });

                $(document).on('click', 'a.delete-purchase-requisition', function(e) {
                    e.preventDefault();
                    swal({
                        title: LANG.sure,
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then(willDelete => {
                        if (willDelete) {
                            var href = $(this).attr('href');
                            $.ajax({
                                method: 'DELETE',
                                url: href,
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        purchase_requisition_table.ajax.reload();
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                });
            @endif

        });
    </script>
    
@endsection