<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="{{ asset('css/tailwind/app.css?v='.$asset_v) }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
        :root {
                --app-font-family: 'Cairo', 'Inter', system-ui, -apple-system, sans-serif;
        }

        html,
        body,
        .tw-font-sans,
        .sidebar-menu,
        .side-bar,
        input,
        select,
        textarea,
        button {
                font-family: var(--app-font-family) !important;
        }

        body {
                background-color: #f8fafc;
                color: #0f172a;
        }

        .sidebar-nav-icon {
                --sidebar-icon-size: 2.75rem;
                width: var(--sidebar-icon-size);
                height: var(--sidebar-icon-size);
                border-radius: 0.95rem;
                background: linear-gradient(145deg, #eef2ff, #e0f2fe);
                color: rgb(30, 64, 175);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.15), 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        .sidebar-nav-icon--muted {
                --sidebar-icon-size: 2.35rem;
                background: linear-gradient(135deg, #f8fafc, #e2e8f0);
                color: #0f172a;
                box-shadow: inset 0 0 0 1px rgba(17, 24, 39, 0.06);
        }

        .sidebar-nav-icon i,
        .sidebar-nav-icon svg {
                width: 1.15rem;
                height: 1.15rem;
        }

        .sidebar-link {
                border-radius: 0.9rem;
                position: relative;
        }

        .sidebar-link:after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: inherit;
                background: linear-gradient(120deg, rgba(59, 130, 246, 0.12), rgba(16, 185, 129, 0.12));
                opacity: 0;
                transition: opacity 0.2s ease, transform 0.2s ease;
                transform: scale(0.98);
                z-index: -1;
        }

        .sidebar-link:hover:after,
        .sidebar-link:focus:after,
        .sidebar-link.active:after {
                opacity: 1;
                transform: scale(1);
        }

        .sidebar-link.active {
                color: #1d4ed8;
                background-color: #eef2ff;
                box-shadow: 0 10px 25px rgba(59, 130, 246, 0.12), inset 0 0 0 1px rgba(59, 130, 246, 0.18);
        }

        .dashboard-hero {
                background: radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.12), transparent 28%),
                        radial-gradient(circle at 80% 10%, rgba(16, 185, 129, 0.14), transparent 25%),
                        linear-gradient(135deg, #0ea5e9, #312e81);
                border-radius: 1.25rem;
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
                overflow: hidden;
        }

        .dashboard-hero::after {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 60% 60%, rgba(255, 255, 255, 0.12), transparent 35%);
                pointer-events: none;
        }

        .stat-card {
                position: relative;
                overflow: hidden;
                border-radius: 1.1rem;
                border: 1px solid #e2e8f0;
                background: linear-gradient(155deg, #ffffff, #f8fafc);
                box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
                transition: transform 0.22s ease, box-shadow 0.22s ease;
                min-height: 9rem;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, rgba(79, 70, 229, 0.06), transparent 35%);
            pointer-events: none;
        }

        .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }

        .stat-card__icon {
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 1.1rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04), 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        .quick-action {
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 1.1rem;
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.08));
                backdrop-filter: blur(10px);
                transition: transform 0.2s ease, border-color 0.18s ease, background 0.2s ease;
        }

        .quick-action:hover {
                border-color: rgba(255, 255, 255, 0.35);
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.12));
                transform: translateY(-3px);
        }

        .quick-action__icon {
                width: 3rem;
                height: 3rem;
                border-radius: 0.95rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #ffffff;
                color: #1d4ed8;
                box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.12), 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .dashboard-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 1.25rem;
        }

        .sidebar-brand-mark {
                position: relative;
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.9rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(145deg, #0ea5e9, #312e81);
                color: #fff;
                font-weight: 800;
                letter-spacing: 0.5px;
                overflow: hidden;
                box-shadow: 0 10px 25px rgba(49, 46, 129, 0.25);
        }

        .sidebar-brand-glow {
                position: absolute;
                width: 140%;
                height: 140%;
                background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.25), transparent 60%);
                inset: -20%;
        }

        .sidebar-brand-text {
                position: relative;
                z-index: 1;
        }

        .sidebar-child-bullet {
                width: 0.75rem;
                height: 0.75rem;
                border-radius: 0.4rem;
                background: linear-gradient(135deg, #1d4ed8, #22d3ee);
                box-shadow: 0 5px 12px rgba(37, 99, 235, 0.25);
                flex-shrink: 0;
        }

        .date-filter-btn {
                min-width: 0;
                padding-left: 0.75rem;
                padding-right: 0.85rem;
                border: 1px solid rgba(15, 23, 42, 0.08);
        }

        @media (max-width: 640px) {
                .sidebar-nav-icon {
                        --sidebar-icon-size: 2.35rem;
                        border-radius: 0.9rem;
                }

                .dashboard-grid {
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                }
        }

        @media (min-width: 1280px) {
                .dashboard-grid {
                        grid-template-columns: repeat(4, minmax(0, 1fr));
                        gap: 1.5rem;
                }
        }

        .sidebar-accordion summary::-webkit-details-marker {
                display: none;
        }

        .sidebar-accordion summary {
                cursor: pointer;
        }

        .sidebar-accordion[open] > summary .sidebar-link {
                background-color: #eef2ff;
        }

        .sidebar-sublink {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                width: 100%;
                padding: 0.55rem 0.75rem;
                border-radius: 0.8rem;
                font-weight: 600;
                color: #0f172a;
                transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sidebar-sublink:hover,
        .sidebar-sublink:focus {
                background-color: #f8fafc;
                color: #1d4ed8;
        }

        /*
        * Pattern lock css
        * Pattern direction
        * http://ignitersworld.com/lab/patternLock.html
        */
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif