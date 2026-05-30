@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="SMTP Settings" />
    </div>

    <form action="{{ route('smtp.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <!-- Section 1: Server Config -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                        <h3 class="font-bold text-gray-800 dark:text-white">Server Configuration</h3>
                        <p class="text-sm text-gray-500">Setup your outbound email server details.</p>
                    </div>
                    <div class="p-7 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="md:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">SMTP Host</label>
                                <input type="text" name="host" value="{{ $setting->host }}" placeholder="smtp.gmail.com"
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Port</label>
                                <input type="number" name="port" value="{{ $setting->port }}" placeholder="587"
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Encryption</label>
                                <select name="encryption" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90">
                                    <option value="tls" {{ $setting->encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $setting->encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ $setting->encryption == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Credentials -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                        <h3 class="font-bold text-gray-800 dark:text-white">Authentication</h3>
                        <p class="text-sm text-gray-500">Secure credentials for your SMTP server.</p>
                    </div>
                    <div class="p-7 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Username</label>
                                <input type="text" name="username" value="{{ $setting->username }}" placeholder="SMTP Username"
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password / App Password</label>
                                <input type="password" name="password" value="{{ $setting->password }}" placeholder="••••••••"
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Sender Info -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800">
                        <h3 class="font-bold text-gray-800 dark:text-white">Sender Identity</h3>
                        <p class="text-sm text-gray-500">How emails appear to your clients.</p>
                    </div>
                    <div class="p-7 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Name</label>
                                <input type="text" name="from_name" value="{{ $setting->from_name }}" placeholder="CRM System"
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Email</label>
                                <input type="email" name="from_email" value="{{ $setting->from_email }}" placeholder="no-reply@crm.com"
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6" x-data="{
                applyPreset(host, port, encryption) {
                    document.querySelector('input[name=host]').value = host;
                    document.querySelector('input[name=port]').value = port;
                    document.querySelector('select[name=encryption]').value = encryption;
                }
            }">
                <!-- Sidebar info -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h4 class="font-bold text-gray-800 dark:text-white mb-4">Quick Presets</h4>
                    <div class="space-y-3">
                        <div @click="applyPreset('smtp.gmail.com', 587, 'tls')" 
                            class="p-3 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 cursor-pointer hover:border-brand-300 transition-all group">
                            <p class="text-sm font-bold group-hover:text-brand-500">Gmail</p>
                            <p class="text-[11px] text-gray-500 mt-1">Host: smtp.gmail.com | Port: 587</p>
                        </div>
                        <div @click="applyPreset('smtp.office365.com', 587, 'tls')" 
                            class="p-3 rounded-xl border border-gray-100 dark:border-gray-800 cursor-pointer hover:border-brand-300 transition-all group">
                            <p class="text-sm font-bold group-hover:text-brand-500">Outlook</p>
                            <p class="text-[11px] text-gray-500 mt-1">Host: smtp.office365.com | Port: 587</p>
                        </div>
                        <div @click="applyPreset('mail.smtpgo.com', 587, 'tls')" 
                            class="p-3 rounded-xl border border-gray-100 dark:border-gray-800 cursor-pointer hover:border-brand-300 transition-all group">
                            <p class="text-sm font-bold group-hover:text-brand-500">SMTP.go</p>
                            <p class="text-[11px] text-gray-500 mt-1">Host: mail.smtpgo.com | Port: 587</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Actions -->
        <div class="flex items-center justify-end gap-3 sticky bottom-6 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-lg z-20">
            <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-brand-600 shadow-theme-xs">
                Save Settings
            </button>
        </div>
    </form>
@endsection
