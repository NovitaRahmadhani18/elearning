<x-layouts.teacher-layout>
    <x-slot name="header">Quizzes > Edit Quiz</x-slot>

    <div x-data="quizForm()">
        <form
            class="flex flex-col items-start gap-4 lg:flex-row"
            method="POST"
            action="{{ route('teacher.quizes.update', $quiz) }}"
        >
            @csrf
            @method('PUT')

            <template x-for="(question, qIndex) in questions" :key="question.id || question.tempId">
                <div class="hidden">
                    <input type="hidden" :name="`questions[${qIndex}][id]`" :value="question.id" />
                    <input type="hidden" :name="`questions[${qIndex}][text]`" :value="question.text" />
                    <input
                        type="hidden"
                        :name="`questions[${qIndex}][correct_option_index]`"
                        :value="question.correctOptionIndex"
                    />
                    <template x-for="(option, oIndex) in question.options" :key="option.id || option.tempId">
                        <div>
                            <input
                                type="hidden"
                                :name="`questions[${qIndex}][options][${oIndex}][id]`"
                                :value="option.id"
                            />
                            <input
                                type="hidden"
                                :name="`questions[${qIndex}][options][${oIndex}][text]`"
                                :value="option.text"
                            />
                        </div>
                    </template>
                </div>
            </template>

            {{-- Kolom utama form --}}
            <div class="w-full rounded-lg border border-primary/20 bg-white p-6">
                {{-- Input Judul & Deskripsi --}}
                <div class="mb-6">
                    <x-input-label for="title" value="Quiz Title" class="text-xl" />
                    <x-text-input
                        id="title"
                        name="title"
                        class="mt-1 w-full"
                        required
                        :value="old('title', $quiz->title)"
                    />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div class="mb-6">
                    <x-input-label for="description" value="Description" />
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="w-full rounded-md border-gray-300 shadow-sm"
                    >
{{ old('description', $quiz->description) }}</textarea
                    >
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <x-divider />
                <h1 class="mb-6 text-xl">Questions</h1>

                {{-- Bagian Pertanyaan Dinamis --}}
                <div class="space-y-4">
                    {{-- PERBAIKAN: Gunakan tempId sebagai fallback key --}}
                    <template x-for="(question, qIndex) in questions" :key="question.id || question.tempId">
                        <div class="space-y-3 rounded-md bg-primary/10 p-4">
                            <div class="flex items-start gap-2">
                                <textarea
                                    x-model="question.text"
                                    rows="1"
                                    placeholder="Enter question text"
                                    class="w-full rounded-md border-gray-300"
                                ></textarea>
                                <button type="button" @click="removeQuestion(qIndex)" x-show="questions.length > 1">
                                    <x-gmdi-delete class="h-6 w-6 text-red-500" />
                                </button>
                            </div>
                            <template x-if="errors['questions.' + qIndex + '.text']">
                                <p
                                    class="mt-1 text-sm text-red-600"
                                    x-text="errors['questions.' + qIndex + '.text'][0]"
                                ></p>
                            </template>
                            <div class="ml-6 space-y-2">
                                {{-- PERBAIKAN: Gunakan tempId sebagai fallback key --}}
                                <template
                                    x-for="(option, oIndex) in question.options"
                                    :key="option.id || option.tempId"
                                >
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="radio"
                                            :name="`q_${qIndex}_correct`"
                                            :checked="question.correctOptionIndex == oIndex"
                                            @change="question.correctOptionIndex = oIndex"
                                        />
                                        <x-text-input type="text" x-model="option.text" required class="w-full" />
                                        <button
                                            type="button"
                                            @click="removeOption(qIndex, oIndex)"
                                            x-show="question.options.length > 2"
                                        >
                                            <x-gmdi-delete class="h-5 w-5 text-red-400" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <button
                                    type="button"
                                    @click="addOption(qIndex)"
                                    class="ml-6 text-sm text-primary hover:underline"
                                >
                                    Add Option
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button
                    @click="addQuestion"
                    type="button"
                    class="mt-6 w-full rounded-md border border-dashed border-primary py-4"
                >
                    Add Question
                </button>
            </div>

            {{-- Kolom Pengaturan Kanan --}}
            <div class="w-full lg:w-[400px] lg:flex-shrink-0">
                <div class="sticky top-4 rounded-lg border border-primary/20 bg-white p-6">
                    <div class="mb-6">
                        <x-input-label for="" value="Quiz Settings" class="text-xl" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="classroom_id" value="Select Course" />
                        <select
                            name="classroom_id"
                            id="classroom_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                            required
                        >
                            <option value="" disabled selected>Select a course</option>
                            @foreach ($classrooms as $id => $classroom)
                                <option value="{{ $id }}" @selected(old('classroom_id', $quiz->classroom_id) == $id)>
                                    {{ $classroom }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('classroom_id')" class="mt-2" />
                    </div>

                    <div class="mb-6" x-data="{ custom: true }">
                        <x-input-label value="Time Limit" />
                        <div class="mt-1 flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="custom_time_limit"
                                name="custom_time_limit"
                                value="1"
                                x-model="custom"
                                class="rounded border-gray-300"
                            />
                            <label for="custom_time_limit" class="text-sm font-normal text-gray-600">Custom time</label>
                        </div>

                        <template x-if="custom">
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Set a custom time limit in minutes.</p>
                                <x-text-input
                                    id="time_limit_custom"
                                    name="time_limit"
                                    type="number"
                                    placeholder="Time limit in minutes"
                                    :value="old('time_limit', $quiz->time_limit)"
                                    class="mt-1 w-full"
                                />
                            </div>
                        </template>

                        <template x-if="!custom">
                            <select
                                id="time_limit"
                                name="time_limit"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                            >
                                <option value="300">5 minutes</option>
                                <option value="600">10 minutes</option>
                                <option value="1800">30 minutes</option>
                                <option value="3600">1 hour</option>
                            </select>
                        </template>
                        <x-input-error :messages="$errors->get('time_limit')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="start_time" value="Start Time" />
                        <x-text-input
                            id="start_time"
                            name="start_time"
                            type="datetime-local"
                            :value="old('start_time', $quiz->start_time)"
                            class="mt-1 w-full"
                            required
                        />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="due_time" value="Due Time" />
                        <x-text-input
                            id="due_time"
                            name="due_time"
                            type="datetime-local"
                            :value="old('due_time', $quiz->due_time)"
                            class="mt-1 w-full"
                            required
                        />
                        <x-input-error :messages="$errors->get('due_time')" class="mt-2" />
                    </div>

                    <x-divider />
                    <div class="mt-6 flex flex-col gap-2">
                        <x-primary-button type="submit" class="justify-center">Update Quiz</x-primary-button>
                        <x-secondary-button type="button" class="justify-center">Cancel</x-secondary-button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function quizForm() {
                const initialQuestions = @json(old('questions', $questions));
                const allErrors = @json($errors->toArray());

                return {
                    errors: allErrors || {},
                    questions:
                        initialQuestions && initialQuestions.length > 0
                            ? initialQuestions
                            : [
                                  {
                                      id: null,
                                      tempId: 'new_' + Date.now(),
                                      text: '',
                                      options: [
                                          { id: null, tempId: 'new_opt_' + Date.now(), text: '' },
                                          { id: null, tempId: 'new_opt_' + (Date.now() + 1), text: '' },
                                      ],
                                      correctOptionIndex: 0,
                                  },
                              ],
                    addQuestion() {
                        // PERBAIKAN: Tambahkan tempId unik saat membuat pertanyaan baru
                        this.questions.push({
                            id: null,
                            tempId: 'new_' + Date.now(),
                            text: '',
                            options: [
                                { id: null, tempId: 'new_opt_' + Date.now(), text: '' },
                                { id: null, tempId: 'new_opt_' + (Date.now() + 1), text: '' },
                            ],
                            correctOptionIndex: 0,
                        });
                    },
                    removeQuestion(qIndex) {
                        if (this.questions.length > 1) {
                            this.questions.splice(qIndex, 1);
                        }
                    },
                    addOption(qIndex) {
                        // PERBAIKAN: Tambahkan tempId unik saat membuat opsi baru
                        this.questions[qIndex].options.push({ id: null, tempId: 'new_opt_' + Date.now(), text: '' });
                    },
                    removeOption(qIndex, oIndex) {
                        if (this.questions[qIndex].options.length > 2) {
                            this.questions[qIndex].options.splice(oIndex, 1);
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.teacher-layout>
