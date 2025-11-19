@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-person-circle" style="font-size: 4rem; color: #0d6efd;"></i>
                        <h5 class="mt-2">{{ $client->name }}</h5>
                        <p class="text-muted small mb-0">{{ $client->email }}</p>
                    </div>
                    <hr>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('client.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('quiz.start') }}">
                            <i class="bi bi-play-circle"></i> Fazer Quiz
                        </a>
                        <a class="nav-link text-danger" href="{{ route('client.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </nav>
                    <form id="logout-form" action="{{ route('client.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Stats Cards -->
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-12 col-sm-4">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total de Quizzes</h6>
                                    <h2 class="mb-0 mt-2">{{ $stats['total_attempts'] }}</h2>
                                </div>
                                <i class="bi bi-clipboard-check" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Média de Acertos</h6>
                                    <h2 class="mb-0 mt-2">{{ number_format($stats['average_percentage'], 1) }}%</h2>
                                </div>
                                <i class="bi bi-graph-up-arrow" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card bg-warning text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Melhor Resultado</h6>
                                    <h2 class="mb-0 mt-2">{{ $stats['best_score'] ?? 0 }}/{{ $stats['best_total'] ?? 0 }}</h2>
                                </div>
                                <i class="bi bi-trophy-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz History -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Histórico de Quizzes</h5>
                </div>
                <div class="card-body">
                    @if($attempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Pontuação</th>
                                        <th>Acertos</th>
                                        <th>Percentual</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->completed_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar 
                                                        @if($attempt->score / $attempt->total_questions >= 0.7) bg-success
                                                        @elseif($attempt->score / $attempt->total_questions >= 0.5) bg-warning
                                                        @else bg-danger
                                                        @endif" 
                                                        role="progressbar" 
                                                        style="width: {{ ($attempt->score / $attempt->total_questions) * 100 }}%">
                                                        {{ $attempt->score }} corretas
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="
                                                    @if($attempt->score / $attempt->total_questions >= 0.7) text-success
                                                    @elseif($attempt->score / $attempt->total_questions >= 0.5) text-warning
                                                    @else text-danger
                                                    @endif">
                                                    {{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%
                                                </strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('client.quiz.details', $attempt->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $attempts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Você ainda não fez nenhum quiz.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('quiz.start') }}" class="btn btn-primary">
                                    <i class="bi bi-play-circle"></i> Iniciar Novo Quiz
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
