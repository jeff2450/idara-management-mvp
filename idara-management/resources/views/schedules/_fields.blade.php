@php($s = $schedule ?? null)

<div>
    <label for="title" class="block text-sm font-medium text-gray-700">Kichwa cha Shughuli</label>
    <input id="title" name="title" value="{{ old('title', $s?->title) }}" required
           class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
</div>

<div>
    <label for="planned_month" class="block text-sm font-medium text-gray-700">Mwezi Uliopangwa</label>
    <select id="planned_month" name="planned_month" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
        @foreach (range(1, 12) as $m)
            <option value="{{ $m }}" @selected(old('planned_month', $s?->planned_month) == $m)>
                {{ \Illuminate\Support\Carbon::create()->month($m)->translatedFormat('F') }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label for="description" class="block text-sm font-medium text-gray-700">Maelezo (hiari)</label>
    <textarea id="description" name="description" rows="3"
              class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('description', $s?->description) }}</textarea>
</div>
