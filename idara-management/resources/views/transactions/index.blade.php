@extends('layouts.app')

@section('title', 'Miamala - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">Miamala - {{ $department->name }}</h1>
            <a href="{{ route('departments.show', $department) }}" class="text-xs text-gray-400 hover:underline">&larr; Rudi kwenye Idara</a>
        </div>
        <a href="{{ route('departments.transactions.create', $department) }}"
           class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
            + Ongeza Muamala
        </a>
    </div>

    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-md px-3 py-2 mb-4">
        Data hii ni ya fedha - inaonekana kwa Kiongozi na Admin pekee, siyo wanachama wa kawaida.
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Tarehe</th>
                    <th class="px-4 py-2 text-left">Aina</th>
                    <th class="px-4 py-2 text-left">Maelezo</th>
                    <th class="px-4 py-2 text-right">Kiasi</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($transactions as $transaction)
                    <tr>
                        <td class="px-4 py-2">{{ $transaction->occurred_at->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-2">{{ $transaction->type }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $transaction->description }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format((float) $transaction->amount, 2) }}</td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <a href="{{ route('departments.transactions.edit', [$department, $transaction]) }}" class="text-gray-500 hover:text-indigo-700">Hariri</a>
                            <form method="POST" action="{{ route('departments.transactions.destroy', [$department, $transaction]) }}"
                                  class="inline" onsubmit="return confirm('Futa muamala huu?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline ml-2">Futa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Hakuna muamala bado.</td></tr>
                @endforelse
            </tbody>
            @if ($transactions->isNotEmpty())
                <tfoot>
                    <tr class="bg-gray-50 font-medium">
                        <td colspan="3" class="px-4 py-2">Jumla (ukurasa huu)</td>
                        <td class="px-4 py-2 text-right">{{ number_format((float) $total, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
@endsection
