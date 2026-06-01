@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb pageTitle="SMTP Providers" />
    </div>

    {{-- Stats Bar --}}
    <div class="flex flex-row gap-4 mb-6 overflow-x-auto pb-2">
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sent Today</p>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $sentToday }}</h4>
        </div>
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Daily Capacity</p>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalCapacity }}</h4>
        </div>
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pending Emails</p>
                    <h4 class="text-2xl font-bold {{ $pendingCount > 0 ? 'text-warning-500' : 'text-success-500' }}">{{ $pendingCount }}</h4>
                </div>
                <a href="{{ route('smtp-providers.pending') }}" title="View pending list" 
                    class="-mt-1 -mr-1 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-gray-100 dark:hover:bg-gray-800/50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Validation Errors Alert --}}
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-error-50 border border-error-200 text-error-700 px-4 py-3 text-sm dark:bg-error-500/10 dark:border-error-500/20 dark:text-error-400">
            <div class="font-semibold mb-1">Please fix the following validation errors:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Container --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        
        <!-- Header -->
        <div class="flex flex-col justify-between gap-5 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center dark:border-gray-800">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">SMTP Providers</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage multi-provider email rotation. Emails auto-switch when a provider hits its daily limit.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="document.getElementById('addProviderModal').classList.remove('hidden')"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M5 10.0002H15.0006M10.0002 5V15.0006" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    New Provider
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Provider</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Host / Driver</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">From Email</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Daily Limit</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sent Today</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($providers as $provider)
                        @php
                            $usage = $provider->daily_limit > 0 ? ($provider->sent_today / $provider->daily_limit) * 100 : 0;
                            $barColor = $usage >= 90 ? 'bg-error-500' : ($usage >= 60 ? 'bg-warning-500' : 'bg-success-500');
                        @endphp
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-gray-500">{{ $provider->priority }}</td>
                            <td class="px-5 py-4 whitespace-nowrap font-semibold text-gray-800 dark:text-white">{{ $provider->name }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $provider->host ?: strtoupper($provider->driver) }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $provider->from_email }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="inline-block text-left w-32">
                                    <span class="text-gray-800 dark:text-white font-medium text-xs">{{ $provider->sent_today }} / {{ $provider->daily_limit }}</span>
                                    <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $barColor }}" style="width: {{ min($usage, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-gray-600 dark:text-gray-400">{{ $provider->sent_today }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if($provider->is_active)
                                    @if($provider->sent_today >= $provider->daily_limit)
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">Limit Reached</span>
                                    @else
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500">Active</span>
                                    @endif
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 font-bold">Disabled</span>
                                @endif
                            </td>
                             <td class="px-5 py-4 whitespace-nowrap text-right">
                                 <div class="flex items-center justify-end gap-2">
                                     {{-- Reset Counter --}}
                                     <form method="POST" action="{{ route('smtp-providers.reset-counter', $provider) }}" class="inline">
                                         @csrf
                                         <button type="submit" 
                                             class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                                             </svg>
                                             Reset
                                         </button>
                                     </form>

                                     {{-- Toggle Active State --}}
                                     <form method="POST" action="{{ route('smtp-providers.toggle', $provider) }}" class="inline">
                                         @csrf
                                         <button type="submit" 
                                             class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                             @if($provider->is_active)
                                                 <svg class="w-3.5 h-3.5 text-warning-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                 </svg>
                                                 Disable
                                             @else
                                                 <svg class="w-3.5 h-3.5 text-success-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                 </svg>
                                                 Enable
                                             @endif
                                         </button>
                                     </form>

                                     {{-- Edit --}}
                                     <button onclick="openEditModal({{ $provider->id }}, {{ $provider->toJson() }})"
                                         class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.089a4.5 4.5 0 01-2.062 1.238l-3.29 1.19a.75.75 0 01-.92-.919l1.19-3.29a4.5 4.5 0 011.238-2.062L16.862 4.487zM16.862 4.487L19.5 7.125" />
                                         </svg>
                                         Edit
                                     </button>

                                     {{-- Delete --}}
                                     <form method="POST" action="{{ route('smtp-providers.destroy', $provider) }}" class="inline">
                                         @csrf @method('DELETE')
                                         <button type="button" onclick="confirmDelete(this.closest('form'), '{{ $provider->name }}')"
                                             class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                 <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                             </svg>
                                             Delete
                                         </button>
                                     </form>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-20 text-center text-gray-400 text-sm italic">
                                No SMTP providers configured yet. Add your first provider above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Provider Modal --}}
    <div id="addProviderModal" class="hidden fixed inset-0 flex items-center justify-center" style="z-index: 999999; background: rgba(0,0,0,0.65); backdrop-filter: blur(2px);">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full mx-4 flex flex-col overflow-hidden" style="max-width: 600px; max-height: 90vh;">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-brand-500">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Add SMTP Provider</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Configure a new email delivery provider</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('addProviderModal').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-white/10 dark:hover:text-gray-200 transition">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Scrollable Body --}}
            <div class="overflow-y-auto px-6 py-5 flex-1">
                <form method="POST" action="{{ route('smtp-providers.store') }}" class="space-y-3" id="addProviderForm">
                    @csrf
                    @include('pages.settings._smtp-provider-form')
                </form>
            </div>

            {{-- Sticky Footer --}}
            <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('addProviderModal').classList.add('hidden')"
                    class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    Cancel
                </button>
                <button type="submit" form="addProviderForm"
                    class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save Provider
                </button>
            </div>
        </div>
    </div>

    {{-- Edit Provider Modal --}}
    <div id="editProviderModal" class="hidden fixed inset-0 flex items-center justify-center" style="z-index: 999999; background: rgba(0,0,0,0.65); backdrop-filter: blur(2px);">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full mx-4 flex flex-col overflow-hidden" style="max-width: 600px; max-height: 90vh;">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-brand-500">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Edit SMTP Provider</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update provider settings and limits</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('editProviderModal').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-white/10 dark:hover:text-gray-200 transition">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Scrollable Body --}}
            <div class="overflow-y-auto px-6 py-5 flex-1">
                <form method="POST" id="editProviderForm" class="space-y-3">
                    @csrf @method('PUT')
                    @include('pages.settings._smtp-provider-form', ['editing' => true])
                </form>
            </div>

            {{-- Sticky Footer --}}
            <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('editProviderModal').classList.add('hidden')"
                    class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    Cancel
                </button>
                <button type="submit" form="editProviderForm"
                    class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(id, data) {
        const form = document.getElementById('editProviderForm');
        form.action = '/smtp-providers/' + id;

        const fields = ['name', 'driver', 'from_email', 'from_name', 'daily_limit', 'priority', 'is_active'];
        fields.forEach(field => {
            const el = form.querySelector('[name="' + field + '"]');
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = !!data[field];
                el.dispatchEvent(new Event('change'));
            } else {
                el.value = data[field] ?? '';
                el.dispatchEvent(new Event('input'));
                el.dispatchEvent(new Event('change'));
            }
        });

        // Always clear API key — user must re-enter to change it
        const apiKeyEl = form.querySelector('[name="api_key"]');
        if (apiKeyEl) apiKeyEl.value = '';

        document.getElementById('editProviderModal').classList.remove('hidden');
    }

    function confirmDelete(form, name) {
        Swal.fire({
            title: 'Delete Provider?',
            text: 'Delete ' + name + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    </script>
@endsection
