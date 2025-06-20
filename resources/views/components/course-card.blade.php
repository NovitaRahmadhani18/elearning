@props(['title', 'lessons', 'progress' => 75])

<div class="bg-white rounded-lg p-4 mb-4">
    <div class="flex items-start">
        <div class="w-16 h-16 bg-gray-200 rounded-md mr-4"></div>
        <div class="flex-1">
            <h3 class="font-semibold text-gray-800">{{ $title }}</h3>
            <p class="text-sm text-gray-600">{{ $lessons }} lesson</p>
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>
</div> 