@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb pageTitle="Campaign Details" />
        <div class="flex gap-3">
            <a href="{{ route('campaigns.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 shadow-theme-xs transition">
                Back to List
            </a>
            @if($campaign->status !== 'Sent' && $campaign->status !== 'Processing')
                <form action="{{ route('campaigns.send-now', $campaign) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-success-500 px-4 py-2 text-sm font-medium text-white hover:bg-success-600 shadow-theme-xs transition">
                        Send Now
                    </button>
                </form>
            @endif
            <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 shadow-theme-xs transition">
                Edit Campaign
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Email Content</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Subject</p>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $campaign->subject }}</h2>
                    </div>
                    <div class="prose prose-sm dark:prose-invert max-w-none border-t border-gray-100 pt-6 dark:border-gray-800">
                        {!! nl2br(e($campaign->body)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Campaign Info</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Status</p>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase
                            @if($campaign->status === 'Sent') bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500
                            @elseif($campaign->status === 'Scheduled') bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500
                            @else bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-400 @endif">
                            {{ $campaign->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Target Audience</p>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                <span class="text-gray-500 mr-1">CRM:</span>
                                @if($campaign->target_status === 'all') 
                                    All Clients 
                                @elseif($campaign->target_status === 'custom')
                                    Custom ({{ count($campaign->selected_clients ?? []) }} selected)
                                @else 
                                    {{ $campaign->target_status }} Clients 
                                @endif
                            </p>
                            @if(!empty($campaign->external_emails))
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    <span class="text-gray-500 mr-1">External:</span>
                                    {{ count($campaign->external_emails) }} recipients from file
                                </p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Scheduled For</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Immediate' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Performance</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div class="text-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <p class="text-xl font-bold text-success-500">{{ number_format($campaign->sent_count) }}</p>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Sent</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <p class="text-xl font-bold text-error-500">{{ number_format($campaign->failed_count) }}</p>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Failed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
