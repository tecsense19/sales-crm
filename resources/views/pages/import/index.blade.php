@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Import Clients" />
        
        <div class="flex items-center gap-3">
            <a href="{{ route('kanban.index') }}" title="Back" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-750 shadow-theme-xs transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mt-6">
        <x-common.component-card title="Upload Data File">
            <form id="import-form" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div id="drop-zone" class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-700 transition-colors hover:border-brand-500">
                    <input type="file" name="file" id="file-upload" class="hidden" accept=".csv,.xlsx,.xls" />
                    <label for="file-upload" class="cursor-pointer">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 text-brand-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <p id="file-name" class="text-gray-700 dark:text-gray-400 font-medium">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500 mt-1">CSV, XLSX or XLS (max. 10MB)</p>
                            <button id="remove-file-btn" type="button" class="mt-3 text-xs font-bold text-red-500 hover:text-red-600 underline hidden">Remove File</button>
                        </div>
                    </label>
                </div>

                <!-- <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20">
                    <h5 class="text-sm font-bold text-blue-800 dark:text-blue-500 mb-1">AI Smart Mapping</h5>
                    <p class="text-xs text-blue-700 dark:text-blue-400/80">
                        Our system will automatically detect and map your columns (e.g. "Customer" will map to "Client Name").
                    </p>
                </div> -->

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-brand-600 shadow-theme-xs">
                    Process File
                </button>
            </form>
        </x-common.component-card>

        <div class="space-y-6">
            <x-common.component-card title="Instructions">
                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">1</span>
                        <span>Prepare your Excel or CSV file with headers in the first row.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">2</span>
                        <span>Upload the file above and click "Process".</span>
                    </li>
                    <!-- <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">3</span>
                        <span>Review the mapped columns and confirm the import count.</span>
                    </li> -->
                </ul>
            </x-common.component-card>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-upload');
        const fileNameDisplay = document.getElementById('file-name');
        const importForm = document.getElementById('import-form');
        const removeFileBtn = document.getElementById('remove-file-btn');

        // Allowed file types and max size
        const allowedExtensions = ['csv', 'xlsx', 'xls'];
        const maxFileSize = 10 * 1024 * 1024; // 10MB

        function validateFile(file) {
            if (!file) return false;
            
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: 'Invalid file format. Please upload CSV, XLSX or XLS files only.',
                        type: 'error'
                    }
                }));
                return false;
            }

            if (file.size > maxFileSize) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: 'File is too large. Maximum size allowed is 10MB.',
                        type: 'error'
                    }
                }));
                return false;
            }

            return true;
        }

        function resetFileInput() {
            fileInput.value = '';
            fileNameDisplay.textContent = 'Click to upload or drag and drop';
            fileNameDisplay.classList.add('text-gray-700', 'dark:text-gray-400');
            fileNameDisplay.classList.remove('text-brand-500', 'font-bold');
            removeFileBtn.classList.add('hidden');
        }

        // Handle Remove File button click
        removeFileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent triggering the file upload dialog
            resetFileInput();
        });

        // Prevent default browser drag actions
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        // Highlight drop zone on drag over
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-brand-500', 'bg-brand-50/20', 'dark:bg-brand-500/5');
            }, false);
        });

        // Unhighlight drop zone on drag leave / drop
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-brand-500', 'bg-brand-50/20', 'dark:bg-brand-500/5');
            }, false);
        });

        // Handle drop event
        dropZone.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files && files.length > 0) {
                const file = files[0];
                if (validateFile(file)) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                } else {
                    resetFileInput();
                }
            }
        }, false);

        // Update UI when file selection changes
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!validateFile(file)) {
                    resetFileInput();
                    return;
                }
                fileNameDisplay.textContent = file.name;
                fileNameDisplay.classList.remove('text-gray-700', 'dark:text-gray-400');
                fileNameDisplay.classList.add('text-brand-500', 'font-bold');
                removeFileBtn.classList.remove('hidden');
            } else {
                resetFileInput();
            }
        });

        // Validate on form submission
        importForm.addEventListener('submit', function(e) {
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: 'Please select or drag a file to upload first.',
                        type: 'error'
                    }
                }));
            }
        });
    </script>
@endsection
