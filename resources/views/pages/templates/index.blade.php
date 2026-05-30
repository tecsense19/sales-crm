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

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{
        showPreviewModal: false,
        previewTemplate: null,
        openPreview(t) {
            this.previewTemplate = t;
            this.showPreviewModal = true;
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

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-white/[0.02]">
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Template Name</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Default Subject</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($templates as $template)
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">
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
                                    <button type="button" @click.stop="openPreview({{ json_encode($template) }})"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Preview
                                    </button>
                                    
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <a href="{{ route('templates.edit', $template) }}"
                                       class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        Edit
                                    </a>

                                    <form action="{{ route('templates.destroy', $template) }}" method="POST" id="delete-template-form-{{ $template->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="
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
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-20 text-center text-gray-400 text-sm italic">No templates found.</td>
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
                                        @click="if(previewTemplate?.content){ navigator.clipboard?.writeText(previewTemplate.content).catch(()=>{}); Swal.fire({toast:true,position:'top-end',icon:'success',title:'Copied!',showConfirmButton:false,timer:2000}); }"
                                        class="inline-flex items-center gap-1.5 rounded-md bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 px-2.5 py-1 text-xs font-semibold hover:bg-brand-100 dark:hover:bg-brand-500/20 transition-colors">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    Copy Body
                                </button>
                            </div>
                            <pre style="white-space: pre-wrap; word-break: break-word; min-width: 0; max-width: 100%; overflow-x: auto;" class="block w-full rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 p-4 text-sm text-gray-700 dark:text-gray-300 font-mono leading-relaxed" x-text="previewTemplate?.content"></pre>
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
