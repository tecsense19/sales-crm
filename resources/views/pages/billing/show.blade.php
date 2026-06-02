@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Invoice #{{ $billing->invoice_number }}" />
        <div class="flex gap-3">
            <a href="{{ route('billing.index') }}" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Billing
            </a>
            <a href="{{ route('billing.edit', $billing) }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5 4.16667L15.8333 7.5L6.66667 16.6667H3.33333V13.3333L12.5 4.16667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Edit Invoice
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between border-b border-gray-100 dark:border-gray-800 pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1">INVOICE</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium">#{{ $billing->invoice_number }}</p>
                <div class="mt-4">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase
                        @if($billing->status === 'Paid') bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500
                        @elseif($billing->status === 'Overdue') bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500
                        @else bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400 @endif">
                        {{ $billing->status }}
                    </span>
                </div>
            </div>
            <div class="mt-6 md:mt-0 md:text-right">
                <div class="text-gray-500 dark:text-gray-400 text-sm space-y-1">
                    <p><span class="font-bold text-gray-700 dark:text-gray-300">Issue Date:</span> {{ $billing->issue_date->format('M d, Y') }}</p>
                    <p><span class="font-bold text-gray-700 dark:text-gray-300">Due Date:</span> {{ $billing->due_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To:</h3>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">{{ $billing->client->name }}</h4>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $billing->client->email }}</p>
                @if($billing->client->mobile)
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $billing->client->mobile }}</p>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800 mb-8">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-5 py-3 font-bold text-gray-800 text-xs dark:text-gray-200 uppercase tracking-wider">Description</th>
                        <th class="px-5 py-3 w-24 text-center font-bold text-gray-800 text-xs dark:text-gray-200 uppercase tracking-wider">Qty</th>
                        <th class="px-5 py-3 w-32 text-right font-bold text-gray-800 text-xs dark:text-gray-200 uppercase tracking-wider">Unit Price</th>
                        <th class="px-5 py-3 w-32 text-right font-bold text-gray-800 text-xs dark:text-gray-200 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($billing->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $item->description }}</td>
                            <td class="px-5 py-4 text-sm text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                            <td class="px-5 py-4 text-sm text-right text-gray-600 dark:text-gray-400">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-5 py-4 text-sm text-right font-bold text-gray-800 dark:text-white/90">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col items-end">
            <div class="w-64 space-y-3">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span class="font-bold text-gray-800 dark:text-white">${{ number_format($billing->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Tax (0%)</span>
                    <span class="font-bold text-gray-800 dark:text-white">$0.00</span>
                </div>
                <div class="border-t border-gray-100 pt-3 flex justify-between">
                    <span class="font-bold text-gray-800 dark:text-white">Total Amount</span>
                    <span class="text-2xl font-bold text-brand-500">${{ number_format($billing->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
