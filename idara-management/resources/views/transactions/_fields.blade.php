@php($t = $transaction ?? null)

<div>
    <label for="type" class="block text-sm font-medium text-gray-700">Aina</label>
    <input id="type" name="type" list="type-suggestions" value="{{ old('type', $t?->type) }}" required
           class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
    <datalist id="type-suggestions">
        <option value="Michango">
        <option value="Zawadi">
        <option value="Manunuzi">
        <option value="Matumizi ya Shughuli">
    </datalist>
</div>

<div>
    <label for="amount" class="block text-sm font-medium text-gray-700">Kiasi (TZS)</label>
    <input id="amount" type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $t?->amount) }}" required
           class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
</div>

<div>
    <label for="occurred_at" class="block text-sm font-medium text-gray-700">Tarehe</label>
    <input id="occurred_at" type="date" name="occurred_at"
           value="{{ old('occurred_at', $t?->occurred_at?->format('Y-m-d')) }}" required
           class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
</div>

<div>
    <label for="description" class="block text-sm font-medium text-gray-700">Maelezo (hiari)</label>
    <textarea id="description" name="description" rows="3"
              class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('description', $t?->description) }}</textarea>
</div>
