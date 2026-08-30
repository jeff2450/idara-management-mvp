<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Letter;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Angalia architecture.md §2.5 - "mail-merge" kutoka letter_templates kwenda
 * PDF halisi, iliyohifadhiwa kwenye `letters` (scoped kwa idara).
 */
class LetterController extends Controller
{
    public function index(Department $department): View
    {
        $this->authorize('viewAny', [Letter::class, $department]);

        $letters = $department->letters()->with(['template', 'generator'])->latest()->paginate(15);

        return view('letters.index', compact('department', 'letters'));
    }

    public function create(Department $department): View
    {
        $this->authorize('create', [Letter::class, $department]);

        $templates = LetterTemplate::orderBy('name')->get();

        return view('letters.create', compact('department', 'templates'));
    }

    public function store(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('create', [Letter::class, $department]);

        $data = $request->validate([
            'template_id' => ['required', 'exists:letter_templates,id'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'fields' => ['nullable', 'array'],
            'fields.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $template = LetterTemplate::findOrFail($data['template_id']);

        // Placeholders za kawaida zinajazwa kiotomatiki; zilizobaki zinatoka
        // kwenye fomu (fields[...]) - angalia LetterTemplate::placeholders().
        $mergeData = array_merge(
            [
                'idara' => $department->name,
                'tarehe' => now()->translatedFormat('d F Y'),
                'jina_mwanachama' => $data['recipient_name'] ?? '',
            ],
            $data['fields'] ?? []
        );

        $body = preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            fn ($matches) => e($mergeData[$matches[1]] ?? ''),
            $template->body_template
        );

        $pdf = Pdf::loadView('pdf.letter', [
            'body' => $body,
            'department' => $department,
        ]);

        $filename = 'letters/'.$department->id.'/'.Str::uuid().'.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        $letter = Letter::create([
            'department_id' => $department->id,
            'template_id' => $template->id,
            'generated_by' => $request->user()->id,
            'recipient_name' => $data['recipient_name'] ?? null,
            'file_path' => $filename,
        ]);

        return redirect()
            ->route('departments.letters.index', $department)
            ->with('status', 'Barua imezalishwa.');
    }

    public function download(Department $department, Letter $letter): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('view', $letter);

        abort_unless($letter->department_id === $department->id, 404);

        return Storage::disk('local')->download($letter->file_path, 'barua-'.$letter->id.'.pdf');
    }
}
