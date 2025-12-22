@extends('layouts.app')

@section('title', 'Accounting Mappings Setup')

@section('content')
@include('accounting::layouts.nav')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Accounting Mappings Setup</h1>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status')['msg'] ?? session('status') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            {!! Form::open(['method' => 'get', 'id' => 'location-filter-form', 'class' => 'form-inline']) !!}
                                <div class="form-group">
                                    {!! Form::label('location_id', __('report.location') . ':', ['class' => 'control-label']) !!}
                                    {!! Form::select('location_id', $locations, $locationId, ['class' => 'form-control', 'id' => 'location_id']) !!}
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-sm-6 text-right">
                            <button type="submit" form="mapping-form" class="btn btn-primary">
                                <i class="fas fa-save"></i> @lang('messages.save')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-clipboard-check"></i> Coverage</h3>
                </div>
                <div class="box-body">
                    <p class="text-muted">Wave 1 operations</p>
                    <ul class="list-unstyled">
                        @foreach($coverage as $operation => $summary)
                            @php
                                $missingCount = count($summary['missing']);
                                $labelClass = $missingCount ? 'label-danger' : 'label-success';
                                $missingLabel = __('accounting::lang.missing_keys') !== 'accounting::lang.missing_keys'
                                    ? __('accounting::lang.missing_keys')
                                    : __('lang_v1.missing');
                            @endphp
                            <li class="m-b-5">
                                <strong>{{ ucwords(str_replace('_', ' ', $operation)) }}</strong>
                                <span class="label {{ $labelClass }}">{{ $missingCount }} {{ $missingLabel }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle text-yellow"></i>
                        Required keys are highlighted below. Legacy and payment defaults are used as fallback where available.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="nav-tabs-custom">
                @php
                    $tabTitles = [
                        'core' => 'Core',
                        'payments' => 'Payments',
                        'sales' => 'Sales',
                        'purchases' => 'Purchases',
                        'expenses' => 'Expenses',
                    ];
                @endphp
                <ul class="nav nav-tabs">
                    @foreach($groups as $group => $keys)
                        <li class="{{ $loop->first ? 'active' : '' }}">
                            <a href="#tab_{{ $group }}" data-toggle="tab">{{ $tabTitles[$group] ?? ucfirst($group) }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    {!! Form::open(['id' => 'mapping-form', 'method' => 'post', 'action' => action([\Modules\Accounting\Http\Controllers\MappingController::class, 'store'])]) !!}
                        @csrf
                        {!! Form::hidden('location_id', $locationId) !!}
                        @foreach($groups as $group => $keys)
                            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_{{ $group }}">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th width="20%">Mapping Key</th>
                                                <th>Description</th>
                                                <th width="30%">Account</th>
                                                <th width="20%">Fallback</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($keys as $mappingKey)
                                                @php
                                                    $selectedId = $selectedMappings[$mappingKey->key] ?? null;
                                                    $selectedLabel = $selectedId ? ($accountsById[$selectedId] ?? '') : '';
                                                    $fallbackId = $fallbackAccounts[$mappingKey->key] ?? null;
                                                    $fallbackLabel = $fallbackId ? ($accountsById[$fallbackId] ?? '') : '';
                                                    $isRequired = isset($requiredKeys[$mappingKey->key]);
                                                    $isMissing = isset($missingKeys[$mappingKey->key]);
                                                @endphp
                                                <tr class="{{ $isMissing ? 'danger' : '' }}">
                                                    <td>
                                                        <strong>{{ $mappingKey->key }}</strong>
                                                        @if($isRequired)
                                                            <span class="label label-info">@lang('messages.required')</span>
                                                        @endif
                                                        @if($isMissing)
                                                            <i class="fas fa-exclamation-circle text-danger" title="Mapping required"></i>
                                                        @endif
                                                    </td>
                                                    <td>{{ $mappingKey->label }}</td>
                                                    <td>
                                                        <select
                                                            name="mappings[{{ $mappingKey->key }}]"
                                                            class="form-control mapping-account"
                                                            data-placeholder="{{ __('messages.please_select') }}"
                                                        >
                                                            @if($selectedId)
                                                                <option value="{{ $selectedId }}" selected>{{ $selectedLabel }}</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>
                                                        @if($fallbackLabel)
                                                            <span class="label label-default">{{ $fallbackLabel }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            {{ __('accounting::lang.no_data_available') !== 'accounting::lang.no_data_available' ? __('accounting::lang.no_data_available') : __('lang_v1.no_data_available') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        $('#location_id').on('change', function () {
            $('#location-filter-form').submit();
        });

        $('.mapping-account').select2({
            width: '100%',
            placeholder: function(){
                return $(this).data('placeholder');
            },
            allowClear: true,
            ajax: {
                url: "{{ action([\Modules\Accounting\Http\Controllers\MappingController::class, 'searchAccounts']) }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return data;
                }
            },
            minimumInputLength: 1
        });
    });
</script>
@endsection
