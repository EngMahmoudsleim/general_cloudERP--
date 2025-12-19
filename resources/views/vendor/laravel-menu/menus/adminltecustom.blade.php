<nav class="tw-flex-1 tw-overflow-y-auto tw-p-4" aria-label="Sidebar navigation">
    <ul class="tw-space-y-1 sidebar-menu">
        @foreach($items as $item)
            @php
                $iconData = isset($item->data['icon']) ? $item->data['icon'] : null;
                $iconAttr = method_exists($item, 'attr') ? $item->attr('icon') : null;
                $icon = $iconData ?? $iconAttr ?? ($item->icon ?? '');
                $hasChildren = $item->hasChildren();
                $isActive = $item->isActive || (method_exists($item, 'hasActiveOnChild') && $item->hasActiveOnChild());
            @endphp
            <li class="tw-list-none">
                @if($hasChildren && count($item->children()) > 0)
                    <details class="sidebar-accordion tw-group tw-w-full" @if($isActive) open @endif>
                        <summary class="tw-w-full">
                            <span class="sidebar-link tw-flex tw-items-center tw-gap-3 tw-w-full tw-px-3 tw-py-2 tw-font-semibold tw-text-slate-800 hover:tw-text-slate-900">
                                <span class="sidebar-nav-icon tw-text-slate-700">
                                    {!! $icon !!}
                                </span>
                                <span class="tw-flex-1 tw-truncate">{{ $item->title }}</span>
                                <svg aria-hidden="true" class="tw-size-4 tw-text-slate-500 tw-transition group-open:tw-rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M6 9l6 6l6-6" />
                                </svg>
                            </span>
                        </summary>
                        <ul class="tw-mt-1 tw-space-y-0.5 tw-pl-4 tw-border-l tw-border-gray-200">
                            @foreach($item->children() as $child)
                                @php
                                    $childActive = $child->isActive || $child->hasActiveOnChild();
                                @endphp
                                <li>
                                    <a href="{{ $child->url() }}"
                                       class="sidebar-sublink {{ $childActive ? 'sidebar-link active' : '' }}">
                                        <span class="sidebar-child-bullet"></span>
                                        <span class="tw-truncate">{{ $child->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @else
                    <a href="{{ $item->url() }}"
                       class="sidebar-link tw-flex tw-items-center tw-gap-3 tw-w-full tw-px-3 tw-py-2 tw-font-semibold tw-text-slate-800 hover:tw-text-slate-900 {{ $isActive ? 'active' : '' }}">
                        <span class="sidebar-nav-icon tw-text-slate-700">
                            {!! $icon !!}
                        </span>
                        <span class="tw-flex-1 tw-truncate">{{ $item->title }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>