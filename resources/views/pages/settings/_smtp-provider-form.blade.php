{{-- Reusable form fields for Add/Edit Provider Modal --}}
<div class="space-y-4">

    {{-- Provider Name + Driver --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Provider Name *</label>
            <input type="text" name="name" placeholder="e.g. Brevo Primary, SMTP2GO Backup"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Provider *</label>
            <select name="driver"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none" required>
                <option value="brevo">Brevo</option>
                <option value="smtp2go">SMTP2GO</option>
            </select>
        </div>
    </div>

    {{-- API Key --}}
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">API Key *</label>
        <input type="password" name="api_key" placeholder="••••••••••••••••••••••"
            class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none"
            @if(!isset($editing)) required @endif>
        @if(isset($editing))
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Leave blank to keep your existing API key.</p>
        @else
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Brevo: find under Settings → SMTP &amp; API → API Keys. &nbsp;SMTP2GO: find under Senders → API Keys.</p>
        @endif
    </div>

    {{-- From Email + From Name --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">From Email *</label>
            <input type="email" name="from_email" placeholder="no-reply@yourdomain.com"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">From Name *</label>
            <input type="text" name="from_name" placeholder="Your Company Name"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none" required>
        </div>
    </div>

    {{-- Daily Limit + Priority --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Daily Email Limit *</label>
            <input type="number" name="daily_limit" placeholder="300" min="1"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none" required>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Brevo free: 300/day. SMTP2GO free: 1,000/mo.</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Priority (1=first) *</label>
            <input type="number" name="priority" placeholder="1" min="1" value="1"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300 outline-none" required>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Lower number = tried first. Auto-switches on limit.</p>
        </div>
    </div>

    {{-- Enable Checkbox --}}
    <div class="flex items-center gap-2 pt-1">
        <input type="checkbox" name="is_active" id="is_active_field" value="1" checked
            class="w-4 h-4 text-brand-500 rounded border-gray-300 dark:border-gray-700">
        <label for="is_active_field" class="text-sm text-gray-700 dark:text-gray-400">Enable this provider</label>
    </div>
</div>
