@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Create New Template" />
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('templates.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Template Configuration</h3>
                <p class="text-sm text-gray-500">Name your template and select type and default subject.</p>
            </div>
            
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="sm:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Template Name <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="name" placeholder="e.g. New lead introduction" value="{{ old('name') }}" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <div class="sm:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Template Type <span class="text-error-500">*</span>
                        </label>
                        <select name="type" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90">
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email Template</option>
                            <option value="whatsapp" {{ old('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp Template</option>
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General Message Template</option>
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Default Subject
                        </label>
                        <input type="text" name="subject" placeholder="Default email subject (optional)" value="{{ old('subject') }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Template Content <span class="text-error-500">*</span>
                    </label>
                    <textarea name="content" rows="12" placeholder="Hi {client_name}, ..." required
                        class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition font-mono">{{ old('content') }}</textarea>
                    
                    <div class="mt-3 rounded-xl bg-brand-50 p-4 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20">
                        <h5 class="text-xs font-bold text-brand-800 dark:text-brand-400 mb-1.5">Template Variables / Placeholders</h5>
                        <p class="text-xs text-brand-700 dark:text-brand-500/80 mb-2">
                            Use the following placeholder keywords in brackets. They will be auto-replaced when using this template with a client.
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-brand-200 dark:border-brand-500/20">{client_name}</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-brand-200 dark:border-brand-500/20">{client_email}</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-brand-200 dark:border-brand-500/20">{technology}</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-brand-200 dark:border-brand-500/20">{project_link}</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-brand-200 dark:border-brand-500/20">{assigned_user}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-lg">
            <a href="{{ route('templates.index') }}" 
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-7 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:hover:bg-white/10 shadow-theme-xs">
                Cancel
            </a>
            <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-brand-600 shadow-theme-xs">
                Create Template
            </button>
        </div>
    </form>
@endsection
