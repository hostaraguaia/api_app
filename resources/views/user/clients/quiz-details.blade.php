@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-4">
                <a href="{{ route('user.clients.show', $attempt->user_id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar ao Cliente
                </a>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-clipboard-data"></i> Detalhes do Quiz - {{ $attempt->user->name }}</h4>
                        <span class="badge bg-light text-dark">
                            {{ $attempt->completed_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-primary">{{ $attempt->score }}</h3>
                                <small class="text-muted">Acertos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-danger">{{ $attempt->total_questions - $attempt->score }}</h3>
                                <small class="text-muted">Erros</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0">{{ $attempt->total_questions }}</h3>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 
                                    @if(($attempt->score / $attempt->total_questions) >= 0.7) text-success
                                    @elseif(($attempt->score / $attempt->total_questions) >= 0.5) text-warning
                                    @else text-danger
                                    @endif">
                                    {{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%
                                </h3>
                                <small class="text-muted">Aproveitamento</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Review -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Revisão das Respostas</h5>
                </div>
                <div class="card-body">
                    @foreach($attempt->quizAnswers as $index => $quizAnswer)
                        <div class="mb-4 p-3 border rounded {{ $quizAnswer->is_correct ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10' }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0">
                                    <span class="badge {{ $quizAnswer->is_correct ? 'bg-success' : 'bg-danger' }} me-2">
                                        {{ $index + 1 }}
                                    </span>
                                    {{ $quizAnswer->question->question }}
                                </h6>
                                @if($quizAnswer->is_correct)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                @else
                                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.5rem;"></i>
                                @endif
                            </div>

                            <div class="ms-4">
                                @foreach($quizAnswer->question->answers as $answer)
                                    <div class="p-2 mb-2 rounded
                                        @if($answer->id == $quizAnswer->answer_id && $quizAnswer->is_correct)
                                            bg-success text-white
                                        @elseif($answer->id == $quizAnswer->answer_id && !$quizAnswer->is_correct)
                                            bg-danger text-white
                                        @elseif($answer->is_correct)
                                            bg-success bg-opacity-25 border border-success
                                        @else
                                            bg-light
                                        @endif">
                                        <div class="d-flex align-items-center">
                                            @if($answer->id == $quizAnswer->answer_id)
                                                <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                                <strong>Resposta do Cliente: </strong>
                                            @elseif($answer->is_correct)
                                                <i class="bi bi-check-circle-fill me-2"></i>
                                                <strong>Resposta correta: </strong>
                                            @else
                                                <i class="bi bi-circle me-2"></i>
                                            @endif
                                            <span class="ms-1">{{ $answer->answer }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
