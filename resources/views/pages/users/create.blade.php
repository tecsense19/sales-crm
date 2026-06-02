@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Add New User" />
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-red-950/20 dark:text-red-400">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Section 1: Basic Credentials -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Credentials & Role</h3>
                <p class="text-sm text-gray-500">Primary login credentials and access level permission settings.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Full Name <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Email Address <span class="text-error-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Password <span class="text-error-500">*</span>
                        </label>
                        <input type="password" name="password" placeholder="Min. 6 characters" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            System Role <span class="text-error-500">*</span>
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            value: '{{ old('role', 'employee') }}',
                            roles: [
                                { id: 'admin', name: 'Admin (Full Access)' },
                                { id: 'employee', name: 'Employee (Standard User)' }
                            ],
                            get filteredRoles() {
                                if (!this.search) return this.roles;
                                return this.roles.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            getRoleName() {
                                let found = this.roles.find(r => r.id === this.value);
                                return found ? found.name : 'Select Role...';
                            },
                            select(id) {
                                this.value = id;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">
                            <input type="hidden" name="role" :value="value">
                            <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 dark:bg-dark-900 shadow-theme-xs text-left cursor-pointer">
                                <span x-text="getRoleName()"></span>
                                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                <div class="mb-1.5 flex-shrink-0">
                                    <input type="text" x-model="search" x-ref="searchInput" placeholder="Search roles..."
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-dark-900 dark:text-white/90 transition shadow-theme-xs">
                                </div>
                                <ul class="overflow-y-auto space-y-0.5 text-sm text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                    <template x-for="r in filteredRoles" :key="r.id">
                                        <li @click="select(r.id)"
                                            class="cursor-pointer rounded px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                            :class="value === r.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                            <span x-text="r.name"></span>
                                            <svg x-show="value === r.id" class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </li>
                                    </template>
                                    <template x-if="filteredRoles.length === 0">
                                        <li class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Profile & Details -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Profile Details</h3>
                <p class="text-sm text-gray-500">Contact details and user biography details.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Phone Number
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (234) 567-890"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Country
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            value: '{{ old('country', '') }}',
                            countries: {{ json_encode(App\Helpers\CountryHelper::getAllCountries()) }},
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
                            <input type="hidden" name="country" :value="value">
                            <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 dark:bg-dark-900 shadow-theme-xs text-left cursor-pointer">
                                <span x-text="value ? value : 'Select Country...'"></span>
                                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                <div class="mb-1.5 flex-shrink-0">
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
                                        <li class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Location (City)
                        </label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. London"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition" />
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Biography / Bio
                    </label>
                    <textarea name="bio" rows="4" placeholder="Brief biographical details..."
                        class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition">{{ old('bio') }}</textarea>
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
                Save User
            </button>
        </div>
    </form>
@endsection
