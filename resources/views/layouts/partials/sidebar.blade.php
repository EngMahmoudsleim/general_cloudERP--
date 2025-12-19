<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden tw-h-full tw-bg-white tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0">

    <!-- sidebar: style can be found in sidebar.less -->

    {{-- <a href="{{route('home')}}" class="logo">
		<span class="logo-lg">{{ Session::get('business.name') }}</span>
	</a> --}}

    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-gap-3 tw-justify-start tw-w-full tw-border-r tw-h-16 tw-px-5 tw-bg-white tw-shrink-0 tw-border-b tw-border-gray-200">
        <span class="sidebar-brand-mark" aria-hidden="true">
            <span class="sidebar-brand-glow"></span>
            <span class="sidebar-brand-text">{{ \Illuminate\Support\Str::upper(mb_substr(Session::get('business.name'), 0, 1)) }}</span>
        </span>
        <div class="tw-flex tw-flex-col tw-leading-tight">
            <span class="tw-text-base tw-font-bold tw-text-slate-900">{{ Session::get('business.name') }}</span>
            <span class="tw-text-xs tw-font-medium tw-text-emerald-600 tw-inline-flex tw-items-center tw-gap-1">
                <span class="tw-inline-block tw-w-2 tw-h-2 tw-rounded-full tw-bg-emerald-500"></span>
                Online
            </span>
        </div>
    </a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}

    <!-- /.sidebar-menu -->
    <!-- /.sidebar -->
</aside>