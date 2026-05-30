<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $invoices = Invoice::with('client')->latest()->paginate(10);
        
        $stats = [
            'total_revenue' => Invoice::where('status', 'Paid')->sum('total_amount'),
            'pending' => Invoice::where('status', 'Sent')->sum('total_amount'),
            'paid_count' => Invoice::where('status', 'Paid')->count(),
            'overdue_count' => Invoice::where('status', 'Overdue')->count(),
            'total_count' => Invoice::count(),
        ];

        return view('pages.billing.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $clients = Client::all();
        $nextInvoiceNumber = 'INV-' . (Invoice::max('id') + 1);
        return view('pages.billing.create', compact('clients', 'nextInvoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|unique:invoices',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $data = $request->all();
                $total = 0;
                foreach ($request->items as $item) {
                    $total += ($item['quantity'] * $item['unit_price']);
                }
                $data['total_amount'] = $total;

                $invoice = Invoice::create($data);

                foreach ($request->items as $item) {
                    $item['total'] = $item['quantity'] * $item['unit_price'];
                    $invoice->items()->create($item);
                }
            });

            return redirect()->route('billing.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $billing)
    {
        return view('pages.billing.show', compact('billing'));
    }

    public function edit(Invoice $billing)
    {
        $clients = Client::all();
        return view('pages.billing.edit', compact('billing', 'clients'));
    }

    public function update(Request $request, Invoice $billing)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $billing) {
                $data = $request->all();

                if ($request->has('items') && is_array($request->items)) {
                    $total = 0;
                    foreach ($request->items as $item) {
                        $total += ($item['quantity'] * $item['unit_price']);
                    }
                    $data['total_amount'] = $total;

                    $billing->update($data);
                    $billing->items()->delete();
                    foreach ($request->items as $item) {
                        $item['total'] = $item['quantity'] * $item['unit_price'];
                        $billing->items()->create($item);
                    }
                } else {
                    $billing->update($data);
                }
            });

            return redirect()->route('billing.index')->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $billing)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($billing) {
                $billing->items()->delete();
                $billing->delete();
            });

            return redirect()->route('billing.index')->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:invoices,id',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
                \App\Models\InvoiceItem::whereIn('invoice_id', $validated['ids'])->delete();
                Invoice::whereIn('id', $validated['ids'])->delete();
            });
            return redirect()->route('billing.index')->with('success', count($validated['ids']) . ' invoices deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete invoices: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $invoices = Invoice::with('client')->get();
        $columns = [
            'invoice_number' => 'Invoice Number',
            'client.name' => 'Client Name',
            'total_amount' => 'Amount',
            'status' => 'Status',
            'issue_date' => 'Date',
            'due_date' => 'Due Date',
        ];

        return \App\Helpers\CsvExporter::export($invoices, 'invoices_export_' . date('Y-m-d') . '.csv', $columns);
    }
}
