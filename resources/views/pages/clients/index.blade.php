@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb pageTitle="Clients Management" />
    </div>

    <div x-data="{
        selected: [],
        expanded: [],
        clients: {{ $clients->map(fn($c) => ['id' => $c->id])->toJson() }},
        showFilter: false,
        
        get isAllSelected() {
            return this.clients.length > 0 && this.selected.length === this.clients.length;
        },
        
        toggleAll() {
            if (this.isAllSelected) {
                this.selected = [];
            } else {
                this.selected = this.clients.map(c => c.id);
            }
        },
        
        toggleSelect(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                this.selected.push(id);
            }
        },

        toggleExpand(id) {
            if (this.expanded.includes(id)) {
                this.expanded = this.expanded.filter(i => i !== id);
            } else {
                this.expanded.push(id);
            }
        }
    }" class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        
        <!-- Header -->
        <div class="flex flex-col justify-between gap-5 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center dark:border-gray-800">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Clients List</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage your customer relationships and lead pipeline.</p>
            </div>
            <div class="flex gap-3">
                <!-- Bulk Actions -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                <div x-show="selected.length > 0" x-transition class="flex items-center gap-3 mr-2 pr-4 border-r border-gray-200 dark:border-gray-800">
                    <form id="bulk-delete-form" action="{{ route('clients.bulk-destroy') }}" method="POST" class="hidden">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                    </form>
                    <button @click="
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'You want to delete ' + selected.length + ' selected clients?',
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

                <a href="{{ route('clients.export') }}" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Export CSV
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M16.667 13.3333V15.4166C16.667 16.1069 16.1074 16.6666 15.417 16.6666H4.58295C3.89259 16.6666 3.33295 16.1069 3.33295 15.4166V13.3333M10.0013 13.3333L10.0013 3.33325M6.14547 9.47942L9.99951 13.331L13.8538 9.47942" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
                @if(auth()->check())
                <a href="{{ route('clients.create') }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M5 10.0002H15.0006M10.0002 5V15.0006" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    Add Client
                </a>
                @endif
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <div class="flex items-center justify-between gap-3">
                <div class="relative w-[260px]">
                    <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-400">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"></path>
                        </svg>
                    </span>
                    <form action="{{ route('clients.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent pr-4 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                    </form>
                </div>
                <div class="relative">
                    <button @click="showFilter = !showFilter" type="button" class="shadow-theme-xs flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <path d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        Filter
                    </button>
                    <div x-show="showFilter" @click.away="showFilter = false" x-transition style="width: 380px;" class="absolute right-0 z-10 mt-2 rounded-lg border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <form action="{{ route('clients.index') }}" method="GET" class="space-y-5">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</label>
                                <select name="status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                                    <option value="">All Statuses</option>
                                    @foreach(['New', 'Interested', 'Contacted', 'In Progress', 'Follow Up', 'On Hold', 'Converted', 'Closed Won', 'Closed Lost', 'Not Interested'] as $st)
                                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Technology</label>
                                <input type="text" name="technology" value="{{ request('technology') }}" placeholder="Search technology..." 
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
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
                        <!-- <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th> -->
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client Name</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mobile no</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Contacted date</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Followup days</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Next followup date</th>
                        <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        <th class="w-10 px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($clients as $client)
                    <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <label class="cursor-pointer">
                                <input type="checkbox" class="sr-only" :checked="selected.includes({{ $client->id }})" @change="toggleSelect({{ $client->id }})">
                                <span :class="selected.includes({{ $client->id }}) ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px] transition">
                                    <svg x-show="selected.includes({{ $client->id }})" width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </label>
                        </td>
                        <!-- <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $client->created_at ? $client->created_at->format('M d, Y') : '-' }}
                        </td> -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm font-bold text-gray-800 dark:text-white/90">
                            {{ $client->name }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $client->email ?: '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $client->mobile_no ?: '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-1.5">
                                <svg class="text-gray-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span>{{ $client->location ?: '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $client->last_contacted_date ? $client->last_contacted_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold uppercase
                                @if(in_array($client->status, ['Closed Won', 'Converted'])) bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500
                                @elseif(in_array($client->status, ['Closed Lost', 'Not Interested'])) bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500
                                @elseif(in_array($client->status, ['Follow Up', 'On Hold'])) bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400
                                @elseif($client->status === 'Interested') bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400
                                @else bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500 @endif">
                                {{ $client->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $client->follow_up_days ?: '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $client->next_follow_up_date ? $client->next_follow_up_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" class="text-gray-400 hover:text-gray-600 transition p-1">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-40 rounded-xl border border-gray-100 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                                    <a href="{{ route('clients.show', $client) }}" class="flex w-full px-3 py-2 text-xs font-medium text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">View Details</a>
                                    @if(auth()->user()->role !== 'employee' || $client->assigned_to === auth()->id())
                                        <a href="{{ route('clients.edit', $client) }}" class="flex w-full px-3 py-2 text-xs font-medium text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">Edit Client</a>
                                    @endif
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" id="delete-form-{{ $client->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="
                                            Swal.fire({
                                                title: 'Delete Client?',
                                                text: 'Are you sure you want to delete this client?',
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
                                                    document.getElementById('delete-form-{{ $client->id }}').submit();
                                                }
                                            })
                                        " class="flex w-full px-3 py-2 text-xs font-medium text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-left">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <button @click="toggleExpand({{ $client->id }})" class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-brand-50 hover:text-brand-500 hover:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 transition-all shadow-sm">
                                <svg x-show="!expanded.includes({{ $client->id }})" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 5V15M5 10H15"/></svg>
                                <svg x-show="expanded.includes({{ $client->id }})" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 10H15"/></svg>
                            </button>
                        </td>
                    </tr>
                    {{-- Animated Premium Detail Row --}}
                    <tr x-show="expanded.includes({{ $client->id }})" x-collapse>
                        <td colspan="11" class="px-5 py-4 bg-gray-50/50 dark:bg-white/[0.02]">
                            <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                {{-- Decorative Background Element --}}
                                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand-500/5 blur-3xl"></div>
                                
                                <div class="relative flex flex-col gap-6">
                                    {{-- Header: Client Summary --}}
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                                            <h4 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-400">Client Insights</h4>
                                        </div>
                                        <h3 class="text-2xl font-extrabold text-gray-800 dark:text-white">
                                            {{ $client->notes ? 'Project Overview' : 'No notes available' }}
                                        </h3>
                                        <p class="max-w-3xl text-base leading-relaxed text-gray-600 dark:text-gray-400">
                                            {{ $client->notes ?: 'Enhance this client profile by adding specific notes or project requirements in the edit section.' }}
                                        </p>
                                    </div>

                                    {{-- Info Grid --}}
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                        <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Technology Stack</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">{{ $client->technology ?: 'Not specified' }}</p>
                                        </div>
                                        <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Global Location</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">{{ $client->location ?: 'Remote / Global' }}</p>
                                        </div>
                                        <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Source Link</p>
                                            <p class="mt-1 text-sm font-semibold">
                                                @if($client->source_url)
                                                    <a href="{{ $client->source_url }}" target="_blank" class="text-brand-500 hover:text-brand-600 hover:underline flex items-center gap-1.5 transition">
                                                        <span>Visit Source</span>
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                                    </a>
                                                @else
                                                    <span class="text-gray-800 dark:text-white">Direct Entry</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Footer Action Pill Buttons --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                                        @php $hasSocial = false; @endphp
                                        @foreach(['website', 'project_link', 'facebook', 'x', 'linkedin', 'instagram', 'youtube', 'whatsapp', 'telegram', 'teams'] as $social)
                                            @if($client->$social)
                                                @php
                                                    $hasSocial = true;
                                                    $link = $client->$social;
                                                    if ($social === 'whatsapp') { $link = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $link); }
                                                    elseif ($social === 'telegram') { $link = 'https://t.me/' . ltrim($link, '@'); }
                                                @endphp
                                                <a href="{{ $link }}" target="_blank" 
                                                    class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-bold text-gray-600 transition hover:bg-brand-500 hover:text-white hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-brand-500 dark:hover:text-white shadow-sm">
                                                    {{ ucfirst(str_replace('_', ' ', $social)) }}
                                                </a>
                                            @endif
                                        @endforeach
                                        
                                        @if(auth()->user()->role !== 'employee' || $client->assigned_to === auth()->id())
                                            <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2 text-xs font-bold text-white shadow-lg shadow-brand-500/20 transition hover:bg-brand-600">
                                                Edit Record
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-5 py-20 text-center text-gray-400 text-sm italic">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center flex-col sm:flex-row justify-between border-t border-gray-200 px-5 py-4 dark:border-gray-800">
            <div class="pb-3 sm:pb-0">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Showing <span class="text-gray-800 dark:text-white/90 font-bold">{{ $clients->firstItem() ?? 0 }}</span>
                    to <span class="text-gray-800 dark:text-white/90 font-bold">{{ $clients->lastItem() ?? 0 }}</span>
                    of <span class="text-gray-800 dark:text-white/90 font-bold">{{ $clients->total() }}</span> clients
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $clients->previousPageUrl() }}" class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
                <ul class="flex items-center gap-1">
                    @foreach ($clients->getUrlRange(max(1, $clients->currentPage() - 2), min($clients->lastPage(), $clients->currentPage() + 2)) as $page => $url)
                        <li>
                            <a href="{{ $url }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold transition {{ $page == $clients->currentPage() ? 'bg-brand-500 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ $clients->nextPageUrl() }}" class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </a>
            </div>
        </div>
    </div>
@endsection
