@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <x-common.page-breadcrumb pageTitle="General Reports" />
        <p class="text-sm text-gray-500 mt-1">Detailed performance metrics and client distribution analysis.</p>
    </div>

    <!-- Date Range Filter -->
    <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-3">
        <div class="w-72">
            <x-form.date-picker 
                name="date_range" 
                mode="range" 
                placeholder="Select date range"
                :defaultDate="[$startDate, $endDate]"
            />
        </div>
        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors" title="Apply filter">
            Filter
        </button>
        @if(request('date_range'))
            <a href="{{ route('reports.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors" title="Clear filter">
                Clear
            </a>
        @endif
    </form>
</div>

<div class="grid grid-cols-12 gap-4 md:gap-6">
    <!-- Stats Row -->
    <div class="col-span-12 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalClients }}</h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients in Period</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-500/10 text-success-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $statusDistribution->whereIn('status', ['Closed Won', 'Converted'])->sum('total') }}
                </h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversions</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $totalClients > 0 ? round(($statusDistribution->whereIn('status', ['Closed Won', 'Converted'])->sum('total') / $totalClients) * 100, 1) : 0 }}%
                </h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Rate</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="col-span-12 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <!-- Status Distribution (Apex Donut/Pie) -->
        <div class="animate-fade-in rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]" style="animation-delay: 0.1s;">
            <h3 class="mb-6 text-lg font-bold text-gray-800 dark:text-white">Status Distribution</h3>
            <div id="status_distribution_chart" class="min-h-[400px]"></div>
        </div>

        <!-- Sales by Technology (Custom Component) -->
        <div class="animate-fade-in rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]" style="animation-delay: 0.2s;">
            @php
                $colors = [
                    'bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 
                    'bg-violet-500', 'bg-cyan-500', 'bg-orange-500', 'bg-lime-500', 
                    'bg-fuchsia-500', 'bg-indigo-500'
                ];
                $totalTechClients = $technologyDistribution->sum('total');
            @endphp
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Sales by Technology</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Technology performance overview</p>
            
            <div class="flex items-baseline gap-2 mb-6">
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $technologyDistribution->sum('total') }}</span>
                <span class="flex items-center text-sm font-medium text-success-500">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    8.4%
                </span>
                <span class="text-sm text-gray-400">Increased vs last week</span>
            </div>

            <!-- Segmented Tick Bar -->
            <div class="flex h-6 w-full gap-1 mb-6">
                @php
                    $totalTicks = 40;
                    $currentTick = 0;
                @endphp
                @if($totalClients > 0)
                    @foreach($technologyDistribution as $tech)
                        @php
                            $percent = $tech->total / $totalClients;
                            $ticksForThisTech = round($percent * $totalTicks);
                        @endphp
                        @for($i = 0; $i < $ticksForThisTech; $i++)
                            @if($currentTick < $totalTicks)
                                <div class="flex-1 h-full rounded-sm {{ $colors[$loop->index % count($colors)] }} animate-tick" 
                                     style="animation-delay: {{ 0.2 + ($currentTick * 0.015) }}s"></div>
                                @php $currentTick++; @endphp
                            @endif
                        @endfor
                    @endforeach
                @endif
                
                {{-- The remaining space will now correctly show as a gray line --}}
                @for($i = $currentTick; $i < $totalTicks; $i++)
                    <div class="flex-1 h-full rounded-sm bg-gray-200 dark:bg-gray-700 animate-tick" 
                         style="animation-delay: {{ 0.2 + ($i * 0.015) }}s"></div>
                @endfor
            </div>

            <!-- Legend and Table -->
            <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-4">
                <div class="flex flex-wrap gap-4 mb-4 pr-2">
                    @foreach($technologyDistribution as $tech)
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $colors[$loop->index % count($colors)] }}"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $tech->technology }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-4 custom-scrollbar pr-4" style="max-height: 170px; overflow-y: auto; overflow-x: hidden;">
                    {{-- Table Header --}}
                    <div class="flex items-center text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-50 dark:border-gray-800 pb-3 sticky top-0 bg-white dark:bg-gray-900 z-10 pr-2">
                        <span class="flex-1">Technologies</span>
                        <span class="w-20 text-center">Metric</span>
                        <span class="w-24 text-right">Total</span>
                    </div>

                    {{-- Table Body --}}
                    @foreach($technologyDistribution as $index => $tech)
                    <div class="flex items-center py-1 group country-item-pulse pr-2">
                        <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $tech->technology }}</span>
                        <span class="w-20 text-center text-sm font-bold text-gray-800 dark:text-white">{{ $tech->total }}</span>
                        <div class="w-24 text-right flex items-center justify-end gap-1">
                            @if(rand(0,1))
                                <svg class="w-3 h-3 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                <span class="text-xs font-bold text-success-500">
                                    {{ $totalTechClients > 0 ? round(($tech->total / $totalTechClients) * 100, 1) : 0 }}%
                                </span>
                            @else
                                <svg class="w-3 h-3 text-danger-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                <span class="text-xs font-bold text-danger-500">
                                    {{ $totalTechClients > 0 ? round(($tech->total / $totalTechClients) * 100, 1) : 0 }}%
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Map Row -->
    <div class="col-span-12 animate-fade-in" style="animation-delay: 0.3s;">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Clients by Country</h3>
                    <p class="text-sm text-gray-500">Global client distribution overview</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div id="regions_div" style="width: 100%; height: 400px;"></div>
                </div>
                <div class="relative overflow-hidden auto-scroll-mask" style="height: 400px !important; overflow: hidden !important;">
                    <div class="{{ $countryDistribution->count() > 6 ? 'animate-auto-scroll' : '' }}">
                        @php $totalCountryClients = $countryDistribution->sum('total'); @endphp

                        @if($countryDistribution->count() > 0)
                            {{-- First set of items --}}
                            @foreach($countryDistribution as $country)
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $country->country }}</span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $country->total }}</span>
                                            <span class="text-xs text-gray-500 w-10 text-right">{{ $totalCountryClients > 0 ? round(($country->total / $totalCountryClients) * 100) : 0 }}%</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                        <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ $totalCountryClients > 0 ? ($country->total / $totalCountryClients) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                            
                            {{-- Duplicate for seamless loop if scrolling --}}
                            @if($countryDistribution->count() > 6)
                                @foreach($countryDistribution as $country)
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $country->country }}</span>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $country->total }}</span>
                                                <span class="text-xs text-gray-500 w-10 text-right">{{ $totalCountryClients > 0 ? round(($country->total / $totalCountryClients) * 100) : 0 }}%</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                            <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ $totalCountryClients > 0 ? ($country->total / $totalCountryClients) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">No location data found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    #status_pie_3d rect, #status_pie_3d path, #regions_div path {
        cursor: pointer !important;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }

    /* Auto-scrolling List Styles */
    @keyframes auto-scroll-vertical {
        0% { transform: translateY(0); }
        100% { transform: translateY(-50%); }
    }

    .animate-auto-scroll {
        animation: auto-scroll-vertical 30s linear infinite;
    }

    .animate-auto-scroll:hover {
        animation-play-state: paused;
    }

    .auto-scroll-mask {
        position: relative;
        mask-image: linear-gradient(to bottom, transparent, black 5%, black 95%, transparent);
        -webkit-mask-image: linear-gradient(to bottom, transparent, black 5%, black 95%, transparent);
    }

    /* Page Load Animations */
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .animate-fade-in {
        opacity: 0;
        animation: fade-in-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes tick-pop {
        0% { transform: scaleY(0); opacity: 0; }
        100% { transform: scaleY(1); opacity: 1; }
    }

    .animate-tick {
        animation: tick-pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        transform-origin: bottom;
        opacity: 0;
    }

    .country-item-pulse {
        transition: transform 0.2s ease;
    }
    .country-item-pulse:hover {
        transform: translateX(4px);
    }
</style>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:["geochart"]});
    google.charts.setOnLoadCallback(drawMap);

    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#E2E8F0' : '#64748B';
        const headingColor = isDark ? '#FFFFFF' : '#1E293B';

        // 1. Status Distribution (ApexCharts)
        var statusOptions = {
            series: {!! json_encode($statusDistribution->pluck('total')) !!},
            chart: {
                type: 'donut',
                height: 400,
                fontFamily: 'Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 1200,
                    animateGradually: { enabled: true, delay: 150 },
                    dynamicAnimation: { enabled: true, speed: 350 }
                }
            },
            labels: {!! json_encode($statusDistribution->pluck('status')) !!},
            colors: ['#3C50E0', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#3B82F6', '#14B8A6', '#F97316', '#0EA5E9', '#6366F1'],
            legend: {
                position: 'bottom',
                fontFamily: 'Inter, sans-serif',
                fontWeight: 500,
                fontSize: '13px',
                labels: { colors: textColor },
                itemMargin: { horizontal: 10, vertical: 5 }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '16px', fontWeight: 600, color: textColor },
                            value: { show: true, fontSize: '24px', fontWeight: 700, color: headingColor, formatter: (val) => val },
                            total: {
                                show: true,
                                label: 'Total',
                                color: textColor,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            },
            stroke: { show: false },
            dataLabels: { enabled: false },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                fillSeriesColor: false,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                },
                y: {
                    formatter: function (val) {
                        return val + " Clients"
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 350 },
                    legend: { position: 'bottom' }
                }
            }]
        };

        var statusChart = new ApexCharts(document.querySelector("#status_distribution_chart"), statusOptions);
        statusChart.render();

        // Theme Toggle Listener to refresh charts
        window.addEventListener('theme-changed', function() {
            const newIsDark = document.documentElement.classList.contains('dark');
            const newTextColor = newIsDark ? '#E2E8F0' : '#64748B';
            const newHeadingColor = newIsDark ? '#FFFFFF' : '#1E293B';
            
            statusChart.updateOptions({
                legend: { labels: { colors: newTextColor } },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                name: { color: newTextColor },
                                value: { color: newHeadingColor },
                                total: { color: newTextColor }
                            }
                        }
                    }
                },
                tooltip: { theme: newIsDark ? 'dark' : 'light' }
            });
        });
    });

    function drawMap() {
        // 2. GeoChart (Map) remains in Google Charts
        var countryData = google.visualization.arrayToDataTable([
            ['Country', 'Clients'],
            @foreach($countryDistribution as $country)
                ['{{ $country->map_name }}', {{ $country->total }}],
            @endforeach
        ]);

        var geochart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        function render() {
            var isMobile = window.innerWidth < 768;
            var countryOptions = {
                colorAxis: { colors: ['#e0e7ff', '#3C50E0'] },
                backgroundColor: 'transparent',
                datalessRegionColor: '#f8fafc',
                defaultColor: '#f1f5f9',
                width: '100%',
                keepAspectRatio: true
            };

            var chartHeight = isMobile ? 300 : 400;
            document.getElementById('regions_div').style.height = chartHeight + 'px';
            geochart.draw(countryData, countryOptions);
        }

        render();

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(render, 250);
        });
    }
</script>
@endpush
