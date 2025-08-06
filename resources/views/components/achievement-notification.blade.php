@if(session()->has('achievement_unlocked'))
    @php $achievement = session('achievement_unlocked'); @endphp
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-4 right-4 z-50 max-w-sm rounded-lg bg-gradient-to-r from-yellow-400 to-orange-500 p-4 text-white shadow-lg">
        
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <x-gmdi-emoji-events class="h-8 w-8 text-white" />
            </div>
            <div class="ml-3 flex-1">
                <h4 class="text-sm font-bold">Lencana Baru Diraih! 🎉</h4>
                <p class="text-sm">{{ $achievement['name'] }}</p>
                <p class="text-xs opacity-90">+{{ $achievement['points'] }} XP</p>
            </div>
            <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                <x-gmdi-close class="h-5 w-5" />
            </button>
        </div>
    </div>
@endif