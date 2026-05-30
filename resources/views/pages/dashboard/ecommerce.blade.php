@extends('layouts.app')

@section('content')
  <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
          <x-common.page-breadcrumb pageTitle="CRM Dashboard" />
          <p class="text-sm text-gray-500 mt-1">Real-time insights into your sales performance and lead acquisition.</p>
      </div>

      <!-- Filter Dropdown -->
      <div x-data="{ open: false }" class="relative">
          <button @click="open = !open" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>{{ ucfirst($filter) }}</span>
              <svg :class="open ? 'rotate-180' : ''" class="transition-transform" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-40 rounded-xl border border-gray-100 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-900">
              <a href="?filter=today" class="flex w-full px-3 py-2 text-xs font-medium {{ $filter === 'today' ? 'bg-brand-50 text-brand-500' : 'text-gray-500' }} rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">Today</a>
              <a href="?filter=week" class="flex w-full px-3 py-2 text-xs font-medium {{ $filter === 'week' ? 'bg-brand-50 text-brand-500' : 'text-gray-500' }} rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">This Week</a>
              <a href="?filter=month" class="flex w-full px-3 py-2 text-xs font-medium {{ $filter === 'month' ? 'bg-brand-50 text-brand-500' : 'text-gray-500' }} rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">This Month</a>
          </div>
      </div>
  </div>

  <!-- Dashboard Grid -->
  <div class="grid grid-cols-12 gap-4 md:gap-6">
    
    <!-- Row 1: Stats Cards -->
    <div class="col-span-12 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 lg:gap-6">
        <!-- New Leads -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['new_leads'] }}</h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Lead</p>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Client</p>
            </div>
        </div>

        <!-- Active Clients -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500/10 text-orange-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['active'] }}</h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Client</p>
            </div>
        </div>

        <!-- Closed Won -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-500/10 text-success-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['closed'] }}</h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Close Client</p>
            </div>
        </div>
    </div>

    <!-- Row 2: Charts (8/4 Layout) -->
    <div class="col-span-12 lg:col-span-8">
        <div class="h-full rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Lead Acquisition Trends</h3>
                    <p class="text-sm text-gray-500 mt-1">Tracking daily lead growth over the selected period.</p>
                </div>
            </div>
            <div id="leadsTrendChart" class="min-h-[350px] w-full"></div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4">
        <div class="h-full rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-bold text-gray-800 dark:text-white">Pipeline Status</h3>
            <div id="pipelineStatusChart" class="min-h-[350px] w-full"></div>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->role !== 'employee')
    <!-- Row 3: Team Stats -->
    <div class="col-span-12">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-bold text-gray-800 dark:text-white">Team Wise Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($userPerformance as $staff)
                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-500/10 text-brand-500 font-bold uppercase text-lg">
                            {{ substr($staff->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $staff->name }}</p>
                            <p class="text-xs text-gray-500">{{ $staff->clients_count }} leads</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-brand-500">
                            {{ $stats['total'] > 0 ? round(($staff->clients_count / $stats['total']) * 100) : 0 }}%
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
  </div>
@endsection

@push('scripts')
<style>
    .apexcharts-tooltip {
        background: #ffffff !important;
        color: #1f2937 !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
    }
    .dark .apexcharts-tooltip {
        background: #111827 !important;
        color: #f3f4f6 !important;
        border: 1px solid #374151 !important;
    }
    .apexcharts-tooltip-title {
        background: #f9fafb !important;
        border-bottom: 1px solid #e5e7eb !important;
        font-weight: 600 !important;
    }
    .dark .apexcharts-tooltip-title {
        background: #1f2937 !important;
        border-bottom: 1px solid #374151 !important;
    }
    /* Ensure charts don't overflow */
    .apexcharts-canvas {
        margin: 0 auto;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94A3B8' : '#64748B';
        const headingColor = isDark ? '#FFFFFF' : '#1E293B';

        // 1. Lead Trends Chart (Line Chart)
        var trendOptions = {
            series: [{
                name: 'New Leads',
                data: {!! json_encode($trends['values']) !!}
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, colors: ['#465fff'] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                }
            },
            colors: ['#465fff'],
            xaxis: {
                categories: {!! json_encode($trends['labels']) !!},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: textColor, fontSize: '12px' } }
            },
            yaxis: {
                labels: { style: { colors: textColor, fontSize: '12px' } }
            },
            grid: {
                borderColor: isDark ? '#334155' : '#e5e7eb',
                strokeDashArray: 4,
                padding: { left: 20, right: 20 }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val) {
                        return val + " leads";
                    }
                }
            }
        };

        var trendChart = new ApexCharts(document.querySelector("#leadsTrendChart"), trendOptions);
        trendChart.render();

        // 2. Pipeline Status Chart (Donut Chart)
        var statusOptions = {
            series: {!! json_encode($statusDistribution->pluck('total')) !!},
            labels: {!! json_encode($statusDistribution->pluck('status')) !!},
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '14px', fontWeight: 600, color: textColor },
                            value: { 
                                show: true, 
                                fontSize: '24px', 
                                fontWeight: 700, 
                                color: headingColor,
                                formatter: (val) => val 
                            },
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
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
                markers: { radius: 12 },
                labels: { colors: textColor },
                itemMargin: { horizontal: 10, vertical: 5 }
            },
            colors: ['#465fff', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#3b82f6', '#14b8a6', '#f97316', '#0ea5e9'],
            stroke: { show: false },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                fillSeriesColor: false,
                y: {
                    formatter: function (val) {
                        return val + " Leads"
                    }
                }
            }
        };

        var statusChart = new ApexCharts(document.querySelector("#pipelineStatusChart"), statusOptions);
        statusChart.render();

        // Theme Toggle Listener
        window.addEventListener('theme-changed', function() {
            const newIsDark = document.documentElement.classList.contains('dark');
            const newTextColor = newIsDark ? '#94A3B8' : '#64748B';
            const newHeadingColor = newIsDark ? '#FFFFFF' : '#1E293B';
            
            trendChart.updateOptions({
                xaxis: { labels: { style: { colors: newTextColor } } },
                yaxis: { labels: { style: { colors: newTextColor } } },
                grid: { borderColor: newIsDark ? '#334155' : '#e5e7eb' },
                tooltip: { theme: newIsDark ? 'dark' : 'light' }
            });

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
</script>
@endpush
