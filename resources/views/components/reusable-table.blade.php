<div id="{{ $tableData->id }}" class="overflow-hidden rounded-lg border border-primary/30 bg-white">
    @if (isset($title))
        <div class="w-full p-4 py-6 text-xl">{{ $title }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="border-b-1 w-fit min-w-full divide-y divide-primary">
            <thead class="bg-primary-dark text-white">
                <tr>
                    @foreach ($tableData->cols as $col)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            {{ $col->label }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/30">
                @forelse ($getData() as $data)
                    <tr>
                        @foreach ($tableData->cols as $col)
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @php
                                    if (Str::contains($col->key, '.')) {
                                        $keys = explode('.', $col->key);

                                        $value = $data;

                                        foreach ($keys as $key) {
                                            $value = $value[$key];
                                            if (is_null($value)) {
                                                break;
                                            }
                                        }
                                    } elseif ($col->key === '') {
                                        $value = $data;
                                    } else {
                                        $value = $data[$col->key];
                                    }
                                @endphp

                                <x-dynamic-component :component="$col->view" :value="$value" />
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="px-6 py-4 text-center text-gray-500">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex justify-between border-t-2 border-t-primary/30 p-4">
        <div>
            <form method="GET" action="{{ request()->url() }}" class="inline-flex items-center space-x-2">
                <select
                    name="perPage"
                    id="perPage"
                    class="w-20 rounded-md border border-gray-300 p-1 px-4"
                    onchange="this.form.submit()"
                >
                    @foreach ([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" {{ request('perPage', 10) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <label for="perPage" class="text-sm">Per page</label>
            </form>
        </div>
        <div>
            {{
                $getData()->links(
                    data: [
                        'scrollTo' => false,
                    ],
                )
            }}
        </div>
    </div>
</div>
