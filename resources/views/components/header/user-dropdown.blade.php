@php
    $user = auth()->user();
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- User Button -->
    <button
        class="flex items-center text-gray-700 dark:text-gray-400 group"
        @click.prevent="toggleDropdown()"
        type="button"
    >
        <span class="mr-3 overflow-hidden rounded-full h-11 w-11 bg-gray-100 flex items-center justify-center border border-gray-200 dark:border-gray-800">
            @if($user && $user->profile_photo_path)
                <img src="{{ $user->profile_photo_url }}" alt="User" />
            @else
                <span class="font-bold text-brand-500 uppercase">{{ $user ? substr($user->name, 0, 1) : '?' }}</span>
            @endif
        </span>

       <span class="hidden sm:block mr-1.5 font-medium text-sm text-gray-700 dark:text-white/90">{{ $user->name ?? 'Guest' }}</span>

        <!-- Chevron Icon -->
        <svg
            class="w-4 h-4 transition-transform duration-200 text-gray-500 group-hover:text-gray-700 dark:text-gray-400"
            :class="{ 'rotate-180': dropdownOpen }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
        class="absolute right-0 mt-3 flex flex-col rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 z-50"
        style="display: none; width: 280px;"
    >
        <!-- User Info -->
        <div class="px-4 py-3 mb-1 border-b border-gray-100 dark:border-gray-800">
            <span class="block font-bold text-gray-800 text-sm dark:text-white truncate">{{ $user->name ?? 'Guest' }}</span>
            <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email ?? 'Not logged in' }}</span>
        </div>

        <!-- Menu Items -->
        <div class="space-y-1">
            <a href="/profile" class="flex items-center gap-4 px-4 py-3 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5 transition-colors group whitespace-nowrap">
                <span class="text-gray-400 group-hover:text-brand-500 transition-colors shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </span>
                Edit profile
            </a>
            <a href="/settings" class="flex items-center gap-4 px-4 py-3 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5 transition-colors group whitespace-nowrap">
                <span class="text-gray-400 group-hover:text-brand-500 transition-colors shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
                Account settings
            </a>
            <a href="/support" class="flex items-center gap-4 px-4 py-3 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5 transition-colors group whitespace-nowrap">
                <span class="text-gray-400 group-hover:text-brand-500 transition-colors shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                </span>
                Support
            </a>
        </div>

        <div class="my-2 border-t border-gray-100 dark:border-gray-800"></div>

        <!-- Sign Out -->
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="flex items-center w-full gap-4 px-4 py-3 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5 transition-colors group whitespace-nowrap" @click="closeDropdown()">
                <span class="text-gray-400 group-hover:text-error-500 transition-colors shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </span>
                Sign out
            </button>
        </form>
    </div>
</div>
