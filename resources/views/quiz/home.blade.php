@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-book"></i> Quiz Católico</h4>
                </div>
                <div class="card-body">
                    <div id="quiz-container">
                        <!-- Quiz Start Screen -->
                        <div id="quiz-start" class="text-center">
                            <h2 class="mb-4">Teste seus conhecimentos sobre a Fé Católica</h2>
                            <p class="lead mb-4">Responda 10 perguntas sobre a doutrina, história e tradições da Igreja Católica.</p>
                            <button class="btn btn-primary btn-lg" onclick="startQuiz()">
                                <i class="bi bi-play-fill"></i> Iniciar Quiz
                            </button>
                        </div>

                        <!-- Quiz Questions -->
                        <div id="quiz-questions" class="d-none">
                            <div class="mb-3">
                                <div class="progress">
                                    <div id="quiz-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">Pergunta <span id="current-question">1</span> de <span id="total-questions">10</span></small>
                            </div>

                            <div id="question-container">
                                <h5 id="question-text" class="mb-4"></h5>
                                <div id="answers-container" class="list-group"></div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button id="prev-btn" class="btn btn-secondary" onclick="prevQuestion()" disabled>
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                                <button id="next-btn" class="btn btn-primary" onclick="nextQuestion()" disabled>
                                    Próxima <i class="bi bi-arrow-right"></i>
                                </button>
                                <button id="submit-btn" class="btn btn-success d-none" onclick="submitQuiz()">
                                    <i class="bi bi-check-circle"></i> Finalizar Quiz
                                </button>
                            </div>
                        </div>

                        <!-- Quiz Results -->
                        <div id="quiz-results" class="d-none text-center">
                            <h2 class="mb-4">Resultado do Quiz</h2>
                            <div class="alert alert-info mb-4">
                                <h1 class="display-3" id="score-display"></h1>
                                <p class="lead" id="score-message"></p>
                            </div>
                            <div id="results-details" class="text-start mb-4"></div>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Fazer Novo Quiz
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
let quizAttemptId = null;

async function startQuiz() {
    try {
        const response = await fetch('/api/quiz/questions', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error('Erro ao carregar perguntas');
        }
        
        questions = await response.json();
        userAnswers = new Array(questions.length).fill(null);
        
        document.getElementById('quiz-start').classList.add('d-none');
        document.getElementById('quiz-questions').classList.remove('d-none');
        document.getElementById('total-questions').textContent = questions.length;
        
        showQuestion(0);
    } catch (error) {
        console.error('Erro ao carregar perguntas:', error);
        alert('Erro ao carregar o quiz. Por favor, tente novamente.');
    }
}

function showQuestion(index) {
    currentQuestionIndex = index;
    const question = questions[index];
    
    document.getElementById('question-text').textContent = question.question;
    document.getElementById('current-question').textContent = index + 1;
    
    const progress = ((index + 1) / questions.length) * 100;
    document.getElementById('quiz-progress').style.width = progress + '%';
    
    const answersContainer = document.getElementById('answers-container');
    answersContainer.innerHTML = '';
    
    question.answers.forEach(answer => {
        const button = document.createElement('button');
        button.className = 'list-group-item list-group-item-action';
        button.textContent = answer.answer;
        button.onclick = () => selectAnswer(answer.id, button);
        
        if (userAnswers[index] === answer.id) {
            button.classList.add('active');
        }
        
        answersContainer.appendChild(button);
    });
    
    document.getElementById('prev-btn').disabled = index === 0;
    updateNextButton();
}

function selectAnswer(answerId, button) {
    userAnswers[currentQuestionIndex] = answerId;
    
    document.querySelectorAll('#answers-container button').forEach(btn => {
        btn.classList.remove('active');
    });
    button.classList.add('active');
    
    updateNextButton();
}

function updateNextButton() {
    const hasAnswer = userAnswers[currentQuestionIndex] !== null;
    const isLastQuestion = currentQuestionIndex === questions.length - 1;
    
    document.getElementById('next-btn').disabled = !hasAnswer;
    
    if (isLastQuestion && hasAnswer) {
        document.getElementById('next-btn').classList.add('d-none');
        document.getElementById('submit-btn').classList.remove('d-none');
    } else {
        document.getElementById('next-btn').classList.remove('d-none');
        document.getElementById('submit-btn').classList.add('d-none');
    }
}

function nextQuestion() {
    if (currentQuestionIndex < questions.length - 1) {
        showQuestion(currentQuestionIndex + 1);
    }
}

function prevQuestion() {
    if (currentQuestionIndex > 0) {
        showQuestion(currentQuestionIndex - 1);
    }
}

async function submitQuiz() {
    const answers = questions.map((q, index) => ({
        question_id: q.id,
        answer_id: userAnswers[index]
    }));
    
    try {
        const response = await fetch('/api/quiz/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({ answers })
        });
        
        if (!response.ok) {
            throw new Error('Erro ao enviar respostas');
        }
        
        const result = await response.json();
        quizAttemptId = result.quiz_attempt_id;
        
        showResults(result);
    } catch (error) {
        console.error('Erro ao enviar quiz:', error);
        alert('Erro ao processar suas respostas. Por favor, tente novamente.');
    }
}

async function showResults(result) {
    document.getElementById('quiz-questions').classList.add('d-none');
    document.getElementById('quiz-results').classList.remove('d-none');
    
    const percentage = result.percentage;
    const scoreDisplay = document.getElementById('score-display');
    const scoreMessage = document.getElementById('score-message');
    
    scoreDisplay.textContent = `${result.score}/${result.total_questions}`;
    
    if (percentage >= 80) {
        scoreMessage.textContent = 'Excelente! Você domina muito bem a fé católica!';
        scoreMessage.className = 'lead text-success';
    } else if (percentage >= 60) {
        scoreMessage.textContent = 'Muito bom! Continue estudando para aprofundar seus conhecimentos.';
        scoreMessage.className = 'lead text-primary';
    } else {
        scoreMessage.textContent = 'Continue estudando! A fé católica é rica e maravilhosa.';
        scoreMessage.className = 'lead text-warning';
    }
    
    // Carregar respostas detalhadas
    try {
        const response = await fetch(`/api/quiz/results/${quizAttemptId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const details = await response.json();
            showDetailedResults(details);
        }
    } catch (error) {
        console.error('Erro ao carregar resultados detalhados:', error);
    }
}

function showDetailedResults(details) {
    const container = document.getElementById('results-details');
    container.innerHTML = '<h5>Respostas Detalhadas:</h5>';
    
    details.quiz_answers.forEach((qa, index) => {
        const div = document.createElement('div');
        div.className = `card mb-3 ${qa.is_correct ? 'border-success' : 'border-danger'}`;
        
        const correctAnswer = qa.question.answers.find(a => a.is_correct);
        
        div.innerHTML = `
            <div class="card-body">
                <h6>${index + 1}. ${qa.question.question}</h6>
                <p class="mb-1">
                    <strong>Sua resposta:</strong> ${qa.answer.answer}
                    ${qa.is_correct ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'}
                </p>
                ${!qa.is_correct ? `<p class="mb-0 text-success"><strong>Resposta correta:</strong> ${correctAnswer.answer}</p>` : ''}
            </div>
        `;
        
        container.appendChild(div);
    });
}
</script>
@endpush
@endsection
