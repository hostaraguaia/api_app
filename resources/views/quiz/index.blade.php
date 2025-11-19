@extends('layouts.app')

@section('content')
<style>
    .quiz-question {
        animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .answer-option {
        transition: all 0.3s ease;
        cursor: pointer;
        padding: 15px;
        margin: 10px 0;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        background: white;
    }
    
    .answer-option:hover:not(.correct):not(.incorrect):not(.disabled) {
        background: #f8f9fa;
        border-color: #0d6efd;
        transform: translateX(5px);
    }
    
    .answer-option.correct {
        background: #d1e7dd !important;
        border-color: #198754 !important;
        animation: bounce 0.6s ease;
    }
    
    .answer-option.incorrect {
        background: #f8d7da !important;
        border-color: #dc3545 !important;
        animation: shake 0.5s ease;
    }
    
    .answer-option.disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .progress-bar-custom {
        height: 8px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0d6efd, #0dcaf0);
        transition: width 0.5s ease;
        border-radius: 10px;
    }
    
    .score-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        margin: 0 10px;
    }
    
    .score-correct {
        background: #d1e7dd;
        color: #198754;
    }
    
    .score-incorrect {
        background: #f8d7da;
        color: #dc3545;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Quiz Interativo</h4>
                        <div id="score-display" class="d-none">
                            <span class="score-badge score-correct" id="correct-score">✓ 0</span>
                            <span class="score-badge score-incorrect" id="incorrect-score">✗ 0</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Loading -->
                    <div class="text-center py-5" id="loading">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-3 text-muted">Carregando perguntas...</p>
                    </div>

                    <!-- Quiz Container -->
                    <div id="quiz-container" style="display: none;">
                        <!-- Progress Bar -->
                        <div class="progress-bar-custom">
                            <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
                        </div>
                        
                        <div class="mb-3 text-muted">
                            <small>Pergunta <span id="current-question">1</span> de <span id="total-questions">0</span></small>
                        </div>
                        
                        <!-- Question Display -->
                        <div id="question-display"></div>
                        
                        <div class="text-center mt-4">
                            <button id="next-btn" class="btn btn-primary btn-lg px-5" style="display: none;">
                                Próxima Pergunta →
                            </button>
                        </div>
                    </div>

                    <!-- Results -->
                    <div id="results" style="display: none;" class="text-center py-4">
                        <div class="mb-4">
                            <i class="bi bi-trophy-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="mb-4">Quiz Finalizado!</h2>
                        <div class="display-3 mb-4 fw-bold" id="final-percentage" style="color: #0d6efd;">0%</div>
                        <p class="lead mb-4">
                            Você acertou <span class="text-success fw-bold" id="final-correct">0</span> de 
                            <span class="fw-bold" id="final-total">0</span> questões
                        </p>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <h3 class="text-success mb-0" id="final-correct-count">0</h3>
                                    <small class="text-muted">Corretas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <h3 class="text-danger mb-0" id="final-incorrect-count">0</h3>
                                    <small class="text-muted">Incorretas</small>
                                </div>
                            </div>
                        </div>
                        
                        @guest('client')
                            <div class="alert alert-info">
                                <h5><i class="bi bi-bookmark-star"></i> Salve seu resultado!</h5>
                                <p class="mb-3">Cadastre-se para salvar seu histórico e acompanhar sua evolução.</p>
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                    <a href="{{ route('client.register') }}?quiz_attempt_id=" id="register-link" class="btn btn-primary">
                                        <i class="bi bi-person-plus"></i> Criar Conta
                                    </a>
                                    <a href="{{ route('client.login') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-box-arrow-in-right"></i> Já tenho conta
                                    </a>
                                </div>
                            </div>
                        @endguest
                        
                        <div class="mt-4">
                            <button onclick="location.reload()" class="btn btn-lg btn-success">
                                <i class="bi bi-arrow-clockwise"></i> Tentar Novamente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let questions = [];
let currentQuestionIndex = 0;
let userAnswers = [];
let correctCount = 0;
let incorrectCount = 0;

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    fetchQuestions();
});

async function fetchQuestions() {
    try {
        const response = await fetch('/api/quiz/questions');
        
        if (response.status === 403) {
            const data = await response.json();
            document.getElementById('loading').style.display = 'none';
            document.getElementById('quiz-container').style.display = 'none';
            
            const errorHtml = `
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="mb-3">Atenção!</h3>
                    <p class="lead text-muted mb-4">${data.error}</p>
                    <a href="{{ route('client.dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-speedometer2"></i> Ir para o Dashboard
                    </a>
                </div>
            `;
            
            // Replace the entire card body content or just the quiz container area
            // To be safe, let's replace the loading div's parent content if possible, 
            // or just hide everything and append this message.
            // Since we are inside card-body, let's replace card-body content.
            document.querySelector('.card-body').innerHTML = errorHtml;
            return;
        }

        questions = await response.json();
        
        if (questions.length === 0) {
            alert('Nenhuma pergunta disponível no momento.');
            return;
        }
        
        document.getElementById('loading').style.display = 'none';
        document.getElementById('quiz-container').style.display = 'block';
        document.getElementById('score-display').classList.remove('d-none');
        document.getElementById('total-questions').textContent = questions.length;
        
        showQuestion(0);
    } catch (error) {
        console.error('Error:', error);
        alert('Erro ao carregar perguntas. Por favor, recarregue a página.');
    }
}

