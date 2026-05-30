{{-- Notification Dropdown Component --}}
@php
    $user = auth()->user();
    $notifications = collect();

    if ($user) {
        $today = today();
        $query = \App\Models\Client::whereNotIn('status', ['Closed Won', 'Closed Lost'])
            ->whereNotNull('next_followup_date')
            ->with('assignedUser');

        if ($user->role === 'admin') {
            // Admins see all due today or overdue clients
            $query->whereDate('next_followup_date', '<=', $today);
        } else {
            // Team members see only their assigned due today or overdue clients
            $query->where('assigned_to', $user->id)
                  ->whereDate('next_followup_date', '<=', $today);
        }

        // Fetch up to 10 notifications
        $clients = $query->orderBy('next_followup_date', 'asc')->take(10)->get();

        foreach ($clients as $client) {
            $isOverdue = $client->next_followup_date->lt($today);
            $notifications->push([
                'id' => $client->id,
                'clientName' => $client->name,
                'action' => $isOverdue ? 'is overdue for follow-up' : 'is due for follow-up today',
                'time' => $client->next_followup_date->diffForHumans(),
                'date' => $client->next_followup_date->format('M d, Y'),
                'type' => $isOverdue ? 'Overdue' : 'Due Today',
                'status' => $isOverdue ? 'overdue' : 'due',
                'url' => url('/clients/' . $client->id . '/edit'),
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($client->name) . '&background=' . ($isOverdue ? 'fecaca&color=991b1b' : 'fef3c7&color=92400e') . '&size=80&bold=true',
            ]);
        }
    }
    
    $notificationCount = $notifications->count();
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    notifying: {{ $notificationCount > 0 ? 'true' : 'false' }},
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
        this.notifying = false;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    },
    handleItemClick() {
        this.closeDropdown();
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        <!-- Notification Badge -->
        <span
            x-show="notifying"
            class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400"
        >
            <span
                class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"
            ></span>
        </span>

        <!-- Bell Icon -->
        <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill=""
            />
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0 z-50"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Notifications ({{ $notificationCount }})
            </h5>

            <button @click="closeDropdown()" class="text-gray-500 dark:text-gray-400" type="button">
                <svg
                    class="fill-current"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                        fill=""
                    />
                </svg>
            </button>
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar flex-grow">
            @if($notifications->isEmpty())
                <li class="p-8 text-center text-gray-500 text-sm flex flex-col items-center justify-center h-full">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>All caught up! No pending follow-up tasks.</span>
                </li>
            @else
                @foreach ($notifications as $notification)
                    <li @click="handleItemClick()">
                        <a
                            class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5 transition"
                            href="{{ $notification['url'] }}"
                        >
                            <span class="relative block w-full h-10 rounded-full z-1 max-w-10 flex-shrink-0">
                                <img src="{{ $notification['avatar'] }}" alt="Client Avatar" class="overflow-hidden rounded-full h-10 w-10" />
                                <span
                                    class="absolute bottom-0 right-0 z-10 h-2.5 w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900 {{ $notification['status'] === 'overdue' ? 'bg-red-500' : 'bg-amber-500' }}"
                                ></span>
                            </span>

                            <span class="block">
                                <span class="mb-1 block text-theme-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-gray-800 dark:text-white/90">
                                        {{ $notification['clientName'] }}
                                    </span>
                                    {{ $notification['action'] }}
                                </span>

                                <span class="flex items-center gap-2 text-theme-xs">
                                    <span class="font-semibold px-2 py-0.5 rounded text-[10px] {{ $notification['status'] === 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        {{ $notification['type'] }}
                                    </span>
                                    <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                                    <span class="text-gray-400">{{ $notification['time'] }}</span>
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>

        <!-- View All Button -->
        <a
            href="{{ url('/clients') }}"
            class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition"
        >
            View All Clients
        </a>
    </div>
    <!-- Dropdown End -->
</div>
