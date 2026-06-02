@extends('layouts.fullscreen-layout', ['title' => 'Page Not Found'])

@section('content')
    <div class="relative flex h-screen w-full flex-col items-center justify-center p-6 bg-white dark:bg-gray-900 transition-colors duration-300">
        
        <div class="relative z-10 flex flex-col items-center text-center max-w-lg">
            <div class="mx-auto w-full max-w-[242px] text-center sm:max-w-[472px]">
                <h1 class="text-title-md xl:text-title-2xl mb-8 font-bold text-gray-800 dark:text-white/90">ERROR</h1>
                <img alt="404" loading="lazy" width="472" height="152" decoding="async" data-nimg="1" class="dark:hidden" src="/images/error/404.svg" style="color: transparent;">
                <img alt="404" loading="lazy" width="472" height="152" decoding="async" data-nimg="1" class="hidden dark:block" src="/images/error/404-dark.svg" style="color: transparent;">
                <p class="mb-6 mt-10 text-base text-gray-700 dark:text-gray-400 sm:text-lg">We can’t seem to find the page you are looking for!</p>
                <a class="shadow-theme-xs inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200" href="/">
                    <svg class="mr-2 -ml-0.5 h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home Page
                </a>
            </div>
        </div>
    </div>
@endsection
