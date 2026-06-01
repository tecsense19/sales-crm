@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb pageTitle="Pending Emails" />
    </div>

    {{-- Card Container --}}
    <div x-data="{
        selected: [],
        pendingEmails: @js($pending->map(fn($e) => ['id' => $e->id])),
        
        get isAllSelected() {
            return this.pendingEmails.length > 0 && this.selected.length === this.pendingEmails.length;
        },
        
        toggleAll() {
            if (this.isAllSelected) {
                this.selected = [];
            } else {
                this.selected = this.pendingEmails.map(e => e.id);
            }
        },
        
        toggleSelect(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                this.selected.push(id);
            }
        }
    }" class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        
        <!-- Header -->
        <div class="flex flex-col justify-between gap-5 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center dark:border-gray-800">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pending Emails</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Emails queued because all SMTP providers reached their daily limit. They will auto-fire when limits reset.</p>
            </div>
            <div class="flex gap-3">
                <!-- Bulk Actions -->
                <div x-show="selected.length > 0" x-transition class="flex items-center gap-3 mr-2 pr-4 border-r border-gray-200 dark:border-gray-800">
                    <form id="bulk-delete-form" action="{{ route('smtp-providers.pending.bulk-destroy') }}" method="POST" class="hidden">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                    </form>
                    <button @click="
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'You want to delete ' + selected.length + ' selected pending emails?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, delete them!',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                popup: 'rounded-2xl dark:bg-gray-900 dark:text-white border border-gray-200 dark:border-gray-800 shadow-xl',
                                confirmButton: 'bg-error-500 hover:bg-error-600 px-6 py-2.5 rounded-lg text-sm font-bold text-white transition',
                                cancelButton: 'bg-gray-100 hover:bg-gray-200 px-6 py-2.5 rounded-lg text-sm font-bold text-gray-700 transition ml-3'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('bulk-delete-form').submit();
                            }
                        })
                    " 
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-error-50 px-4 py-2.5 text-sm font-medium text-error-600 transition hover:bg-error-100 dark:bg-error-500/10 dark:text-error-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        Delete Selected (<span x-text="selected.length"></span>)
                    </button>
                </div>

                <a href="{{ route('smtp-providers.index') }}" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    ← Back to Providers
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="w-14 px-5 py-4 text-left">
                            <label class="cursor-pointer">
                                <input type="checkbox" class="sr-only" @change="toggleAll()" :checked="isAllSelected">
                                <span :class="isAllSelected ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px] transition">
                                    <svg x-show="isAllSelected" width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </label>
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campaign</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipient</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attempts</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Next Attempt</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Error</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Queued At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($pending as $email)
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="sr-only" :checked="selected.includes({{ $email->id }})" @change="toggleSelect({{ $email->id }})">
                                    <span :class="selected.includes({{ $email->id }}) ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px] transition">
                                        <svg x-show="selected.includes({{ $email->id }})" width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </label>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-medium text-gray-800 dark:text-white">
                                {{ $email->campaign->name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-800 dark:text-white font-medium leading-tight">{{ $email->recipient_name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $email->recipient_email }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($email->status === 'sent')
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500">Sent</span>
                                @elseif($email->status === 'failed')
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500">Failed</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $email->attempts }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500 text-xs">
                                {{ $email->next_attempt_at ? $email->next_attempt_at->diffForHumans() : '—' }}
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs max-w-xs truncate" title="{{ $email->last_error }}">
                                {{ $email->last_error ? \Str::limit($email->last_error, 60) : '—' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-400 text-xs">{{ $email->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-20 text-center text-gray-400 text-sm italic">No pending emails. All caught up!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pending->hasPages())
            <div class="flex items-center justify-between border-t border-gray-200 px-5 py-4 dark:border-gray-800">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Showing <span class="text-gray-800 dark:text-white/90 font-bold">{{ $pending->firstItem() ?? 0 }}</span>
                        to <span class="text-gray-800 dark:text-white/90 font-bold">{{ $pending->lastItem() ?? 0 }}</span>
                        of <span class="text-gray-800 dark:text-white/90 font-bold">{{ $pending->total() }}</span> pending emails
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ $pending->previousPageUrl() }}" class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                    <ul class="flex items-center gap-1">
                        @foreach ($pending->getUrlRange(max(1, $pending->currentPage() - 2), min($pending->lastPage(), $pending->currentPage() + 2)) as $page => $url)
                            <li>
                                <a href="{{ $url }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold transition {{ $page == $pending->currentPage() ? 'bg-brand-500 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $pending->nextPageUrl() }}" class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