function showQuestion(index) {
    if (index >= questions.length) {
        submitQuiz();
        return;
    }
    
    currentQuestionIndex = index;
    const question = questions[index];
    
    // Update progress
    const progress = ((index + 1) / questions.length) * 100;
    document.getElementById('progress-fill').style.width = progress + '%';
    document.getElementById('current-question').textContent = index + 1;
    
    // Render question
    const questionHtml = `
        <div class="quiz-question">
            <h4 class="mb-4">${question.question}</h4>
            <div id="answers-container">
                ${question.answers.map(answer => `
                    <div class="answer-option" data-answer-id="${answer.id}" onclick="selectAnswer(${answer.id}, ${question.id})">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">${answer.answer}</div>
                            <div class="answer-icon" style="display: none;">
                                <i class="bi" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    document.getElementById('question-display').innerHTML = questionHtml;
    document.getElementById('next-btn').style.display = 'none';
}

async function selectAnswer(answerId, questionId) {
    // Prevent multiple clicks
    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach(opt => opt.classList.add('disabled'));
    
    // Check if answer is correct
    const response = await fetch('/api/quiz/questions');
    const allQuestions = await response.json();
    const currentQuestion = allQuestions.find(q => q.id === questionId);
    
    // Since we removed is_correct from frontend, we need to check via a different approach
    // For now, we'll store the answer and validate on submit
    // But for immediate feedback, we need the correct answer info
    
    // Let's make a temporary call to check (or modify API to return correct answer after selection)
    const selectedOption = document.querySelector(`[data-answer-id="${answerId}"]`);
    const icon = selectedOption.querySelector('.answer-icon i');
    const iconContainer = selectedOption.querySelector('.answer-icon');
    
    // Store user answer
    userAnswers.push({
        question_id: questionId,
        answer_id: answerId
    });
    
    // For demo purposes, we'll validate on backend and show next button
    // In a real scenario, you'd want immediate feedback
    // Let's modify this to get instant feedback
    
    try {
        // Make a single-answer validation call
        const validateResponse = await fetch('/api/quiz/validate-answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                question_id: questionId,
                answer_id: answerId
            })
        });
        
        const result = await validateResponse.json();
        console.log('Validate Answer Response:', result);
        
        if (result.correct) {
            selectedOption.classList.add('correct');
            icon.classList.add('bi-check-circle-fill', 'text-success');
            correctCount++;
        } else {
            selectedOption.classList.add('incorrect');
            icon.classList.add('bi-x-circle-fill', 'text-danger');
            incorrectCount++;
            
            // Show correct answer
            const correctAnswerId = result.correct_answer_id;
            console.log('Correct Answer ID:', correctAnswerId);
            const correctOption = document.querySelector(`[data-answer-id="${correctAnswerId}"]`);
            console.log('Correct Option Element:', correctOption);
            if (correctOption) {
                correctOption.classList.add('correct');
                const correctIcon = correctOption.querySelector('.answer-icon i');
                correctIcon.classList.add('bi-check-circle-fill', 'text-success');
                correctOption.querySelector('.answer-icon').style.display = 'block';
            }
        }
        
        iconContainer.style.display = 'block';
        
        // Update score display
        document.getElementById('correct-score').textContent = `✓ ${correctCount}`;
        document.getElementById('incorrect-score').textContent = `✗ ${incorrectCount}`;
        
        // Show next button after delay
        setTimeout(() => {
            document.getElementById('next-btn').style.display = 'inline-block';
        }, 1000);
        
    } catch (error) {
        console.error('Error validating answer:', error);
        // Fallback: just show next button
        document.getElementById('next-btn').style.display = 'inline-block';
    }
}

// Next button handler
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'next-btn') {
        showQuestion(currentQuestionIndex + 1);
    }
});

async function submitQuiz() {
    try {
        const response = await fetch('/api/quiz/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ answers: userAnswers })
        });

        const result = await response.json();
        
        if (response.ok) {
            showResults(result);
        } else {
            console.error('Error submitting quiz:', result);
            showResults(result);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erro ao enviar respostas.');
    }
}

function showResults(result) {
    document.getElementById('quiz-container').style.display = 'none';
    document.getElementById('results').style.display = 'block';
    document.getElementById('score-display').classList.add('d-none');
    
    document.getElementById('final-percentage').textContent = result.percentage + '%';
    document.getElementById('final-correct').textContent = result.score;
    document.getElementById('final-total').textContent = result.total_questions;
    document.getElementById('final-correct-count').textContent = result.score;
    document.getElementById('final-incorrect-count').textContent = result.total_questions - result.score;
    
    const registerLink = document.getElementById('register-link');
    if (registerLink) {
        registerLink.href = `{{ route('client.register') }}?quiz_attempt_id=${result.quiz_attempt_id}`;
    }
}
</script>
@endpush
@endsection
