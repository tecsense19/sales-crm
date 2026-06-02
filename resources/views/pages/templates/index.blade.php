@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb pageTitle="Communication Templates" />
        @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('templates.create') }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                <path d="M5 10.0002H15.0006M10.0002 5V15.0006" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            New Template
        </a>
        @endif
    </div>

    <!-- @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif -->

    <div x-data="{
        showPreviewModal: false,
        previewTemplate: null,
        selected: [],
        templates: {{ $templates->map(fn($t) => ['id' => $t->id])->toJson() }},
        openPreview(t) {
            this.previewTemplate = t;
            this.showPreviewModal = true;
        },
        get isAllSelected() {
            return this.templates.length > 0 && this.selected.length === this.templates.length;
        },
        toggleAll() {
            if (this.isAllSelected) {
                this.selected = [];
            } else {
                this.selected = this.templates.map(t => t.id);
            }
        },
        toggleSelect(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                this.selected.push(id);
            }
        }
    }">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            
            <!-- Header -->
            <div class="flex flex-col justify-between gap-5 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Templates List</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage pre-defined email or message templates for client communication.</p>
                </div>
            </div>

            <!-- Search & Actions Bar -->
            <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.01]">
                <!-- Left: Search Input and Buttons -->
                <form action="{{ route('templates.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <div class="relative w-[260px]">
                        <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-400">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent pr-10 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                        @if(request('search'))
                            <a href="{{ route('templates.index') }}" class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
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
                        <a href="{{ route('templates.index') }}" class="shadow-theme-xs inline-flex h-10 items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear
                        </a>
                    @endif
                </form>
 
                 <!-- Right: Actions -->
                 <div class="flex items-center gap-3">

                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <div x-show="selected.length > 0" x-transition>
                        <form id="bulk-delete-templates-form" action="{{ route('templates.bulk-destroy') }}" method="POST" class="hidden">
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                        </form>
                        <button @click="
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'You want to delete ' + selected.length + ' selected templates?',
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
                                    document.getElementById('bulk-delete-templates-form').submit();
                                }
                            })
                        " 
                            class="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-error-50 px-4 text-xs font-medium text-error-600 transition hover:bg-error-100 dark:bg-error-500/10 dark:text-error-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            Delete Selected (<span x-text="selected.length"></span>)
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <th class="w-14 px-5 py-4 text-left">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="sr-only" @change="toggleAll()" :checked="isAllSelected">
                                    <span :class="isAllSelected ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px] transition">
                                        <svg x-show="isAllSelected" width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </label>
                            </th>
                            @endif
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Template Name</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Default Subject</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($templates as $template)
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50" :class="{ 'bg-gray-50/50 dark:bg-white/[0.01]': selected.includes({{ $template->id }}) }">
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <td class="px-5 py-4 whitespace-nowrap">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="sr-only" :checked="selected.includes({{ $template->id }})" @change="toggleSelect({{ $template->id }})">
                                    <span :class="selected.includes({{ $template->id }}) ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px] transition">
                                        <svg x-show="selected.includes({{ $template->id }})" width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </label>
                            </td>
                            @endif
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $template->name }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $template->subject ?? 'N/A' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase
                                    @if($template->type === 'email') bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400
                                    @elseif($template->type === 'whatsapp') bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400
                                    @else bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300 @endif">
                                    {{ $template->type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                     <button type="button" @click.stop="openPreview({{ json_encode($template) }})" title="Preview"
                                             class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                         <!-- Preview -->
                                     </button>
                                     
                                     @if(auth()->check() && auth()->user()->role === 'admin')
                                     <a href="{{ route('templates.edit', $template) }}" title="Edit"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                         <!-- Edit -->
                                     </a>
 
                                     <form action="{{ route('templates.destroy', $template) }}" method="POST" id="delete-template-form-{{ $template->id }}">
                                         @csrf @method('DELETE')
                                         <button type="button" title="Delete" @click="
                                             Swal.fire({
                                                 title: 'Delete Template?',
                                                 text: 'Are you sure you want to delete this template?',
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
                                                     document.getElementById('delete-template-form-{{ $template->id }}').submit();
                                                 }
                                             })
                                         " class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                             <!-- Delete -->
                                         </button>
                                     </form>
                                     @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-20 text-center text-gray-400 text-sm italic">No templates found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Preview Modal Overlay --}}
        <template x-teleport="body">
            <div x-show="showPreviewModal" x-cloak
                 style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:999999;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="showPreviewModal = false"
                 @click.self="showPreviewModal = false">

                {{-- Centering wrapper --}}
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:calc(100% - 2rem);max-width:42rem;max-height:85vh;display:flex;flex-direction:column;">

                {{-- Modal Card --}}
                <div style="background:#fff;border-radius:1rem;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);border:1px solid #e5e7eb;display:flex;flex-direction:column;max-height:85vh;overflow:hidden;width:100%;"
                     class="dark:bg-gray-900 dark:border-gray-700"
                     @click.stop>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-50 dark:bg-brand-500/15">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-600 dark:text-brand-400"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm font-bold text-gray-900 dark:text-white" x-text="previewTemplate?.name"></h2>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400" x-text="previewTemplate?.type"></span>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Template Preview</p>
                            </div>
                        </div>
                        <button @click="showPreviewModal = false" class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    {{-- Scrollable Body --}}
                    <div class="overflow-y-auto overflow-x-hidden flex-1 p-6 space-y-5">

                        {{-- Subject --}}
                        <div x-show="previewTemplate?.subject">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Subject Line</label>
                            <p class="mt-1.5 text-sm font-semibold text-gray-800 dark:text-white" x-text="previewTemplate?.subject"></p>
                        </div>

                        {{-- Message Body --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Message Body</label>
                                <button type="button"
                                        @click="if(previewTemplate?.content){ navigator.clipboard?.writeText(previewTemplate.content).catch(()=>{}); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Copied!', type: 'success' } })); }"
                                        class="inline-flex items-center gap-1.5 rounded-md bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 px-2.5 py-1 text-xs font-semibold hover:bg-brand-100 dark:hover:bg-brand-500/20 transition-colors">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    Copy Body
                                </button>
                            </div>
                            <div class="block w-full rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 p-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-sans prose dark:prose-invert max-w-none"
                                 x-show="previewTemplate?.content && (previewTemplate.content.includes('<') && previewTemplate.content.includes('>'))"
                                 x-html="previewTemplate?.content"></div>
                            <pre style="white-space: pre-wrap; word-break: break-word; min-width: 0; max-width: 100%; overflow-x: auto;" 
                                 class="block w-full rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 p-4 text-sm text-gray-700 dark:text-gray-300 font-mono leading-relaxed" 
                                 x-show="previewTemplate?.content && !(previewTemplate.content.includes('<') && previewTemplate.content.includes('>'))"
                                 x-text="previewTemplate?.content"></pre>
                        </div>

                        {{-- Placeholders --}}
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Available Placeholders</label>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="text-[11px] font-mono px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">{client_name}</span>
                                <span class="text-[11px] font-mono px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">{client_email}</span>
                                <span class="text-[11px] font-mono px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">{technology}</span>
                                <span class="text-[11px] font-mono px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">{project_link}</span>
                                <span class="text-[11px] font-mono px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">{assigned_user}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex-shrink-0">
                        <button @click="showPreviewModal = false"
                                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
                </div>{{-- /centering wrapper --}}
            </div>
        </template>

    </div>
@endsection
