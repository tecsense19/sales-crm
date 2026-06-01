@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-common.page-breadcrumb pageTitle="Create New Invoice" />
    </div>

    <!-- max-w-5xl -->
    <form action="{{ route('billing.store') }}" method="POST" class="mx-auto space-y-6"> 
        @csrf
        <input type="hidden" name="invoice_number" value="{{ $nextInvoiceNumber }}">
        
        <!-- Section 1: Client & Timing -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white">Invoice Information</h3>
                    <p class="text-sm text-gray-500">Generate a new billing record for a client.</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase text-gray-400">Invoice Number</span>
                    <p class="text-lg font-bold text-brand-500">{{ $nextInvoiceNumber }}</p>
                </div>
            </div>
            
            <div class="p-7 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Select Client <span class="text-error-500">*</span>
                        </label>
                        <select name="client_id" required class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90">
                            <option value="">— Choose Client —</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Issue Date <span class="text-error-500">*</span>
                        </label>
                        <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Due Date <span class="text-error-500">*</span>
                        </label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Line Items -->
        <div x-data="{ 
            items: [{ description: '', quantity: 1, unit_price: 0 }],
            addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0 }) },
            removeItem(index) { this.items.splice(index, 1) },
            calculateTotal() {
                return this.items.reduce((acc, item) => acc + (item.quantity * item.unit_price), 0).toFixed(2)
            }
        }" class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-7 py-5 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white">Services & Pricing</h3>
                    <p class="text-sm text-gray-500">List the services provided to the client.</p>
                </div>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-2 rounded-lg border border-brand-500 px-4 py-2 text-xs font-bold text-brand-500 hover:bg-brand-500 hover:text-white transition shadow-theme-xs">
                    + Add Line Item
                </button>
            </div>
            
            <div class="p-7">
                <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 font-bold text-gray-800 text-[11px] dark:text-gray-200 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 w-24 text-center font-bold text-gray-800 text-[11px] dark:text-gray-200 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 w-32 text-right font-bold text-gray-800 text-[11px] dark:text-gray-200 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 w-32 text-right font-bold text-gray-800 text-[11px] dark:text-gray-200 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 w-16 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-3">
                                        <input type="text" :name="'items['+index+'][description]'" x-model="item.description" placeholder="Item description" required
                                            class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" required
                                            class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-sm text-center focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" step="0.01" required
                                            class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-sm text-right focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 transition dark:border-gray-700 dark:text-white/90" />
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-gray-800 dark:text-white">
                                        <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-gray-400 hover:text-red-500">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex flex-col items-end">
                    <div class="w-64 space-y-3">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-gray-800 dark:text-white" x-text="calculateTotal()"></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Tax (0%)</span>
                            <span class="font-bold text-gray-800 dark:text-white">0.00</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 flex justify-between">
                            <span class="font-bold text-gray-800 dark:text-white">Total Amount</span>
                            <span class="text-xl font-bold text-brand-500" x-text="calculateTotal()"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Actions -->
        <div class="flex items-center justify-end gap-3 sticky bottom-6 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-lg z-10">
            <button type="button" onclick="window.history.back()" 
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-7 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:hover:bg-white/10 shadow-theme-xs">
                Cancel
            </button>
            <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-brand-600 shadow-theme-xs">
                Generate Invoice
            </button>
        </div>
    </form>
@endsection
