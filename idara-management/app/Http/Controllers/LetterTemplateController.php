<?php

namespace App\Http\Controllers;

use App\Models\LetterTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Angalia architecture.md §2.5 - templates ni za JUMLA, Admin pekee
 * anaziunda/kuzihariri.
 */
class LetterTemplateController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', LetterTemplate::class);

        $templates = LetterTemplate::withCount('letters')->latest()->paginate(15);

        return view('letter-templates.index', compact('templates'));
    }

    public function create(): View
    {
        $this->authorize('create', LetterTemplate::class);

        return view('letter-templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', LetterTemplate::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'body_template' => ['required', 'string', 'max:20000'],
        ]);

        $template = LetterTemplate::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('letter-templates.index')
            ->with('status', "Template '{$template->name}' imeundwa.");
    }

    public function edit(LetterTemplate $letterTemplate): View
    {
        $this->authorize('update', $letterTemplate);

        return view('letter-templates.edit', ['template' => $letterTemplate]);
    }

    public function update(Request $request, LetterTemplate $letterTemplate): RedirectResponse
    {
        $this->authorize('update', $letterTemplate);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'body_template' => ['required', 'string', 'max:20000'],
        ]);

        $letterTemplate->update($data);

        return redirect()
            ->route('letter-templates.index')
            ->with('status', "Template '{$letterTemplate->name}' imesasishwa.");
    }

    public function destroy(LetterTemplate $letterTemplate): RedirectResponse
    {
        $this->authorize('delete', $letterTemplate);

        $letterTemplate->delete();

        return redirect()
            ->route('letter-templates.index')
            ->with('status', 'Template imefutwa.');
    }
}
