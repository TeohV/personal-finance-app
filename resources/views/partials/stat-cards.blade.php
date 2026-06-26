<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @foreach ($cards as $card)
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col justify-center">
            <span class="text-sm font-medium text-gray-500 mb-1">
                {{ $card['label'] }}
            </span>
            <span class="text-2xl font-bold {{ $card['value_class'] ?? 'text-gray-900' }}">
                {{ $card['value'] }}
            </span>
        </div>
    @endforeach
</div>