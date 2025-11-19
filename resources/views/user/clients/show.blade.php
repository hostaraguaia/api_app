@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dados do Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle" style="font-size: 4rem; color: #6c757d;"></i>
                        <h4 class="mt-2">{{ $client->name }}</h4>
                        <p class="text-muted">{{ $client->email }}</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-telephone me-2"></i> Telefone</span>
                            <span>{{ $client->phone ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-card-text me-2"></i> CPF</span>
                            <span>{{ $client->cpf ?? '-' }}</span>
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-geo-alt me-2"></i> Endereço<br>
                            <span class="text-muted ms-4 d-block">
                                @if($client->zip_code)
                                    {{ $client->zip_code }}<br>
                                    {{ $client->city }} - {{ $client->state }}<br>
                                    {{ $client->parish ?? 'Paróquia não informada' }}
                                @else
                                    Endereço não cadastrado
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar me-2"></i> Cadastro</span>
                            <span>{{ $client->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Histórico de Quizzes</h5>
                    <a href="{{ route('user.clients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    @if($client->quizAttempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Pontuação</th>
                                        <th>Desempenho</th>
                                        <th>Detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client->quizAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i') : 'Em andamento' }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $percentage = $attempt->total_questions > 0 ? ($attempt->score / $attempt->total_questions) * 100 : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar 
                                                        @if($percentage >= 70) bg-success
                                                        @elseif($percentage >= 50) bg-warning
                                                        @else bg-danger
                                                        @endif" 
                                                        role="progressbar" 
                                                        style="width: {{ $percentage }}%">
                                                        {{ number_format($percentage, 0) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('user.quiz.details', $attempt->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">Este cliente ainda não realizou nenhum quiz.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
