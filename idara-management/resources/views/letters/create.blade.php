@extends('layouts.app')

@section('title', 'Zalisha Barua')

@section('content')
    <h1 class="text-lg font-semibold mb-6">Zalisha Barua - {{ $department->name }}</h1>

    @php
        // Placeholders 'idara' na 'tarehe' zinajazwa kiotomatiki na mfumo
        // (angalia LetterController@store), hivyo hazionyeshwi kwenye fomu.
        $templatesData = $templates->mapWithKeys(fn ($t) => [
            $t->id => array_values(array_diff($t->placeholders(), ['idara', 'tarehe', 'jina_mwanachama'])),
        ]);
    @endphp

    <form method="POST" action="{{ route('departments.letters.store', $department) }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-2xl"
          x-data="{ templateId: '{{ old('template_id', $templates->first()?->id) }}', fields: @js($templatesData) }">
        @csrf

        <div>
            <label for="template_id" class="block text-sm font-medium text-gray-700">Template</label>
            <select id="template_id" name="template_id" x-model="templateId" required
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                @forelse ($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                @empty
                    <option value="" disabled>Hakuna template - tengeneza moja kwanza</option>
                @endforelse
            </select>
        </div>

        <div>
            <label for="recipient_name" class="block text-sm font-medium text-gray-700">Jina la Mpokeaji (hiari)</label>
            <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
        </div>

        <template x-for="field in (fields[templateId] || [])" :key="field">
            <div>
                <label class="block text-sm font-medium text-gray-700 capitalize" x-text="field.replaceAll('_', ' ')"></label>
                <input :name="`fields[${field}]`" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
            </div>
        </template>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
            Zalisha PDF
        </button>
    </form>
@endsection
