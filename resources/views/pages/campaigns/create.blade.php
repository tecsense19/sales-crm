@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Create New Campaign" />
    </div>

    <form action="{{ route('campaigns.store') }}" method="POST" enctype="multipart/form-data" 
        x-data="{ targetStatus: 'none', selectedClients: [], fileName: '', dragOver: false, selectedTemplate: '', subject: '', content: '', templates: @js($templates) }" 
        x-init="$watch('targetStatus', value => { if (value !== 'custom') selectedClients = [] })"
        @submit="if (targetStatus === 'none' && !fileName) { $event.preventDefault(); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Please upload an external CSV/Excel file when targeting only external recipients.', type: 'error' } })); }"
        class="space-y-6">
        @csrf
        
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
                        <input type="text" name="name" placeholder="e.g. Summer Special 2026" required
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
                        <input type="datetime-local" name="scheduled_at" 
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Target Audience <span class="text-error-500">*</span>
                        </label>
                        <select name="target_status" x-model="targetStatus" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90">
                            <option value="none">None (Only External CSV Recipients)</option>
                            <option value="all">All CRM Clients</option>
                            <option value="custom">Specific CRM Clients (Select Below)</option>
                            @foreach(['New', 'Interested', 'Contacted', 'In Progress', 'Follow Up', 'On Hold', 'Converted', 'Closed Won', 'Closed Lost', 'Not Interested'] as $st)
                                <option value="{{ $st }}">{{ $st }} Clients</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2.5: CRM Client Selection (Conditional) -->
        <div x-show="targetStatus === 'custom'" x-transition 
            class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Select CRM Clients</h3>
                <p class="text-sm text-gray-500">Manually pick the clients who should receive this campaign.</p>
            </div>
            <div class="p-7">
                <div class="max-h-80 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-gray-50 dark:bg-gray-900/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($clients as $client)
                            <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                                <input type="checkbox" name="selected_clients[]" value="{{ $client->id }}" x-model="selectedClients" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->name }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $client->email ?? 'No Email' }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: External Recipients (Standalone) -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Upload External Recipients</h3>
                <p class="text-sm text-gray-500">Send via CSV or Excel without adding to CRM.</p>
            </div>
            <div class="p-7 space-y-6">
                <div class="rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center dark:border-gray-800 transition-colors hover:border-brand-500 h-full flex flex-col justify-center min-h-[250px]"
                    :class="fileName || dragOver ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/5' : ''"
                    @dragover.prevent="dragOver = true"
                    @dragenter.prevent="dragOver = true"
                    @dragleave.prevent="dragOver = false"
                    @drop.prevent="
                        dragOver = false;
                        const files = $event.dataTransfer.files;
                        if (files && files.length > 0) {
                            const file = files[0];
                            const allowed = ['csv', 'xlsx', 'xls'];
                            const ext = file.name.split('.').pop().toLowerCase();
                            if (!allowed.includes(ext)) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Invalid file format. Please upload CSV, XLSX or XLS files only.', type: 'error' } }));
                                return;
                            }
                            if (file.size > 10 * 1024 * 1024) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'File is too large. Maximum size allowed is 10MB.', type: 'error' } }));
                                return;
                            }
                            $refs.externalFileInput.files = files;
                            fileName = file.name;
                        }
                    ">
                    <input type="file" name="external_file" id="external-file-upload" class="hidden" accept=".csv,.xlsx,.xls" x-ref="externalFileInput"
                        @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const allowed = ['csv', 'xlsx', 'xls'];
                                const ext = file.name.split('.').pop().toLowerCase();
                                if (!allowed.includes(ext)) {
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Invalid file format. Please upload CSV, XLSX or XLS files only.', type: 'error' } }));
                                    $event.target.value = '';
                                    fileName = '';
                                    return;
                                }
                                if (file.size > 10 * 1024 * 1024) {
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'File is too large. Maximum size allowed is 10MB.', type: 'error' } }));
                                    $event.target.value = '';
                                    fileName = '';
                                    return;
                                }
                                fileName = file.name;
                            } else {
                                fileName = '';
                            }
                        " />
                    <label for="external-file-upload" class="cursor-pointer block w-full h-full">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 text-brand-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 dark:text-gray-400 font-bold text-lg" x-text="fileName ? fileName : 'Click to upload or drag and drop'"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="fileName ? 'File selected successfully' : 'CSV, XLSX or XLS (max. 10MB)'"></p>
                            <template x-if="fileName">
                                <button type="button" @click.prevent="fileName = ''; document.getElementById('external-file-upload').value = ''" class="mt-4 text-xs font-bold text-error-500 hover:text-error-600 underline">Remove File</button>
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
                    <select name="template_id" x-model="selectedTemplate" @change="
                        const temp = templates.find(t => t.id == selectedTemplate);
                        if (temp) {
                            subject = temp.subject;
                            content = temp.content;
                        } else {
                            subject = '';
                            content = '';
                        }
                    " class="dark:bg-dark-900 shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90">
                        <option value="">-- Choose Template --</option>
                        @foreach($templates as $temp)
                            <option value="{{ $temp->id }}">{{ $temp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="p-7 space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Content (HTML)
                    </label>
                    <div class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent text-sm text-gray-800 dark:text-white/90 focus-within:border-brand-300 focus-within:ring-3 focus-within:ring-brand-500/10 transition overflow-hidden">
                        <div id="editor-container" style="min-height: 300px;" class="bg-white dark:bg-dark-950 text-gray-800 dark:text-white/90 border-0"></div>
                    </div>
                    <input type="hidden" name="content" x-model="content" id="content-input" />
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
                Create Campaign
            </button>
        </div>
    </form>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <style>
        /* Quill Dark Mode Adjustments */
        .dark .ql-toolbar {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .ql-toolbar .ql-stroke {
            stroke: #d1d5db !important;
        }
        .dark .ql-toolbar .ql-fill {
            fill: #d1d5db !important;
        }
        .dark .ql-toolbar .ql-picker {
            color: #d1d5db !important;
        }
        .dark .ql-container {
            border-color: #374151 !important;
            background-color: #111827 !important;
        }
        .dark .ql-editor {
            color: #f3f4f6 !important;
        }
        .ql-toolbar {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #e5e7eb !important;
        }
        .ql-container {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #e5e7eb !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('editor-container');
            if (!container) return;

            const quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'clean']
                    ]
                }
            });

            function plainTextToHtml(text) {
                if (!text) return '';
                if (text.includes('<p>') || text.includes('<br>') || text.includes('<div>') || text.includes('</')) {
                    return text;
                }
                return text
                    .split(/\r?\n\r?\n/)
                    .map(para => {
                        const cleanPara = para.replace(/\r?\n/g, '<br>');
                        return `<p>${cleanPara}</p>`;
                    })
                    .join('');
            }

            const form = container.closest('form');
            setTimeout(() => {
                const data = Alpine.$data(form);
                if (data) {
                    if (data.content) {
                        quill.root.innerHTML = plainTextToHtml(data.content);
                    }

                    quill.on('text-change', () => {
                        const html = quill.root.innerHTML;
                        data.content = (html === '<p><br></p>' || html === '') ? '' : html;
                    });

                    data.$watch('content', value => {
                        const htmlValue = plainTextToHtml(value || '');
                        if (quill.root.innerHTML !== htmlValue) {
                            quill.root.innerHTML = htmlValue;
                        }
                    });
                }
            }, 100);
        });
    </script>
    @endpush
@endsection
