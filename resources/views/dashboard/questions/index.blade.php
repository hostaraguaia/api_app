@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Perguntas</h4>
                    <a href="{{ route('dashboard.questions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nova Pergunta
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pergunta</th>
                                    <th>Dificuldade</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($questions as $question)
                                    <tr>
                                        <td>{{ $question->id }}</td>
                                        <td>{{ Str::limit($question->question, 50) }}</td>
                                        <td>
                                            @if ($question->difficulty === 'easy')
                                                <span class="badge bg-success">Fácil</span>
                                            @elseif ($question->difficulty === 'medium')
                                                <span class="badge bg-warning">Média</span>
                                            @else
                                                <span class="badge bg-danger">Difícil</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $question->is_active ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('dashboard.questions.edit', $question) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dashboard.questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta pergunta?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Nenhuma pergunta cadastrada</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $questions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
