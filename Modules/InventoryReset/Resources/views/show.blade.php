@extends('layouts.app')
@section('title', __('inventoryreset::lang.reset_details'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        {{ __('inventoryreset::lang.reset_details') }} #{{ $resetLog->id }}
    </h1>

    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> {{ __('home.home') }}</a></li>
        <li><a href="{{ route('inventory-reset.index') }}">{{ __('inventoryreset::lang.inventory_reset') }}</a></li>
        <li class="active">{{ __('inventoryreset::lang.reset_details') }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- Back Button -->
        <div class="col-md-12">
            <a href="{{ route('inventory-reset.index') }}"
                class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white hover:tw-text-white margin-bottom">
                <i class="fa fa-arrow-left"></i> {{ __('inventoryreset::lang.back_to_dashboard') }}
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Reset Information -->
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-info-circle"></i> {{ __('inventoryreset::lang.reset_information') }}
                    </h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <td style="width: 40%;"><strong>{{ __('inventoryreset::lang.reset_id') }}:</strong></td>
                            <td>#{{ $resetLog->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.reset_type') }}:</strong></td>
                            <td>
                                <span
                                    class="label {{ $resetLog->reset_type === 'all_products' ? 'label-info' : 'label-warning' }}">
                                    {{ $resetLog->formatted_reset_type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.reset_mode') }}:</strong></td>
                            <td>
                                <span
                                    class="label {{ $resetLog->reset_mode === 'all_levels' ? 'label-info' : 'label-warning' }}">
                                    {{ $resetLog->formatted_reset_mode }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.operation_type') }}:</strong></td>
                            <td>
                                <span
                                    class="label {{ $resetLog->target_quantity == 0 ? 'label-info' : 'label-warning' }}">
                                    {{ $resetLog->formatted_target_quantity }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.status') }}:</strong></td>
                            <td>
                                <span class="label {{ $resetLog->status_badge_class }}">
                                    {{ __("inventoryreset::lang.{$resetLog->status}") }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.performed_by') }}:</strong></td>
                            <td>{{ $resetLog->user->first_name ?? __('inventoryreset::lang.not_available') }} {{ $resetLog->user->last_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.started_at') }}:</strong></td>
                            <td>{{ @format_datetime($resetLog->created_at) }}</td>
                        </tr>
                        @if($resetLog->completed_at)
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.completed_at') }}:</strong></td>
                            <td>{{ @format_datetime($resetLog->completed_at) }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.duration') }}:</strong></td>
                            <td>{{ $resetLog->created_at->diffForHumans($resetLog->completed_at, true) }}</td>
                        </tr>
                        @endif
                        @if($resetLog->location_id)
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.location') }}:</strong></td>
                            <td>{{ $resetLog->location->name ?? __('inventoryreset::lang.not_available') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.reason') }}:</strong></td>
                            <td>{{ $resetLog->reason }}</td>
                        </tr>
                        @if($resetLog->error_message)
                        <tr>
                            <td><strong>{{ __('inventoryreset::lang.error_message') }}:</strong></td>
                            <td class="text-red">{{ $resetLog->error_message }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Reset Statistics -->
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-chart-bar"></i> {{ __('inventoryreset::lang.reset_statistics') }}
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('inventoryreset::lang.items_processed') }}</span>
                                    <span class="info-box-number">{{ $resetLog->items_reset }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('inventoryreset::lang.total_quantity_reset')
                                        }}</span>
                                    <span class="info-box-number">{{
                                        @format_quantity($resetLog->resetItems->sum('quantity_before')) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('inventoryreset::lang.unique_products') }}</span>
                                    <span class="info-box-number">{{
                                        $resetLog->resetItems->unique('product_id')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="fa fa-map-marker"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('business.business_locations') }}</span>
                                    <span class="info-box-number">{{
                                        $resetLog->resetItems->unique('location_id')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Items Details -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-list"></i> {{ __('inventoryreset::lang.reset_items_details') }}
                    </h3>
                </div>
                <div class="box-body">
                    @if($resetLog->resetItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="reset-items-table">
                            <thead>
                                <tr>
                                    <th>{{ __('sale.product') }}</th>
                                    <th>{{ __('product.sku') }}</th>
                                    <th>{{ __('business.business_location') }}</th>
                                    <th class="text-center">{{ __('inventoryreset::lang.quantity_before') }}</th>
                                    <th class="text-center">{{ __('inventoryreset::lang.quantity_after') }}</th>
                                    <th class="text-center">{{ __('inventoryreset::lang.quantity_reset') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resetLog->resetItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name ?? __('inventoryreset::lang.not_available') }}</strong>
                                        @if($item->product && $item->product->type == 'variable')
                                        <br><small class="text-muted">{{ __('inventoryreset::lang.variable_product')
                                            }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->product->sku ?? __('inventoryreset::lang.not_available') }}</td>
                                    <td>{{ $item->location->name ?? __('inventoryreset::lang.not_available') }}</td>
                                    <td class="text-center">
                                        <span class="label label-primary">{{ @format_quantity($item->quantity_before) }}
                                            {{ $item->product->unit->short_name ?? '' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-default">{{ @format_quantity($item->quantity_after) }}
                                            {{ $item->product->unit->short_name ?? '' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-danger">-{{
                                            @format_quantity($item->quantity_difference) }} {{
                                            $item->product->unit->short_name ?? '' }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center">
                        <i class="fa fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                        <h4>{{ __('lang_v1.no_data') }}</h4>
                        <p class="text-muted">{{ __('purchase.no_records_found') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
    // Initialize DataTable if there are items
    @if($resetLog->resetItems->count() > 0)
    $('#reset-items-table').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "targets": [3, 4, 5], "className": "text-center" }
        ]
    });
    @endif

    // Auto refresh if still processing
    @if($resetLog->status === 'processing')
    setTimeout(function() {
        location.reload();
    }, 5000);
    @endif
});
</script>
@endsection