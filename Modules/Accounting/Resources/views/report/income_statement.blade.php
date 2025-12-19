@extends('layouts.app')

@section('title', __('accounting::lang.income_statement'))

@section('content')

@include('accounting::layouts.nav')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'accounting::lang.income_statement' )</h1>
</section>

<section class="content">

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
            {!! Form::text('date_range_filter', null,
                ['placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control', 'readonly', 'id' => 'date_range_filter']); !!}
        </div>
    </div>

    <div class="col-md-10 col-md-offset-1">
        <div class="box box-warning">
            <div class="box-header with-border text-center">
                <h2 class="box-title">@lang( 'accounting::lang.income_statement')</h2>
                <p>{{@format_date($start_date)}} ~ {{@format_date($end_date)}}</p>
            </div>

            <div class="box-body">
                
                {{-- 📊 الإيرادات (Revenues) --}}
                <h3 class="text-primary"><i class="fas fa-dollar-sign"></i> @lang('accounting::lang.revenues')</h3>
                <table class="table table-bordered table-condensed">
                    <tbody>
                        {{-- إجمالي المبيعات --}}
                        @if(!empty($sales_details))
                            <tr class="bg-light-blue">
                                <th colspan="2">@lang('accounting::lang.gross_sales')</th>
                            </tr>
                            @foreach($sales_details as $sub_type => $data)
                                @foreach($data['accounts'] as $account)
                                    <tr>
                                        <td class="col-md-8" style="padding-left: 30px;">{{$account->name}}</td>
                                        <td class="col-md-4 text-right">@format_currency($account->balance)</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr class="info">
                                <th>@lang('accounting::lang.total_gross_sales')</th>
                                <th class="text-right">@format_currency($gross_sales)</th>
                            </tr>
                        @endif

                        {{-- مرتجعات وخصومات المبيعات --}}
                        @if(!empty($sales_returns_details) && $sales_returns != 0)
                            <tr class="bg-light-red">
                                <th colspan="2">@lang('accounting::lang.sales_returns_allowances')</th>
                            </tr>
                            @foreach($sales_returns_details as $sub_type => $data)
                                @foreach($data['accounts'] as $account)
                                    <tr>
                                        <td style="padding-left: 30px;">{{$account->name}}</td>
                                        <td class="text-right text-danger">(@format_currency(abs($account->balance)))</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr class="danger">
                                <th>@lang('accounting::lang.total_sales_returns')</th>
                                <th class="text-right text-danger">(@format_currency(abs($sales_returns)))</th>
                            </tr>
                        @endif

                        {{-- صافي المبيعات --}}
                        <tr class="success" style="font-size: 16px;">
                            <th><strong>@lang('accounting::lang.net_sales')</strong></th>
                            <th class="text-right"><strong>@format_currency($net_sales)</strong></th>
                        </tr>
                    </tbody>
                </table>

                {{-- 💰 تكلفة البضاعة المباعة (COGS) --}}
                <h3 class="text-warning"><i class="fas fa-boxes"></i> @lang('accounting::lang.cost_of_goods_sold')</h3>
                <table class="table table-bordered table-condensed">
                    <tbody>
                        @if(!empty($cogs_details))
                            @foreach($cogs_details as $sub_type => $data)
                                @foreach($data['accounts'] as $account)
                                    <tr>
                                        <td class="col-md-8" style="padding-left: 30px;">{{$account->name}}</td>
                                        <td class="col-md-4 text-right">@format_currency($account->balance)</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif

                        {{-- مرتجعات المشتريات --}}
                        @if(!empty($purchase_returns_details) && $purchase_returns != 0)
                            <tr>
                                <td style="padding-left: 30px;" class="text-success">@lang('accounting::lang.purchase_returns')</td>
                                <td class="text-right text-success">(@format_currency(abs($purchase_returns)))</td>
                            </tr>
                        @endif

                        <tr class="warning">
                            <th>@lang('accounting::lang.total_cogs')</th>
                            <th class="text-right">@format_currency($net_cogs)</th>
                        </tr>

                        {{-- إجمالي الربح --}}
                        <tr class="success" style="font-size: 17px;">
                            <th><strong>@lang('accounting::lang.gross_profit')</strong></th>
                            <th class="text-right"><strong>@format_currency($gross_profit)</strong></th>
                        </tr>
                    </tbody>
                </table>

                {{-- 📉 المصروفات التشغيلية (Operating Expenses) --}}
                <h3 class="text-danger"><i class="fas fa-file-invoice-dollar"></i> @lang('accounting::lang.operating_expenses')</h3>
                <table class="table table-bordered table-condensed">
                    <tbody>
                        @if(!empty($operating_expense_details))
                            @foreach($operating_expense_details as $sub_type => $data)
                                <tr class="active">
                                    <th colspan="2">{{$sub_type}}</th>
                                </tr>
                                @foreach($data['accounts'] as $account)
                                    <tr>
                                        <td style="padding-left: 30px;">{{$account->name}}</td>
                                        <td class="text-right">@format_currency($account->balance)</td>
                                    </tr>
                                @endforeach
                                <tr class="info">
                                    <th style="padding-left: 15px;">@lang('accounting::lang.total') ({{$sub_type}})</th>
                                    <th class="text-right">@format_currency($data['total'])</th>
                                </tr>
                            @endforeach
                        @endif

                        <tr class="danger">
                            <th>@lang('accounting::lang.total_operating_expenses')</th>
                            <th class="text-right">@format_currency($operating_expenses)</th>
                        </tr>

                        {{-- الربح التشغيلي --}}
                        <tr class="primary" style="font-size: 17px;">
                            <th><strong>@lang('accounting::lang.operating_profit')</strong></th>
                            <th class="text-right"><strong>@format_currency($operating_profit)</strong></th>
                        </tr>
                    </tbody>
                </table>

                {{-- ➕➖ الإيرادات والمصروفات الأخرى --}}
                @if(!empty($other_income_details) || !empty($other_expense_details))
                    <h3><i class="fas fa-exchange-alt"></i> @lang('accounting::lang.other_income_expenses')</h3>
                    <table class="table table-bordered table-condensed">
                        <tbody>
                            {{-- الإيرادات الأخرى --}}
                            @if(!empty($other_income_details))
                                <tr class="bg-light-blue">
                                    <th colspan="2">@lang('accounting::lang.other_income')</th>
                                </tr>
                                @foreach($other_income_details as $sub_type => $data)
                                    @foreach($data['accounts'] as $account)
                                        <tr>
                                            <td style="padding-left: 30px;">{{$account->name}}</td>
                                            <td class="text-right">@format_currency($account->balance)</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="info">
                                    <th>@lang('accounting::lang.total_other_income')</th>
                                    <th class="text-right">@format_currency($other_income)</th>
                                </tr>
                            @endif

                            {{-- المصروفات الأخرى --}}
                            @if(!empty($other_expense_details))
                                <tr class="bg-light-red">
                                    <th colspan="2">@lang('accounting::lang.other_expenses')</th>
                                </tr>
                                @foreach($other_expense_details as $sub_type => $data)
                                    @foreach($data['accounts'] as $account)
                                        <tr>
                                            <td style="padding-left: 30px;">{{$account->name}}</td>
                                            <td class="text-right">@format_currency($account->balance)</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="danger">
                                    <th>@lang('accounting::lang.total_other_expenses')</th>
                                    <th class="text-right">@format_currency($other_expenses)</th>
                                </tr>
                            @endif

                            {{-- صافي الربح قبل الضريبة --}}
                            <tr class="warning" style="font-size: 16px;">
                                <th><strong>@lang('accounting::lang.profit_before_tax')</strong></th>
                                <th class="text-right"><strong>@format_currency($profit_before_tax)</strong></th>
                            </tr>
                        </tbody>
                    </table>
                @endif

                {{-- 💵 ضريبة الدخل --}}
                @if(!empty($income_tax_details) && $income_tax != 0)
                    <table class="table table-bordered table-condensed">
                        <tbody>
                            <tr>
                                <td class="col-md-8">@lang('accounting::lang.income_tax_expense')</td>
                                <td class="col-md-4 text-right text-danger">@format_currency($income_tax)</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                {{-- 🎯 صافي الربح --}}
                <div class="text-center" style="margin-top: 20px; padding: 20px; background-color: #f0f8ff; border-radius: 10px;">
                    <h2 style="margin: 0;">
                        <strong>@lang('accounting::lang.net_profit'):</strong>
                        <span class="label @if($net_profit >= 0) label-success @else label-danger @endif" 
                              style="font-size: 24px; padding: 10px 20px;">
                            @format_currency($net_profit)
                        </span>
                    </h2>
                    @if($net_sales > 0)
                        <p style="margin-top: 10px; color: #666;">
                            <small>هامش الربح الصافي: {{ number_format(($net_profit / $net_sales) * 100, 2) }}%</small>
                        </p>
                    @endif
                </div>

            </div>

        </div>
    </div>

</section>

@stop

@section('javascript')

<script type="text/javascript">
    $(document).ready(function(){

        dateRangeSettings.startDate = moment('{{$start_date}}');
        dateRangeSettings.endDate = moment('{{$end_date}}');

        $('#date_range_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                apply_filter();
            }
        );
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range_filter').val('');
            apply_filter();
        });

        function apply_filter(){
            var start = '';
            var end = '';

            if ($('#date_range_filter').val()) {
                start = $('input#date_range_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                end = $('input#date_range_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }

            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('start_date', start);
            urlParams.set('end_date', end);
            window.location.search = urlParams;
        }
    });

</script>

@stop