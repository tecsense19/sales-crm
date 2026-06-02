@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Edit Campaign" />
    </div>

    <form action="{{ route('campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data" 
        x-data="{ 
            targetStatus: '{{ $campaign->target_status }}', 
            selectedClients: (@js($campaign->selected_clients ?? [])).map(Number),
            selectedClientsDetails: @js($clients->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email])->toArray()),
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            fileName: '',
            selectedTemplate: '{{ $campaign->template_id ?? '' }}',
            subject: @js($campaign->subject),
            templates: @js($templates),
            tplOpen: false,
            tplSearch: '',
            get filteredTemplates() {
                const opts = [{ value: '', label: '-- Choose Template --' }, ...this.templates.map(t => ({ value: String(t.id), label: t.name }))];
                if (!this.tplSearch) return opts;
                return opts.filter(o => o.label.toLowerCase().includes(this.tplSearch.toLowerCase()));
            },
            get selectedTemplateLabel() {
                const t = this.templates.find(t => String(t.id) === String(this.selectedTemplate));
                return t ? t.name : '-- Choose Template --';
            },
            
            toggleClient(client) {
                const id = Number(client.id);
                const idx = this.selectedClients.map(Number).indexOf(id);
                if (idx !== -1) {
                    this.selectedClients.splice(idx, 1);
                    this.selectedClientsDetails = this.selectedClientsDetails.filter(c => Number(c.id) !== id);
                } else {
                    this.selectedClients.push(id);
                    this.selectedClientsDetails.push({
                        id: id,
                        name: client.name,
                        email: client.email
                    });
                }
            },
            
            selectAllVisible() {
                this.searchResults.forEach(client => {
                    const id = Number(client.id);
                    if (!this.selectedClients.map(Number).includes(id)) {
                        this.selectedClients.push(id);
                        this.selectedClientsDetails.push({
                            id: id,
                            name: client.name,
                            email: client.email
                        });
                    }
                });
            },
            
            fetchClients(query) {
                this.isSearching = true;
                fetch(`/campaigns/search-clients?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.searchResults = data;
                        this.isSearching = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.isSearching = false;
                    });
            },
            
            selectOption(val) {
                this.selectedTemplate = val;
                const temp = this.templates.find(t => String(t.id) === String(val));
                if (temp) {
                    this.subject = temp.subject;
                    if (window.CKEDITOR && window.CKEDITOR.instances.editor) {
                        window.CKEDITOR.instances.editor.setData(temp.content || '');
                    }
                } else {
                    this.subject = '';
                    if (window.CKEDITOR && window.CKEDITOR.instances.editor) {
                        window.CKEDITOR.instances.editor.setData('');
                    }
                }
                this.tplOpen = false;
                this.tplSearch = '';
            }
        }" 
        x-init="
            if (targetStatus === 'custom') {
                fetchClients('');
            }
            $watch('targetStatus', value => { 
                if (value !== 'custom') {
                    selectedClients = []; 
                    selectedClientsDetails = [];
                } else {
                    fetchClients('');
                }
            }); 
            $watch('searchQuery', value => fetchClients(value));
        "
        class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Section 1: Campaign Configuration -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Campaign Configuration</h3>
                <p class="text-sm text-gray-500">Name your campaign and set the email subject line.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="sm:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Campaign Name <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ $campaign->name }}" placeholder="e.g. Summer Special 2026" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Subject Line <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="subject" x-model="subject" placeholder="What your clients will see in their inbox" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Audience & Timing -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Audience & Timing</h3>
                <p class="text-sm text-gray-500">Select who will receive this email and when.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Schedule Date
                        </label>
                        <x-form.date-picker 
                            name="scheduled_at" 
                            placeholder="Select schedule date & time"
                            :enableTime="true"
                            :defaultDate="$campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d H:i') : null"
                        />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Target Audience <span class="text-error-500">*</span>
                        </label>
                        <div class="relative" x-data="{ 
                            open: false, 
                            search: '',
                            options: [
                                { value: 'none', label: 'None (Only External CSV Recipients)' },
                                { value: 'all', label: 'All CRM Clients' },
                                { value: 'custom', label: 'Specific CRM Clients (Select Below)' },
                                @foreach(['New', 'Interested', 'Contacted', 'In Progress', 'Follow Up', 'On Hold', 'Converted', 'Closed Won', 'Closed Lost', 'Not Interested'] as $st)
                                    { value: '{{ $st }}', label: '{{ $st }} Clients' },
                                @endforeach
                            ],
                            get filteredOptions() {
                                if (!this.search) return this.options;
                                return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            get selectedLabel() {
                                const option = this.options.find(o => o.value === targetStatus);
                                return option ? option.label : 'Select Target Audience';
                            }
                        }" @click.outside="open = false; search = ''">
                            <!-- Hidden native input to post value -->
                            <input type="hidden" name="target_status" :value="targetStatus">
                            
                            <!-- Trigger Button -->
                            <button type="button" @click="open = !open" 
                                class="flex items-center justify-between dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 text-left">
                                <span x-text="selectedLabel"></span>
                                <svg class="h-4 w-4 text-gray-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                class="absolute z-9999 mt-1.5 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-850 dark:bg-gray-950 p-2 space-y-2 max-h-64 overflow-y-auto" x-cloak>
                                <!-- Search box inside dropdown -->
                                <div class="relative">
                                    <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2 text-gray-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input type="text" x-model="search" placeholder="Search options..." @click.stop
                                        class="h-9 w-full rounded-md border border-gray-200 bg-gray-50 dark:bg-dark-900 pl-12 pr-3 py-1.5 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500/10 transition dark:border-gray-800 dark:text-white/90" />
                                </div>
                                
                                <!-- Options List -->
                                <div class="space-y-0.5">
                                    <template x-for="option in filteredOptions" :key="option.value">
                                        <button type="button" @click="targetStatus = option.value; open = false; search = ''"
                                            class="w-full text-left px-3 py-2 text-sm rounded-md transition-colors hover:bg-gray-105 dark:hover:bg-white/5"
                                            :class="targetStatus === option.value ? 'bg-brand-50 text-brand-800 font-semibold dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300'">
                                            <span x-text="option.label"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredOptions.length === 0" class="text-center py-4 text-xs text-gray-500">
                                        No options found
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2.5: CRM Client Selection (Conditional) -->
        <div x-show="targetStatus === 'custom'" x-transition 
            class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Select CRM Clients</h3>
                <p class="text-sm text-gray-500">Search and select the clients who should receive this campaign.</p>
            </div>
            <div class="p-7 space-y-6">
                <!-- Selected Clients list (Tags/Badges) -->
                <div x-show="selectedClientsDetails.length > 0" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Selected Clients (<span x-text="selectedClientsDetails.length"></span>)
                        </span>
                        <button type="button" @click="selectedClients = []; selectedClientsDetails = []" class="text-xs font-medium text-error-500 hover:text-error-600 transition-colors">
                            Clear Selection
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-800">
                        <template x-for="client in selectedClientsDetails" :key="client.id">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-brand-50 text-brand-800 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                <span x-text="client.name"></span>
                                <button type="button" @click="toggleClient(client)" class="text-brand-500 hover:text-brand-700 dark:hover:text-brand-300">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="relative">
                    <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" 
                           x-model.debounce.300ms="searchQuery" 
                           placeholder="Search CRM Clients by name or email..." 
                           class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-12 pr-12 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    <div class="absolute -translate-y-1/2 right-4 top-1/2 flex items-center" x-show="isSearching" x-cloak>
                        <svg class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Search Results Grid -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400" x-text="searchQuery ? 'Search Results' : 'Suggested Clients'"></span>
                        <template x-if="searchResults.length > 0">
                            <button type="button" @click="selectAllVisible()" class="text-xs font-medium text-brand-500 hover:text-brand-600 transition-colors">
                                Select All Visible
                            </button>
                        </template>
                    </div>
                    
                    <div class="max-h-80 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-gray-50 dark:bg-gray-900/50">
                        <!-- Empty State / Loading State -->
                        <div x-show="searchResults.length === 0 && !isSearching" class="text-center py-6 text-sm text-gray-500">
                            <span x-text="searchQuery ? 'No clients found matching \'' + searchQuery + '\'' : 'Type to search clients...'"></span>
                        </div>
                        
                        <div x-show="isSearching" class="text-center py-6 text-sm text-gray-500" x-cloak>
                            Searching clients...
                        </div>

                        <!-- Checkbox Grid -->
                        <div x-show="searchResults.length > 0 && !isSearching" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <template x-for="client in searchResults" :key="client.id">
                                <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-white dark:hover:bg-gray-850 transition cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-gray-700"
                                       :class="(selectedClients.includes(Number(client.id)) || selectedClients.includes(client.id.toString())) ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' : ''">
                                    <input type="checkbox" 
                                           :value="client.id" 
                                           :checked="selectedClients.map(Number).includes(Number(client.id))"
                                           @change="toggleClient(client)" 
                                           class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white" x-text="client.name"></span>
                                        <span class="text-[11px] text-gray-500" x-text="client.email || 'No Email'"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Hidden Inputs for Form Submission -->
                <template x-for="id in selectedClients" :key="id">
                    <input type="hidden" name="selected_clients[]" :value="id">
                </template>
            </div>
        </div>

        <!-- Section 3: External Recipients (Standalone) -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">External Recipients</h3>
                <p class="text-sm text-gray-500">Update via CSV or Excel without adding to CRM.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center dark:border-gray-800 transition-colors hover:border-brand-500 h-full flex flex-col justify-center min-h-[250px]"
                    :class="fileName ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/5' : ''">
                    <input type="file" name="external_file" id="external-file-upload" class="hidden" accept=".csv,.xlsx,.xls" 
                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                    <label for="external-file-upload" class="cursor-pointer block">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 text-brand-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <template x-if="fileName">
                                <div class="flex flex-col items-center">
                                    <p class="text-gray-700 dark:text-gray-400 font-bold text-lg" x-text="fileName"></p>
                                    <p class="text-xs text-gray-500 mt-1">New file selected</p>
                                    <button type="button" @click.prevent="fileName = ''; document.getElementById('external-file-upload').value = ''" class="mt-4 text-xs font-bold text-error-500 hover:text-error-600 underline">Remove File</button>
                                </div>
                            </template>
                            <template x-if="!fileName">
                                <div class="flex flex-col items-center">
                                    @if(!empty($campaign->external_emails))
                                        <p class="text-brand-500 dark:text-brand-400 font-bold text-lg">Currently has {{ count($campaign->external_emails) }} recipients</p>
                                        <p class="text-xs text-gray-500 mt-1">Click to replace with a new file</p>
                                    @else
                                        <p class="text-gray-700 dark:text-gray-400 font-bold text-lg">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-500 mt-1">CSV, XLSX or XLS (max. 10MB)</p>
                                    @endif
                                </div>
                            </template>
                        </div>
                    </label>
                </div>

                <!-- <div class="rounded-xl bg-brand-50 p-4 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20">
                    <h5 class="text-sm font-bold text-brand-800 dark:text-brand-400 mb-1">AI Smart Mapping</h5>
                    <p class="text-xs text-brand-700 dark:text-brand-500/80">
                        Our system will automatically detect and map your email columns.
                    </p>
                </div> -->
            </div>
        </div>

        <!-- Section 4: Content Design -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white">Email Content</h3>
                    <p class="text-sm text-gray-500">Design your email message using HTML or plain text.</p>
                </div>
                <div class="w-full sm:w-auto" style="width: 300px; min-width: 300px;">
                    <div class="relative" @click.outside="tplOpen = false; tplSearch = ''">
                        <!-- Hidden native input to post value -->
                        <input type="hidden" name="template_id" :value="selectedTemplate">
                        
                        <!-- Trigger Button -->
                        <button type="button" @click="tplOpen = !tplOpen" 
                            class="flex items-center justify-between dark:bg-dark-900 shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90 text-left">
                            <span x-text="selectedTemplateLabel"></span>
                            <svg class="h-4 w-4 text-gray-500 transition-transform duration-200" :class="tplOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="tplOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute z-9999 mt-1.5 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-850 dark:bg-gray-950 p-2 space-y-2 max-h-64 overflow-y-auto" x-cloak>
                            <!-- Search box inside dropdown -->
                            <div class="relative">
                                <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" x-model="tplSearch" placeholder="Search templates..." @click.stop
                                    class="h-9 w-full rounded-md border border-gray-200 bg-gray-50 dark:bg-dark-900 pl-12 pr-3 py-1.5 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500/10 transition dark:border-gray-800 dark:text-white/90" />
                            </div>
                            
                            <!-- Options List -->
                            <div class="space-y-0.5">
                                <template x-for="option in filteredTemplates" :key="option.value">
                                    <button type="button" @click="selectOption(option.value)"
                                        class="w-full text-left px-3 py-2 text-sm rounded-md transition-colors hover:bg-gray-100 dark:hover:bg-white/5"
                                        :class="String(selectedTemplate) === option.value ? 'bg-brand-50 text-brand-800 font-semibold dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300'">
                                        <span x-text="option.label"></span>
                                    </button>
                                </template>
                                <div x-show="filteredTemplates.length === 0" class="text-center py-4 text-xs text-gray-500">
                                    No options found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-7 space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Content (HTML)
                    </label>
                    <textarea name="content" id="editor" rows="10" class="w-full">{{ old('content', $campaign->body) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500 italic">Available tags: @{{name}}, @{{email}}, @{{location}}</p>
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
                Update Campaign
            </button>
        </div>
    </form>

    @push('scripts')
    <script>window.CKEDITOR_BASEPATH = 'https://cdn.ckeditor.com/4.22.1/full/';</script>
    <script src="{{ asset('js/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            CKEDITOR.replace('editor', {
                height: 350,
                allowedContent: true,
                removePlugins: 'exportpdf',
                versionCheck: false
            });
        });
    </script>
    @endpush
@endsection
