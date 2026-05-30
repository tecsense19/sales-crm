<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('pages.import.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                Excel::import(new ClientsImport, $request->file('file'));
            });

            return redirect()->route('clients.index')->with('success', 'Clients imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed and data was rolled back: ' . $e->getMessage());
        }
    }
}
