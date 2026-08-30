@extends('layouts.app')

@section('title', 'Hariri Muamala')

@section('content')
    <h1 class="text-lg font-semibold mb-6">Hariri Muamala - {{ $department->name }}</h1>

    <form method="POST" action="{{ route('departments.transactions.update', [$department, $transaction]) }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-lg">
        @csrf @method('PUT')
        @include('transactions._fields')

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">Sasisha</button>
            <a href="{{ route('departments.transactions.index', $department) }}" class="text-sm text-gray-500 hover:underline">Ghairi</a>
        </div>
    </form>
@endsection
