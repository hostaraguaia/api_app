@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalhes da Pergunta</h4>
                    <a href="{{ route('user.questions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-muted mb-2">Pergunta</h5>
                        <p class="fs-5">{{ $question->question }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted mb-2">Dificuldade</h5>
                            @if($question->difficulty == 'easy')
                                <span class="badge bg-success fs-6">Fácil</span>
                            @elseif($question->difficulty == 'medium')
                                <span class="badge bg-warning text-dark fs-6">Média</span>
                            @else
                                <span class="badge bg-danger fs-6">Difícil</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted mb-2">Status</h5>
                            <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                                {{ $question->is_active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </div>
                    </div>

                    <h5 class="text-muted mb-3">Respostas</h5>
                    <div class="list-group">
                        @foreach($question->answers as $answer)
                            <div class="list-group-item d-flex justify-content-between align-items-center {{ $answer->is_correct ? 'list-group-item-success' : '' }}">
                                <span>{{ $answer->answer }}</span>
                                @if($answer->is_correct)
                                    <span class="badge bg-success rounded-pill">Correta</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('user.questions.edit', $question) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="{{ route('user.questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta pergunta?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
