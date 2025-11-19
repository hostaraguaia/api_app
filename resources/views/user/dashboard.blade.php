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
                        <a class="nav-link active text-dark" href="{{ route('user.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link text-dark" href="{{ route('user.questions.index') }}">
                            <i class="bi bi-question-circle"></i> Gerenciar Perguntas
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
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-white shadow-sm">
                        <div class="card-body p-5 text-center">
                            <h2 class="mb-4">Bem-vindo ao Painel Administrativo</h2>
                            <p class="text-muted mb-4">Aqui você pode gerenciar todo o conteúdo do Quiz.</p>
                            
                            <div class="row g-3 g-md-4">
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('user.questions.index') }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                        <div class="card-body text-center py-4">
                                            <i class="bi bi-collection text-primary mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="text-dark">Gerenciar Perguntas</h5>
                                            <p class="text-muted small mb-0">Criar, editar e remover perguntas</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('user.clients.index') }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                        <div class="card-body text-center py-4">
                                            <i class="bi bi-people text-success mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="text-dark">Clientes</h5>
                                            <p class="text-muted small mb-0">Visualizar usuários cadastrados</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush
@endsection
