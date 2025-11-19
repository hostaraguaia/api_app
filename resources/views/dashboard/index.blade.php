@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('dashboard.index') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('dashboard.questions.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-question-circle"></i> Perguntas
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total de Perguntas</h6>
                                    <h2 class="mb-0">{{ App\Models\Question::count() }}</h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Perguntas Ativas</h6>
                                    <h2 class="mb-0">{{ App\Models\Question::where('is_active', true)->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Quiz Realizados</h6>
                                    <h2 class="mb-0">{{ App\Models\QuizAttempt::count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Ações Rápidas</h5>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="{{ route('dashboard.questions.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nova Pergunta
                        </a>
                        <a href="{{ route('dashboard.questions.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list"></i> Ver Todas as Perguntas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
