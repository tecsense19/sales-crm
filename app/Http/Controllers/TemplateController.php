<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $query = Template::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        $templates = $query->orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);
        }

        return view('pages.templates.index', compact('templates'));
    }

    public function create()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('pages.templates.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'type' => 'required|string|in:email,whatsapp,general',
            'content' => 'required|string',
        ]);

        Template::create($validated);

        return redirect()->route('templates.index')->with('success', 'Template created successfully.');
    }

    public function show(Template $template)
    {
        if (!auth()->check()) {
            abort(403);
        }

        return view('pages.templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('pages.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'type' => 'required|string|in:email,whatsapp,general',
            'content' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->route('templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(Template $template)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:templates,id',
        ]);

        Template::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('templates.index')->with('success', 'Selected templates deleted successfully.');
    }
}
