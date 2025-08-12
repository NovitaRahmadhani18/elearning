@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    <x-slot name="header">Quizzes > Create Quiz</x-slot>

    <div class="flex flex-col items-start gap-4 lg:flex-row">
        <!-- Main Content -->
        <div class="w-full rounded-lg border border-primary/20 bg-white p-6">
            <div class="mb-6">
                <x-input-label for="title" value="Quiz Title" class="text-xl" />
                <x-text-input
                    id="title"
                    wire:model="title"
                    class="mt-1 w-full"
                    placeholder="Please enter the quiz title"
                />
                @error('title')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <div class="mb-6">
                <x-input-label for="description" value="Description" />
                <textarea
                    id="description"
                    wire:model="description"
                    rows="5"
                    placeholder="Enter quiz description"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                ></textarea>
                @error('description')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <!-- Questions Section -->
            <x-divider />
            <h1 class="mb-6 text-xl">Questions</h1>

            <div class="space-y-4">
                @foreach ($questions as $qIndex => $question)
                    <div
                        class="space-y-3 rounded-md bg-primary/10 p-4"
                        wire:key="question-{{ $question['tempId'] ?? $qIndex }}"
                    >
                        <div class="flex items-start gap-2">
                            <div class="flex-1">
                                <textarea
                                    wire:model="questions.{{ $qIndex }}.text"
                                    rows="1"
                                    placeholder="Enter question text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                ></textarea>

                                <!-- Question Image Upload -->
                                <div class="mt-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">
                                        Question Image (Optional)
                                    </label>
                                    <input
                                        type="file"
                                        wire:model="questionImages.{{ $qIndex }}"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark"
                                    />
                                    @if (isset($question['image_path']) && $question['image_path'])
                                        <div class="relative mt-2">
                                            <img
                                                src="{{ Storage::url($question['image_path']) }}"
                                                alt="Question image"
                                                class="max-w-xs rounded border"
                                            />
                                            <button
                                                type="button"
                                                wire:click="removeQuestionImage({{ $qIndex }})"
                                                class="absolute right-1 top-1 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                            >
                                                <x-gmdi-close class="h-3 w-3" />
                                            </button>
                                        </div>
                                    @endif

                                    @error("questionImages.{$qIndex}")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
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
                            @foreach ($question['options'] as $oIndex => $option)
                                <div
                                    class="flex items-start gap-2"
                                    wire:key="option-{{ $option['tempId'] ?? $oIndex }}"
                                >
                                    <input
                                        type="radio"
                                        name="q_{{ $qIndex }}_correct"
                                        value="{{ $oIndex }}"
                                        wire:click="setCorrectOption({{ $qIndex }}, {{ $oIndex }})"
                                        @checked(($question['correctOptionIndex'] ?? 0) == $oIndex)
                                        class="mt-1 text-primary focus:ring-primary"
                                    />
                                    <div class="flex-1">
                                        <x-text-input
                                            type="text"
                                            wire:model="questions.{{ $qIndex }}.options.{{ $oIndex }}.text"
                                            placeholder="Enter option text"
                                            class="w-full"
                                        />

                                        <!-- Option Image Upload -->
                                        <div class="mt-2">
                                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                                Option Image (Optional)
                                            </label>
                                            <input
                                                type="file"
                                                wire:model="optionImages.{{ $qIndex }}.{{ $oIndex }}"
                                                accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded file:border-0 file:bg-gray-100 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                                            />
                                            @if (isset($option['image_path']) && $option['image_path'])
                                                <div class="relative mt-2">
                                                    <img
                                                        src="{{ Storage::url($option['image_path']) }}"
                                                        alt="Option image"
                                                        class="max-w-xs rounded border"
                                                    />
                                                    <button
                                                        type="button"
                                                        wire:click="removeOptionImage({{ $qIndex }}, {{ $oIndex }})"
                                                        class="absolute right-1 top-1 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                                    >
                                                        <x-gmdi-close class="h-3 w-3" />
                                                    </button>
                                                </div>
                                            @endif

                                            @error("optionImages.{$qIndex}.{$oIndex}")
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    @if (count($question['options']) > 2)
                                        <button
                                            type="button"
                                            wire:click="removeOption({{ $qIndex }}, {{ $oIndex }})"
                                            class="mt-1 text-red-400 hover:text-red-600"
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
                                class="ml-6 text-sm text-primary-dark hover:underline"
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
        </div>

        <!-- Sidebar Settings -->
        <div class="w-full rounded-lg border border-primary/20 bg-white p-6 lg:w-[400px] lg:flex-shrink-0">
            <div class="mb-6">
                <x-input-label value="Quiz Settings" class="text-xl" />
            </div>

            <div class="mb-6">
                <x-input-label for="classroom_id" value="Select Course" />
                <select
                    wire:model="classroom_id"
                    id="classroom_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                >
                    <option value="" disabled>Select a course</option>
                    @foreach ($classrooms as $classroom)
                        <option value="{{ $classroom['id'] }}">{{ $classroom['title'] }}</option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <div class="mb-6">
                <x-input-label value="Time Limit" />
                <div class="mt-1 flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="custom_time_limit"
                        wire:model.live="custom_time_limit"
                        class="rounded border-gray-300"
                    />
                    <label for="custom_time_limit" class="text-sm font-normal text-gray-600">Custom time</label>
                </div>

                @if ($custom_time_limit)
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Set a custom time limit in minutes.</p>
                        <x-text-input
                            id="time_limit_custom"
                            wire:model="time_limit"
                            type="number"
                            placeholder="Time limit in minutes"
                            class="mt-1 w-full"
                        />
                    </div>
                @else
                    <select
                        id="time_limit"
                        wire:model="time_limit"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                    >
                        <option value="5">5 minutes</option>
                        <option value="10">10 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                    </select>
                @endif
                @error('time_limit')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <div class="mb-6">
                <x-input-label for="start_time" value="Start Time" />
                <x-text-input id="start_time" wire:model="start_time" type="datetime-local" class="mt-1 w-full" />
                @error('start_time')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <div class="mb-6">
                <x-input-label for="due_time" value="Due Time" />
                <x-text-input id="due_time" wire:model="due_time" type="datetime-local" class="mt-1 w-full" />
                @error('due_time')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <div class="mb-6">
                <x-input-label for="point" value="Total Point" />
                <x-text-input id="point" type="number" wire:model="point" class="mt-1 w-full" />
                @error('point')
                    <x-input-error :messages="$message" class="mt-2" />
                @enderror
            </div>

            <x-divider />

            <div class="mt-6 flex flex-col gap-2">
                <x-primary-button
                    wire:click="save"
                    class="justify-center"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove>Save Quiz</span>
                    <span wire:loading>Saving...</span>
                </x-primary-button>
                <x-secondary-button wire:click="cancel" class="justify-center">Cancel</x-secondary-button>
            </div>
        </div>
    </div>
</div>
