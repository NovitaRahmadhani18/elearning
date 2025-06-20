@props([
    'value',
])

<div class="flex flex-row gap-2">
    <a href="{{ route('admin.users.edit', $value) }}" class="rounded bg-yellow-500 px-4 py-2 font-bold text-white">
        Edit
    </a>

    <form
        action="{{ route('admin.users.destroy', $value) }}"
        method="post"
        x-target="user-management-table"
        @ajax:before="confirm('Are you sure?') || $event.preventDefault()"
    >
        @csrf
        @method('DELETE')
        <button class="rounded bg-red-500 px-4 py-2 font-bold text-white hover:bg-red-600" type="submit">Delete</button>
    </form>
</div>
