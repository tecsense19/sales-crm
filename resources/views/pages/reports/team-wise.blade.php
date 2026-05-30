@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <x-common.page-breadcrumb pageTitle="Team Wise Reports" />
        <p class="text-sm text-gray-500 mt-1">Breakdown of performance by team and individual members.</p>
    </div>

    <!-- Date Range Filter -->
    <form action="{{ route('reports.team-wise') }}" method="GET" class="flex items-center gap-3">
        <div class="w-72">
            <x-form.date-picker 
                name="date_range" 
                mode="range" 
                placeholder="Select date range"
                :defaultDate="[$startDate, $endDate]"
            />
        </div>
        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            Filter
        </button>
    </form>
</div>

<div class="grid grid-cols-12 gap-4 md:gap-6">
    <!-- Top Row: Member Performance and Summary -->
    <div class="col-span-12 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <div class="animate-fade-in h-full rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]" style="animation-delay: 0.1s;">
            <h3 class="mb-6 text-lg font-bold text-gray-800 dark:text-white">Individual Member Performance</h3>
            <div id="userPerformanceChart" class="min-h-[400px]"></div>
        </div>

        <div class="animate-fade-in h-full rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]" style="animation-delay: 0.2s;">
            <h3 class="mb-6 text-lg font-bold text-gray-800 dark:text-white">Quick Summary</h3>
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-brand-50 dark:bg-brand-500/5 border border-brand-100 dark:border-brand-500/10">
                    <p class="text-xs font-medium text-brand-600 dark:text-brand-400 uppercase tracking-wider">Top Performing Team</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $teamStats->sortByDesc('closed_won')->first()?->teams ?: 'N/A' }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-success-50 dark:bg-success-500/5 border border-success-100 dark:border-success-500/10">
                    <p class="text-xs font-medium text-success-600 dark:text-success-400 uppercase tracking-wider">Most Leads Generated</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $userPerformance->sortByDesc('clients_count')->first()?->name ?: 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Performance Table (Bottom) -->
    <div class="col-span-12 animate-fade-in" style="animation-delay: 0.3s;">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Performance by Team</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Team Name</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">Total Clients</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">New Leads</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">Closed Won</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">Conversion %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($teamStats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $stat->teams ?: 'Unassigned' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $stat->total_clients }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                    {{ $stat->new_leads }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-600 dark:bg-green-500/10 dark:text-green-400">
                                    {{ $stat->closed_won }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-brand-500">
                                {{ $stat->total_clients > 0 ? round(($stat->closed_won / $stat->total_clients) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">No data found for the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $teamStats->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Page Load Animations */
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        opacity: 0;
        animation: fade-in-up 0.8s ease-out forwards;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Leads Handled',
                data: {!! json_encode($userPerformance->pluck('clients_count')) !!}
            }],
            chart: { 
                type: 'bar', 
                height: 400, 
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 1000,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '35%',
                    distributed: true,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: { 
                enabled: true,
                offsetY: -20,
                style: { fontSize: '12px', fontWeight: 600, colors: ['#64748b'] }
            },
            colors: ['#465fff', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#3b82f6', '#0ea5e9'],
            xaxis: {
                categories: {!! json_encode($userPerformance->pluck('name')) !!},
                labels: { style: { colors: '#64748b', fontSize: '12px', fontWeight: 500 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#64748b', fontSize: '12px' } }
            },
            legend: { show: false },
            grid: { 
                borderColor: '#f1f5f9', 
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " Leads"
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#userPerformanceChart"), options).render();
    });
</script>
@endpush
