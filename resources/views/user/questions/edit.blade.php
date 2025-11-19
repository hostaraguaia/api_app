@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Editar Pergunta</h4>
                    <a href="{{ route('user.questions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.questions.update', $question) }}" method="POST" id="questionForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="question" class="form-label">Pergunta</label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required>{{ old('question', $question->question) }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="difficulty" class="form-label">Dificuldade</label>
                                <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                    <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Fácil</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Média</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Difícil</option>
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Ativa</label>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Respostas</h5>
                        <div id="answers-container">
                            @foreach($question->answers as $index => $answer)
                                <div class="card mb-3 answer-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center">
                                                <span class="badge bg-secondary rounded-circle p-2">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="hidden" name="answers[{{ $index }}][id]" value="{{ $answer->id }}">
                                                <input type="text" class="form-control" name="answers[{{ $index }}][answer]" placeholder="Digite a resposta" required value="{{ old('answers.'.$index.'.answer', $answer->answer) }}">
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input correct-answer-radio" type="radio" name="correct_answer" value="{{ $index }}" {{ old('correct_answer', $answer->is_correct ? $index : '') == $index ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Correta</label>
                                                </div>
                                                <!-- Hidden inputs for is_correct -->
                                                <input type="hidden" name="answers[{{ $index }}][is_correct]" class="is-correct-input" value="{{ $answer->is_correct ? '1' : '0' }}">
                                            </div>
                                            <div class="col-md-1">
                                                @if($index > 1)
                                                    <button type="button" class="btn btn-danger btn-sm remove-answer" title="Remover">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-outline-primary mb-4" id="add-answer">
                            <i class="bi bi-plus-circle"></i> Adicionar Resposta
                        </button>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('user.questions.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let answerCount = {{ count($question->answers) }};

document.getElementById('add-answer').addEventListener('click', function() {
    if (answerCount >= 4) {
        alert('Máximo de 4 respostas permitidas');
        return;
    }
    
    const container = document.getElementById('answers-container');
    const newAnswer = document.createElement('div');
    newAnswer.className = 'card mb-3 answer-item';
    newAnswer.innerHTML = `
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label">Resposta ${answerCount + 1} *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="answers[${answerCount}][answer]" 
                        required>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input 
                            class="form-check-input correct-answer-radio" 
                            type="radio" 
                            name="correct_answer" 
                            value="${answerCount}">
                        <label class="form-check-label">
                            Resposta Correta
                        </label>
                    </div>
                    <input type="hidden" name="answers[${answerCount}][is_correct]" value="0" class="is-correct-input">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-answer" title="Remover">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(newAnswer);
    answerCount++;
    updateRemoveButtons();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-answer') || e.target.closest('.remove-answer')) {
        const button = e.target.classList.contains('remove-answer') ? e.target : e.target.closest('.remove-answer');
        if (answerCount <= 2) {
            alert('Mínimo de 2 respostas obrigatórias');
            return;
        }
        button.closest('.answer-item').remove();
        answerCount--;
        updateAnswerNumbers();
        updateRemoveButtons();
    }
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('correct-answer-radio')) {
        // Reset all is_correct to 0
        document.querySelectorAll('.is-correct-input').forEach(input => {
            input.value = '0';
        });
        
        // Set selected answer to 1
        const selectedIndex = e.target.value;
        const hiddenInput = e.target.closest('.col-md-3').querySelector('.is-correct-input');
        hiddenInput.value = '1';
    }
});

function updateAnswerNumbers() {
    const answers = document.querySelectorAll('.answer-item');
    answers.forEach((answer, index) => {
        const label = answer.querySelector('label');
        if (label && label.textContent.includes('Resposta')) {
            label.textContent = `Resposta ${index + 1} *`;
        }
        
        // Update input names to ensure sequential indexing
        const textInput = answer.querySelector('input[type="text"]');
        if (textInput) {
            textInput.name = `answers[${index}][answer]`;
        }

        const radioInput = answer.querySelector('input[type="radio"]');
        if (radioInput) {
            radioInput.value = index;
        }

        const hiddenInput = answer.querySelector('.is-correct-input');
        if (hiddenInput) {
            hiddenInput.name = `answers[${index}][is_correct]`;
        }
        
        const idInput = answer.querySelector('input[name*="[id]"]');
        if (idInput) {
             idInput.name = `answers[${index}][id]`;
        }
    });
}

function updateRemoveButtons() {
    const removeButtons = document.querySelectorAll('.remove-answer');
    removeButtons.forEach((button, index) => {
        button.style.display = answerCount > 2 ? 'block' : 'none';
    });
}

// Initialize correct answer radios
document.querySelectorAll('.correct-answer-radio').forEach(radio => {
    if (radio.checked) {
        const hiddenInput = radio.closest('.col-md-3').querySelector('.is-correct-input');
        hiddenInput.value = '1';
    }
});

updateRemoveButtons();
</script>
@endpush
@endsection
