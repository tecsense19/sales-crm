@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Edit Client" />
    </div>

    <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Section 1: Basic Information -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Basic Information</h3>
                <p class="text-sm text-gray-500">Update primary contact details for {{ $client->name }}.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Client Name <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ $client->name }}" placeholder="Enter client name" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Email Address
                        </label>
                        <input type="email" name="email" value="{{ $client->email }}" placeholder="client@example.com"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Mobile No
                        </label>
                        <input type="text" name="mobile_no" value="{{ $client->mobile_no }}" placeholder="+123456789"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Project & Status -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Project & Assignment</h3>
                <p class="text-sm text-gray-500">Update technical details and ownership.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Location
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            value: '{{ old('location', $client->location ?? '') }}',
                            countries: {{ json_encode(array_merge(
                                ($client->location && !in_array($client->location, App\Helpers\CountryHelper::getAllCountries())) ? [$client->location] : [],
                                App\Helpers\CountryHelper::getAllCountries()
                            )) }},
                            get filteredCountries() {
                                if (!this.search) return this.countries;
                                return this.countries.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            select(country) {
                                this.value = country;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">
                            <input type="hidden" name="location" :value="value">
                            <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 dark:bg-dark-900 shadow-theme-xs text-left">
                                <span x-text="value ? value : 'Select Country...'"></span>
                                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-950"
                                style="max-height: 320px; overflow: hidden; display: flex; flex-direction: column;">
                                <div class="mb-2 flex-shrink-0">
                                    <input type="text" x-model="search" x-ref="searchInput" placeholder="Search countries..."
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-dark-900 dark:text-white/90 transition shadow-theme-xs">
                                </div>
                                <ul class="overflow-y-auto space-y-0.5 text-sm text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                    <template x-for="country in filteredCountries" :key="country">
                                        <li @click="select(country)"
                                            class="cursor-pointer rounded px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                            :class="value === country ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                            <span x-text="country"></span>
                                            <svg x-show="value === country" class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </li>
                                    </template>
                                    <template x-if="filteredCountries.length === 0">
                                        <li class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 italic">No countries found</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Technology
                        </label>
                        <input type="text" name="technology" value="{{ $client->technology }}" placeholder="e.g. Laravel, React"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Status
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            value: '{{ old('status', $client->status ?? 'New') }}',
                            statuses: {{ json_encode(['New', 'Interested', 'Contacted', 'In Progress', 'Follow Up', 'On Hold', 'Converted', 'Closed Won', 'Closed Lost', 'Not Interested']) }},
                            get filteredStatuses() {
                                if (!this.search) return this.statuses;
                                return this.statuses.filter(s => s.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            select(status) {
                                this.value = status;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">
                            <input type="hidden" name="status" :value="value">
                            <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 dark:bg-dark-900 shadow-theme-xs text-left cursor-pointer">
                                <span x-text="value ? value : 'Select Status...'"></span>
                                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                <div class="mb-1.5 flex-shrink-0">
                                    <input type="text" x-model="search" x-ref="searchInput" placeholder="Search status..."
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-dark-900 dark:text-white/90 transition shadow-theme-xs">
                                </div>
                                <ul class="overflow-y-auto space-y-0.5 text-sm text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                    <template x-for="status in filteredStatuses" :key="status">
                                        <li @click="select(status)"
                                            class="cursor-pointer rounded px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                            :class="value === status ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                            <span x-text="status"></span>
                                            <svg x-show="value === status" class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </li>
                                    </template>
                                    <template x-if="filteredStatuses.length === 0">
                                        <li class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->role !== 'employee')
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Assigned To
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            value: '{{ old('assigned_to', $client->assigned_to ?? '') }}',
                            users: {{ collect($users)->map(fn($u) => ['id'=>$u->id,'name'=>$u->name.' ('.ucfirst($u->role).')'])->toJson() }},
                            get filteredUsers() {
                                let list = [{id: '', name: '— Unassigned —'}, ...this.users];
                                if (!this.search) return list;
                                return list.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            getUserName() {
                                let found = this.users.find(u => u.id == this.value);
                                return found ? found.name : '— Unassigned —';
                            },
                            select(id) {
                                this.value = id;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">
                            <input type="hidden" name="assigned_to" :value="value">
                            <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 dark:bg-dark-900 shadow-theme-xs text-left cursor-pointer">
                                <span x-text="getUserName()"></span>
                                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                <div class="mb-1.5 flex-shrink-0">
                                    <input type="text" x-model="search" x-ref="searchInput" placeholder="Search user..."
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-dark-900 dark:text-white/90 transition shadow-theme-xs">
                                </div>
                                <ul class="overflow-y-auto space-y-0.5 text-sm text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                    <template x-for="u in filteredUsers" :key="u.id">
                                        <li @click="select(u.id)"
                                            class="cursor-pointer rounded px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                            :class="value == u.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                            <span x-text="u.name"></span>
                                            <svg x-show="value == u.id" class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </li>
                                    </template>
                                    <template x-if="filteredUsers.length === 0">
                                        <li class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 3: Social & Communication Links -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Social & Communication</h3>
                <p class="text-sm text-gray-500">Links and alternative contact methods.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-3 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Website</label>
                        <input type="text" name="website" value="{{ $client->website }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Link</label>
                        <input type="text" name="project_link" value="{{ $client->project_link }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">LinkedIn</label>
                        <input type="text" name="linkedin" value="{{ $client->linkedin }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Facebook</label>
                        <input type="text" name="facebook" value="{{ $client->facebook }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Teams</label>
                        <input type="text" name="teams" value="{{ $client->teams }}" placeholder="ID or link" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ $client->whatsapp }}" placeholder="+1234..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Telegram</label>
                        <input type="text" name="telegram" value="{{ $client->telegram }}" placeholder="@username" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Instagram</label>
                        <input type="text" name="instagram" value="{{ $client->instagram }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">YouTube</label>
                        <input type="text" name="youtube" value="{{ $client->youtube }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">X (Twitter)</label>
                        <input type="text" name="x" value="{{ $client->x }}" placeholder="https://x.com/..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Source URL</label>
                        <input type="text" name="source_url" value="{{ $client->source_url }}" placeholder="https://..." class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Follow-up & Notes -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]"
             x-data="{
                 calculateNextDate() {
                     const lastInput = this.$el.querySelector('input[name=\'last_contacted_date\']');
                     const daysInput = this.$el.querySelector('input[name=\'follow_up_days\']');
                     const nextInput = this.$el.querySelector('input[name=\'next_followup_date\']');
                     
                     if (lastInput && daysInput && nextInput) {
                         const lastVal = lastInput.value;
                         const daysVal = parseInt(daysInput.value, 10);
                         
                         if (lastVal && !isNaN(daysVal)) {
                             const parts = lastVal.split('-');
                             if (parts.length === 3) {
                                 const date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                                 date.setDate(date.getDate() + daysVal);
                                 const y = date.getFullYear();
                                 const m = String(date.getMonth() + 1).padStart(2, '0');
                                 const d = String(date.getDate()).padStart(2, '0');
                                 const nextStr = `${y}-${m}-${d}`;
                                 if (nextInput._flatpickr) {
                                     nextInput._flatpickr.setDate(nextStr);
                                 } else {
                                     nextInput.value = nextStr;
                                 }
                             }
                         } else {
                             if (nextInput._flatpickr) {
                                 nextInput._flatpickr.clear();
                             } else {
                                 nextInput.value = '';
                             }
                         }
                     }
                 }
             }"
             @date-change="calculateNextDate()"
             @input="calculateNextDate()">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Additional Details & Follow-ups</h3>
                <p class="text-sm text-gray-500">Scheduling and miscellaneous notes.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <x-form.date-picker 
                        name="last_contacted_date" 
                        label="Last Contacted Date" 
                        :defaultDate="$client->last_contacted_date?->format('Y-m-d')" 
                    />
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Follow-up Days
                        </label>
                        <input type="number" name="follow_up_days" value="{{ $client->follow_up_days ?? 7 }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <x-form.date-picker 
                        name="next_followup_date" 
                        label="Next Follow-up Date (Calculated if empty)" 
                        :defaultDate="$client->next_followup_date?->format('Y-m-d')" 
                    />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Notes
                    </label>
                    <textarea name="notes" rows="4" placeholder="Enter additional notes..."
                        class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition">{{ $client->notes }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Actions -->
        <div class="flex items-center justify-end gap-3 sticky bottom-6 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-lg z-10">
            <button type="button" onclick="window.history.back()" 
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-7 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:hover:bg-white/10 shadow-theme-xs">
                Cancel
            </button>
            <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-brand-600 shadow-theme-xs">
                Update Client
            </button>
        </div>
    </form>
@endsection
