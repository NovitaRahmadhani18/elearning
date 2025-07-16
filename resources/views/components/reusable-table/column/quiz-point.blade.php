@props([
    'value',
])

<div>
    {{
        $value->user
            ->quizSubmissions()
            ->where('quiz_id', $value->content->contentable->id)
            ->first()?->score ?? 0
    }}
</div>
