<div x-data="{ 
    questions: @entangle('questions'),
    syncToForm() {
        const container = document.getElementById('questions-hidden-inputs');
        if (!container) return;
        
        console.log('Syncing questions to form:', this.questions);
        
        let inputsHtml = '';
        this.questions.forEach((question, qIndex) => {
            if (question && typeof question === 'object') {
                inputsHtml += `<input type="hidden" name="questions[${qIndex}][id]" value="${question.id || ''}" />`;
                inputsHtml += `<input type="hidden" name="questions[${qIndex}][text]" value="${(question.text || '').replace(/\"/g, '&quot;').replace(/'/g, '&#39;')}" />`;
                inputsHtml += `<input type="hidden" name="questions[${qIndex}][correct_option_index]" value="${question.correctOptionIndex || 0}" />`;
                
                if (question.options && Array.isArray(question.options)) {
                    question.options.forEach((option, oIndex) => {
                        if (option && typeof option === 'object') {
                            inputsHtml += `<input type="hidden" name="questions[${qIndex}][options][${oIndex}][id]" value="${option.id || ''}" />`;
                            inputsHtml += `<input type="hidden" name="questions[${qIndex}][options][${oIndex}][text]" value="${(option.text || '').replace(/\"/g, '&quot;').replace(/'/g, '&#39;')}" />`;
                        }
                    });
                }
            }
        });
        
        container.innerHTML = inputsHtml;
        console.log('Synced', container.querySelectorAll('input').length, 'hidden inputs');
    }
}" 
x-init="
    $watch('questions', () => { 
        console.log('Questions changed, syncing...'); 
        syncToForm(); 
    }, { deep: true });
    
    // Initial sync
    $nextTick(() => syncToForm());
    
    // Sync before form submit
    document.querySelector('form')?.addEventListener('submit', (e) => {
        console.log('Form submitting, final sync...');
        syncToForm();
    });
">
    <x-divider />
    <h1 class="mb-6 text-xl">Questions</h1>

    <div class="space-y-4">
        @foreach ($questions as $qIndex => $question)
            <div class="space-y-3 rounded-md bg-primary/10 p-4" wire:key="question-{{ $question['tempId'] ?? $question['id'] ?? $qIndex }}">
                <div class="flex items-start gap-2">
                    <textarea
                        wire:model.live="questions.{{ $qIndex }}.text"
                        rows="1"
                        placeholder="Enter question text"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                    ></textarea>
                    @if (count($questions) > 1)
                        <button 
                            type="button" 
                            wire:click="removeQuestion({{ $qIndex }})"
                            class="text-red-500 hover:text-red-700"
                        >
                            <x-gmdi-delete class="h-6 w-6" />
                        </button>
                    @endif
                </div>

                @error("questions.{$qIndex}.text")
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="ml-6 space-y-2">
                    @foreach (($question['options'] ?? []) as $oIndex => $option)
                        <div class="flex items-center gap-2" wire:key="option-{{ $option['tempId'] ?? $option['id'] ?? $oIndex }}">
                            <input
                                type="radio"
                                name="q_{{ $qIndex }}_correct"
                                value="{{ $oIndex }}"
                                wire:click="setCorrectOption({{ $qIndex }}, {{ $oIndex }})"
                                @checked(($question['correctOptionIndex'] ?? 0) == $oIndex)
                                class="text-primary focus:ring-primary"
                            />
                            <x-text-input
                                type="text"
                                wire:model.live="questions.{{ $qIndex }}.options.{{ $oIndex }}.text"
                                placeholder="Enter option text"
                                class="w-full"
                                required
                            />
                            @if (count($question['options'] ?? []) > 2)
                                <button
                                    type="button"
                                    wire:click="removeOption({{ $qIndex }}, {{ $oIndex }})"
                                    class="text-red-400 hover:text-red-600"
                                >
                                    <x-gmdi-delete class="h-5 w-5" />
                                </button>
                            @endif
                        </div>
                        @error("questions.{$qIndex}.options.{$oIndex}.text")
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endforeach
                </div>

<div>
                    <button
                        type="button"
                        wire:click="addOption({{ $qIndex }})"
                        class="ml-6 text-sm text-primary hover:underline"
                    >
                        Add Option
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <button
        type="button"
        wire:click="addQuestion"
        class="mt-6 w-full rounded-md border border-dashed border-primary py-4 transition hover:bg-primary/20"
    >
        Add Question
    </button>

    {{-- Dynamic hidden inputs container --}}
    <div id="questions-hidden-inputs"></div>
</div>
