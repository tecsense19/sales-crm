@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb pageTitle="Billing & Invoicing" />
    </div>

    <!-- Stats Summary -->
    <div class="flex flex-row gap-4 mb-6 overflow-x-auto pb-2">
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Revenue</p>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['total_revenue'], 2) }}</h4>
        </div>
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pending</p>
            <h4 class="text-2xl font-bold text-warning-500">${{ number_format($stats['pending'], 2) }}</h4>
        </div>
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Overdue</p>
            <h4 class="text-2xl font-bold text-error-500">{{ $stats['overdue_count'] }}</h4>
        </div>
        <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Invoices</p>
            <h4 class="text-2xl font-bold text-brand-500">{{ $stats['total_count'] }}</h4>
        </div>
    </div>

    <div x-data="{
        selected: [],
        invoices: {{ $invoices->map(fn($b) => ['id' => $b->id])->toJson() }},
        showFilter: false,
        
        get isAllSelected() {
            return this.invoices.length > 0 && this.selected.length === this.invoices.length;
        },
        
        toggleAll() {
            if (this.isAllSelected) {
                this.selected = [];
            } else {
                this.selected = this.invoices.map(i => i.id);
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
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Invoices List</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Track payments, issue new invoices, and manage revenue.</p>
            </div>
            <div class="flex gap-3">
                <!-- Bulk Actions -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                <div x-show="selected.length > 0" x-transition class="flex items-center gap-3 mr-2 pr-4 border-r border-gray-200 dark:border-gray-800">
                    <form id="bulk-delete-form" action="{{ route('billing.bulk-destroy') }}" method="POST" class="hidden">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                    </form>
                    <button @click="
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'You want to delete ' + selected.length + ' selected invoices?',
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
                @endif

                <a href="{{ route('billing.export') }}" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Export CSV
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M16.667 13.3333V15.4166C16.667 16.1069 16.1074 16.6666 15.417 16.6666H4.58295C3.89259 16.6666 3.33295 16.1069 3.33295 15.4166V13.3333M10.0013 13.3333L10.0013 3.33325M6.14547 9.47942L9.99951 13.331L13.8538 9.47942" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
                <a href="{{ route('billing.create') }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M5 10.0002H15.0006M10.0002 5V15.0006" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    New Invoice
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <div class="flex items-center justify-between gap-3">
                <form action="{{ route('billing.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('from_date'))
                        <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                    @endif
                    @if(request('to_date'))
                        <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                    @endif

                    <div class="relative w-[260px]">
                        <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-400">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent pr-10 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                        @if(request('search'))
                            <a href="{{ route('billing.index', request()->except(['search', 'page'])) }}" class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                    
                    <button type="submit" class="shadow-theme-xs inline-flex h-10 items-center justify-center gap-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 px-4 text-sm font-semibold text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="opacity-90">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                    
                    @if(request('search'))
                        <a href="{{ route('billing.index', request()->except(['search', 'page'])) }}" class="shadow-theme-xs inline-flex h-10 items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear
                        </a>
                    @endif
                </form>
                <div class="relative">
                    <button @click="showFilter = !showFilter" type="button" class="shadow-theme-xs flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <path d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        Filter
                    </button>
                    <div x-show="showFilter" @click.away="showFilter = false" x-transition style="width: 380px;" class="absolute right-0 z-10 mt-2 rounded-lg border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <form action="{{ route('billing.index') }}" method="GET" class="space-y-5">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Payment Status</label>
                                <select name="status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                                    <option value="">All Statuses</option>
                                    @foreach(['Paid', 'Pending', 'Overdue', 'Cancelled'] as $st)
                                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date Range</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 mb-2">
                                <input type="date" name="to_date" value="{{ request('to_date') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                            </div>
                            <button type="submit" class="bg-brand-500 hover:bg-brand-600 h-11 w-full rounded-lg px-3 py-2 text-sm font-bold text-white transition shadow-theme-xs">Apply Filter</button>
                        </form>
                    </div>
                </div>
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
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice Info</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($invoices as $billing)
                    <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <label class="cursor-pointer">
                                <input type="checkbox" class="sr-only" :checked="selected.includes({{ $billing->id }})" @change="toggleSelect({{ $billing->id }})">
                                <span :class="selected.includes({{ $billing->id }}) ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px] transition">
                                    <svg x-show="selected.includes({{ $billing->id }})" width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </label>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800 dark:text-white/90 leading-tight">#{{ $billing->invoice_number }}</span>
                                <span class="text-[11px] text-gray-400">{{ $billing->issue_date->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-[10px] text-gray-500">
                                    {{ substr($billing->client->name, 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-400">{{ $billing->client->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-800 dark:text-white/90">${{ number_format($billing->total_amount, 2) }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold uppercase
                                @if($billing->status === 'Paid') bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500
                                @elseif($billing->status === 'Overdue') bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500
                                @else bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400 @endif">
                                {{ $billing->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('billing.show', $billing) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Preview
                                </a>
                                
                                <a href="{{ route('billing.edit', $billing) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    Edit
                                </a>

                                <form action="{{ route('billing.destroy', $billing) }}" method="POST" id="delete-form-{{ $billing->id }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="
                                        Swal.fire({
                                            title: 'Delete Invoice?',
                                            text: 'Are you sure you want to delete this invoice?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#ef4444',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Yes, delete it!',
                                            cancelButtonText: 'Cancel',
                                            customClass: {
                                                popup: 'rounded-2xl dark:bg-gray-900 dark:text-white border border-gray-200 dark:border-gray-800 shadow-xl',
                                                confirmButton: 'bg-error-500 hover:bg-error-600 px-6 py-2.5 rounded-lg text-sm font-bold text-white transition',
                                                cancelButton: 'bg-gray-100 hover:bg-gray-200 px-6 py-2.5 rounded-lg text-sm font-bold text-gray-700 transition ml-3'
                                            },
                                            buttonsStyling: false
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                document.getElementById('delete-form-{{ $billing->id }}').submit();
                                            }
                                        })
                                    " class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-20 text-center text-gray-400 text-sm italic">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center flex-col sm:flex-row justify-between border-t border-gray-200 px-5 py-4 dark:border-gray-800">
            <div class="pb-3 sm:pb-0">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Showing <span class="text-gray-800 dark:text-white/90 font-bold">{{ $invoices->firstItem() ?? 0 }}</span>
                    to <span class="text-gray-800 dark:text-white/90 font-bold">{{ $invoices->lastItem() ?? 0 }}</span>
                    of <span class="text-gray-800 dark:text-white/90 font-bold">{{ $invoices->total() }}</span> invoices
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $invoices->previousPageUrl() }}" class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
                <ul class="flex items-center gap-1">
                    @foreach ($invoices->getUrlRange(max(1, $invoices->currentPage() - 2), min($invoices->lastPage(), $invoices->currentPage() + 2)) as $page => $url)
                        <li>
                            <a href="{{ $url }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold transition {{ $page == $invoices->currentPage() ? 'bg-brand-500 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ $invoices->nextPageUrl() }}" class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </a>
            </div>
        </div>
    </div>
@endsection
