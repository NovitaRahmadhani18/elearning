@props([
    'type' => 'horizontal',
])
@if ($type == 'horizontal')
    <div class="mb-6 border-b border-b-primary/20"></div>
@else
    <div class="mb-6 border-y border-y-primary/20"></div>
@endif
