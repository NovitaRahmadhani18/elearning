@props([
    'value',
])

<div>
    @forelse ($value->roles as $item)
        {{ App\Enums\RoleEnum::from($item->name)->badge() }}
    @empty
        <span class="text-gray-500">No roles assigned</span>
    @endforelse
</div>
