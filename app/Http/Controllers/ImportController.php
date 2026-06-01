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
            // Verify file headers first
            $headings = (new \Maatwebsite\Excel\HeadingRowImport)->toArray($request->file('file'));
            $firstSheetHeadings = $headings[0][0] ?? [];

            if (empty($firstSheetHeadings)) {
                return back()->with('error', 'Import failed: The uploaded file is empty or has no header row.');
            }

            $hasEmail = false;
            $hasName = false;

            foreach ($firstSheetHeadings as $heading) {
                if (empty($heading)) continue;
                $h = strtolower(trim($heading));
                if (in_array($h, ['email', 'email_address', 'mail', 'emailaddress'])) {
                    $hasEmail = true;
                }
                if (in_array($h, ['name', 'client_name', 'clientname', 'full_name', 'fullname'])) {
                    $hasName = true;
                }
            }

            if (!$hasEmail) {
                return back()->with('error', 'Import failed: The file must contain an "email" column header.');
            }
            if (!$hasName) {
                return back()->with('error', 'Import failed: The file must contain a "name" or "client_name" column header.');
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                Excel::import(new ClientsImport, $request->file('file'));
            });

            return redirect()->route('kanban.index')->with('success', 'Clients imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed and data was rolled back: ' . $e->getMessage());
        }
    }
}
