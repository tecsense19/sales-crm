@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Import Clients" />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-common.component-card title="Upload Data File">
            <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-700 transition-colors hover:border-brand-500">
                    <input type="file" name="file" id="file-upload" class="hidden" accept=".csv,.xlsx,.xls" />
                    <label for="file-upload" class="cursor-pointer">
                        <div class="flex flex-col items-center">
                            <span class="text-4xl mb-4" id="file-icon">📄</span>
                            <p id="file-name" class="text-gray-700 dark:text-gray-400 font-medium">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500 mt-1">CSV, XLSX or XLS (max. 10MB)</p>
                        </div>
                    </label>
                </div>

                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20">
                    <h5 class="text-sm font-bold text-blue-800 dark:text-blue-500 mb-1">AI Smart Mapping</h5>
                    <p class="text-xs text-blue-700 dark:text-blue-400/80">
                        Our system will automatically detect and map your columns (e.g. "Customer" will map to "Client Name").
                    </p>
                </div>

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
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">3</span>
                        <span>Review the mapped columns and confirm the import count.</span>
                    </li>
                </ul>
            </x-common.component-card>
        </div>
    </div>

    <script>
        document.getElementById('file-upload').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Click to upload or drag and drop';
            const fileNameDisplay = document.getElementById('file-name');
            const fileIcon = document.getElementById('file-icon');
            
            if (e.target.files[0]) {
                fileNameDisplay.textContent = fileName;
                fileNameDisplay.classList.remove('text-gray-700', 'dark:text-gray-400');
                fileNameDisplay.classList.add('text-brand-500', 'font-bold');
                fileIcon.textContent = '✅';
            } else {
                fileNameDisplay.textContent = 'Click to upload or drag and drop';
                fileNameDisplay.classList.add('text-gray-700', 'dark:text-gray-400');
                fileNameDisplay.classList.remove('text-brand-500', 'font-bold');
                fileIcon.textContent = '📄';
            }
        });
    </script>
@endsection
