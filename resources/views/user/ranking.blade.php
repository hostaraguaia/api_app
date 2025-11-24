@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-person-badge" style="font-size: 4rem; color: #212529;"></i>
                        <h5 class="mt-2">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                        <span class="badge bg-dark mt-2">Administrador</span>
                    </div>
                    <hr>
                    <nav class="nav flex-column">
                        <a class="nav-link text-dark" href="{{ route('user.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link text-dark" href="{{ route('user.questions.index') }}">
                            <i class="bi bi-question-circle"></i> Gerenciar Perguntas
                        </a>
                        <a class="nav-link active text-dark" href="{{ route('user.ranking') }}">
                            <i class="bi bi-trophy"></i> Ranking
                        </a>
                        <a class="nav-link text-danger" href="{{ route('user.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </nav>
                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-trophy text-warning"></i> Ranking de Participantes
                        </h4>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($ranking->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> Nenhum participante encontrado ainda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 80px;">Posição</th>
                                        <th>Nome</th>

                                        <th class="text-center">Pontuação Geral</th>

                                        <th class="text-center">Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking as $index => $attempt)
                                        <tr>
                                            <td class="text-center">
                                                @if($index === 0)
                                                    <span class="badge bg-warning text-dark" style="font-size: 1.2rem;">
                                                        <i class="bi bi-trophy-fill"></i> 1º
                                                    </span>
                                                @elseif($index === 1)
                                                    <span class="badge bg-secondary" style="font-size: 1.1rem;">
                                                        <i class="bi bi-trophy-fill"></i> 2º
                                                    </span>
                                                @elseif($index === 2)
                                                    <span class="badge bg-danger" style="font-size: 1.1rem;">
                                                        <i class="bi bi-trophy-fill"></i> 3º
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}º</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $attempt->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $attempt->user->email }}</small>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    {{ $attempt->score }}
                                                </span>
                                            </td>

                                            <td class="text-center text-muted">
                                                <small>{{ $attempt->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 text-muted">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Total de participantes: <strong>{{ $ranking->count() }}</strong>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>
@endpush
@endsection
