@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Client Profile" />
        
        <div class="flex items-center gap-3">
            <a href="{{ route('kanban.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 shadow-theme-xs transition">
                <svg class="mr-2 -ml-0.5 h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
            <!-- @if(auth()->check() && (auth()->user()->role !== 'employee' || $client->assigned_to === auth()->id()))
            <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 shadow-theme-xs transition">
                Edit Client
            </a>
            @endif -->
        </div>
    </div>

    <div class="space-y-6">
        <!-- Profile Header Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex w-full flex-col items-center gap-6 xl:flex-row">
                    <div class="h-20 w-20 flex items-center justify-center rounded-full border border-gray-200 bg-brand-500/10 text-brand-500 font-bold text-2xl dark:border-gray-800">
                        {{ substr($client->name, 0, 1) }}
                    </div>
                    <div class="order-3 xl:order-2">
                        <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 xl:text-left dark:text-white/90">
                            {{ $client->name }}
                        </h4>
                        <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $client->technology ?? 'No Tech Specified' }}
                            </p>
                            <div class="hidden h-3.5 w-px bg-gray-300 xl:block dark:bg-gray-700"></div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $client->location ?? 'Unknown Location' }}
                            </p>
                        </div>
                    </div>
                    <div class="order-2 flex grow items-center gap-2 xl:order-3 xl:justify-end">
                        @foreach(['website', 'project_link', 'facebook', 'x', 'linkedin', 'instagram', 'youtube', 'whatsapp', 'telegram', 'teams'] as $social)
                            @if($client->$social)
                                @php
                                    $link = $client->$social;
                                    if ($social === 'whatsapp') {
                                        $link = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $link);
                                    } elseif ($social === 'telegram') {
                                        $link = 'https://t.me/' . ltrim($link, '@');
                                    } elseif ($social === 'teams') {
                                        $link = str_starts_with($link, 'http') ? $link : 'msteams://teams.microsoft.com/l/chat/0/0?users=' . $link;
                                    }
                                @endphp
                                <a href="{{ $link }}" target="_blank" title="{{ ucfirst(str_replace('_', ' ', $social)) }}"
                                    class="shadow-theme-xs flex h-11 w-11 items-center justify-center gap-2 rounded-full border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-all">
                                    @if($social == 'facebook')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.6666 11.2503H13.7499L14.5833 7.91699H11.6666V6.25033C11.6666 5.39251 11.6666 4.58366 13.3333 4.58366H14.5833V1.78374C14.3118 1.7477 13.2858 1.66699 12.2023 1.66699C9.94025 1.66699 8.33325 3.04771 8.33325 5.58342V7.91699H5.83325V11.2503H8.33325V18.3337H11.6666V11.2503Z" fill="" /></svg>
                                    @elseif($social == 'x')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.1708 1.875H17.9274L11.9049 8.75833L18.9899 18.125H13.4424L9.09742 12.4442L4.12578 18.125H1.36745L7.80912 10.7625L1.01245 1.875H6.70078L10.6283 7.0675L15.1708 1.875ZM14.2033 16.475H15.7308L5.87078 3.43833H4.23162L14.2033 16.475Z" fill="" /></svg>
                                    @elseif($social == 'linkedin')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.78381 4.16645C5.78351 4.84504 5.37181 5.45569 4.74286 5.71045C4.11391 5.96521 3.39331 5.81321 2.92083 5.32613C2.44836 4.83904 2.31837 4.11413 2.59216 3.49323C2.86596 2.87233 3.48886 2.47942 4.16715 2.49978C5.06804 2.52682 5.78422 3.26515 5.78381 4.16645ZM5.83381 7.06645H2.50048V17.4998H5.83381V7.06645ZM11.1005 7.06645H7.78381V17.4998H11.0672V12.0248C11.0672 8.97475 15.0422 8.69142 15.0422 12.0248V17.4998H18.3338V10.8914C18.3338 5.74978 12.4505 5.94145 11.0672 8.46642L11.1005 7.06645Z" fill="" /></svg>
                                    @elseif($social == 'instagram')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.8567 1.66699C11.7946 1.66854 12.2698 1.67351 12.6805 1.68573L12.8422 1.69102C13.0291 1.69766 13.2134 1.70599 13.4357 1.71641C14.3224 1.75738 14.9273 1.89766 15.4586 2.10391C16.0078 2.31572 16.4717 2.60183 16.9349 3.06503C17.3974 3.52822 17.6836 3.99349 17.8961 4.54141C18.1016 5.07197 18.2419 5.67753 18.2836 6.56433C18.2935 6.78655 18.3015 6.97088 18.3081 7.15775L18.3133 7.31949C18.3255 7.73011 18.3311 8.20543 18.3328 9.1433L18.3335 9.76463C18.3336 9.84055 18.3336 9.91888 18.3336 9.99972L18.3335 10.2348L18.333 10.8562C18.3314 11.794 18.3265 12.2694 18.3142 12.68L18.3089 12.8417C18.3023 13.0286 18.294 13.213 18.2836 13.4351C18.2426 14.322 18.1016 14.9268 17.8961 15.458C17.6842 16.0074 17.3974 16.4713 16.9349 16.9345C16.4717 17.397 16.0057 17.6831 15.4586 17.8955C14.9273 18.1011 14.3224 18.2414 13.4357 18.2831C13.2134 18.293 13.0291 18.3011 12.8422 18.3076L12.6805 18.3128C12.2698 18.3251 11.7946 18.3306 10.8567 18.3324L10.2353 18.333C10.1594 18.333 10.0811 18.333 10.0002 18.333H9.76516L9.14375 18.3325C8.20591 18.331 7.7306 18.326 7.31997 18.3137L7.15824 18.3085C6.97136 18.3018 6.78703 18.2935 6.56481 18.2831C5.67801 18.2421 5.07384 18.1011 4.5419 17.8955C3.99328 17.6838 3.5287 17.397 3.06551 16.9345C2.60231 16.4713 2.3169 16.0053 2.1044 15.458C1.89815 14.9268 1.75856 14.322 1.7169 13.4351C1.707 13.213 1.69892 13.0286 1.69238 12.8417L1.68714 12.68C1.67495 12.2694 1.66939 11.794 1.66759 10.8562L1.66748 9.1433C1.66903 8.20543 1.67399 7.73011 1.68621 7.31949L1.69151 7.15775C1.69815 6.97088 1.70648 6.78655 1.7169 6.56433C1.75786 5.67683 1.89815 5.07266 2.1044 4.54141C2.3162 3.9928 2.60231 3.52822 3.06551 3.06503C3.5287 2.60183 3.99398 2.31641 4.5419 2.10391C5.07315 1.89766 5.67731 1.75808 6.56481 1.71641C6.78703 1.70652 6.97136 1.69844 7.15824 1.6919L7.31997 1.68666C7.7306 1.67446 8.20591 1.6689 9.14375 1.6671L10.8567 1.66699ZM10.0002 5.83308C7.69781 5.83308 5.83356 7.69935 5.83356 9.99972C5.83356 12.3021 7.69984 14.1664 10.0002 14.1664C12.3027 14.1664 14.1669 12.3001 14.1669 9.99972C14.1669 7.69732 12.3006 5.83308 10.0002 5.83308ZM10.0002 7.49974C11.381 7.49974 12.5002 8.61863 12.5002 9.99972C12.5002 11.3805 11.3813 12.4997 10.0002 12.4997C8.6195 12.4997 7.50023 11.3809 7.50023 9.99972C7.50023 8.61897 8.61908 7.49974 10.0002 7.49974ZM14.3752 4.58308C13.8008 4.58308 13.3336 5.04967 13.3336 5.62403C13.3336 6.19841 13.8002 6.66572 14.3752 6.66572C14.9496 6.66572 15.4169 6.19913 15.4169 5.62403C15.4169 5.04967 14.9488 4.58236 14.3752 4.58308Z" fill="" /></svg>
                                    @elseif($social == 'youtube')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.646 5.361c-.22-1.636-1.503-2.92-3.14-3.141C13.738 2 10.005 2 10.005 2s-3.733 0-6.5.22C1.868 2.44.585 3.725.365 5.361.025 8.163.025 10 .025 10s0 1.838.34 4.639c.22 1.636 1.503 2.92 3.14 3.141 2.767.22 6.5.22 6.5.22s3.733 0 6.5-.22c1.637-.22 2.92-1.505 3.14-3.141.34-2.801.34-4.639.34-4.639s0-1.837-.34-4.639zm-11.75 7.688V6.95l5.526 3.05-5.526 3.05v-.001z" fill=""/></svg>
                                    @elseif($social == 'website')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.664-3.596A10.033 10.033 0 018.665 8H4.332zm-1.077 2H8.665a10.027 10.027 0 00-1.848 5.174A6.01 6.01 0 013.255 10zM11.335 10H16.745a6.01 6.01 0 01-3.563 5.174A10.027 10.027 0 0011.335 10zm5.41-2H11.335a10.033 10.033 0 00-2.669-3.569A6.012 6.012 0 0116.745 8z" clip-rule="evenodd" fill=""/></svg>
                                    @elseif($social == 'project_link')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5.172 8.35a2 2 0 11-2.828-2.828l3-3a2 2 0 012.828 0 1 1 0 001.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 00-1.414-1.414l-1.5 1.5z" clip-rule="evenodd" fill=""/></svg>
                                    @elseif($social == 'whatsapp')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a8 8 0 00-6.91 12L2 18l4.13-1.07A8 8 0 1010 2zm4.07 11.53c-.15.42-.87.81-1.2.86-.31.05-.72.07-2.18-.53-1.75-.72-2.88-2.52-2.96-2.63-.09-.11-.71-.95-.71-1.81s.44-1.27.6-1.44c.15-.16.33-.2.44-.2s.22 0 .31 0c.1 0 .22-.04.35.26.13.31.46 1.12.5 1.21.04.09.07.2 0 .31-.07.11-.11.22.31-.11.13-.24.29-.33.38-.11.11-.22.24-.11.44.11.2.49.82 1.05 1.32.72.64 1.32.84 1.51.93.2.09.31.07.42-.04.11-.13.5-.58.64-.78.13-.2.27-.16.46-.09.2.07 1.24.58 1.45.69.22.11.35.18.4.27.06.1.06.56-.09.98z" fill=""/></svg>
                                    @elseif($social == 'telegram')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.844 2.164L1.31 8.528c-1.436.575-1.428 1.376-.263 1.733l4.248 1.327 9.83-6.196c.465-.281.892-.131.537.185l-7.965 7.188-3.089 9.539c-.58.558-1.07.562-1.393-.162l-1.844-5.694 13.905-5.262z" fill=""/></svg>
                                    @elseif($social == 'teams')
                                        <svg class="fill-current w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.917 5.833A2.917 2.917 0 1010 2.917a2.917 2.917 0 002.917 2.917zm0 1.667c-1.492 0-4.5.75-4.5 2.25v2.75h9v-2.75c0-1.5-3.008-2.25-4.5-2.25zM6.25 6.667A2.083 2.083 0 104.167 4.583 2.083 2.083 0 006.25 6.667zm0 1.25c-.217 0-.458.017-.7.05-.708.85-1.15 1.95-1.15 3.283v2.917h3.333V11.25c0-.667.15-1.308.408-1.883-1.05-.883-1.891-1.45-1.891-1.45z" fill=""/></svg>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if(auth()->check() && (auth()->user()->role !== 'employee' || $client->assigned_to === auth()->id()))
                <a href="{{ route('clients.edit', $client) }}"
                    class="shadow-theme-xs flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 lg:inline-flex lg:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="" />
                    </svg>
                    Edit
                </a>
                @endif
            </div>
        </div>

        <!-- Personal Information Card -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Personal Information</h3>
                @if(auth()->check() && auth()->user()->role !== 'employee')
                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition shadow-theme-xs">
                    <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor" /></svg>
                    Edit
                </a>
                @endif
            </div>
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-7 2xl:gap-x-32">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">First Name</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ explode(' ', $client->name)[0] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Last Name</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ str_replace(explode(' ', $client->name)[0], '', $client->name) ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email address</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Phone</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->mobile_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Status</p>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase
                            @if(in_array($client->status, ['Closed Won', 'Converted'])) bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-500
                            @elseif(in_array($client->status, ['Closed Lost', 'Not Interested'])) bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-500
                            @elseif(in_array($client->status, ['Follow Up', 'On Hold'])) bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400
                            @elseif($client->status === 'Interested') bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400
                            @else bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500 @endif">
                            {{ $client->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Bio / Notes</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed font-medium">{{ $client->notes ?? 'No additional information provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address & Assignment Card -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Business Details</h3>
                @if(auth()->check() && (auth()->user()->role !== 'employee' || $client->assigned_to === auth()->id()))
                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition shadow-theme-xs">
                    <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor" /></svg>
                    Edit
                </a>
                @endif
            </div>
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-7 2xl:gap-x-32">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Country</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">City/State</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Technology Stack</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->technology ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Assigned User</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->assignedUser->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Next Follow-up</p>
                        <p class="text-sm font-semibold {{ $client->isFollowUpOverdue() ? 'text-red-500' : 'text-gray-800 dark:text-white' }}">
                            {{ $client->next_follow_up_date ? $client->next_follow_up_date->format('M d, Y') : 'Not Scheduled' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Social & Communication Card -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Social & Communication</h3>
            </div>
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-7 2xl:gap-x-32">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Website</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->website) <a href="{{ $client->website }}" target="_blank" class="text-brand-500 hover:underline">{{ $client->website }}</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Project Link</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->project_link) <a href="{{ $client->project_link }}" target="_blank" class="text-brand-500 hover:underline">{{ $client->project_link }}</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">LinkedIn</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->linkedin) <a href="{{ $client->linkedin }}" target="_blank" class="text-brand-500 hover:underline">View Profile</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Facebook</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->facebook) <a href="{{ $client->facebook }}" target="_blank" class="text-brand-500 hover:underline">View Page</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">X (Twitter)</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->x) <a href="{{ $client->x }}" target="_blank" class="text-brand-500 hover:underline">View Profile</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Instagram</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->instagram) <a href="{{ $client->instagram }}" target="_blank" class="text-brand-500 hover:underline">View Profile</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">YouTube</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->youtube) <a href="{{ $client->youtube }}" target="_blank" class="text-brand-500 hover:underline">View Channel</a> @else N/A @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">WhatsApp</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->whatsapp ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Telegram</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->telegram ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Teams</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $client->teams ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Source URL</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            @if($client->source_url) <a href="{{ $client->source_url }}" target="_blank" class="text-brand-500 hover:underline">{{ $client->source_url }}</a> @else N/A @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
