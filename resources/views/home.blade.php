@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h1 class="display-4 mb-4">Bem-vindo ao Quiz</h1>
                    <p class="lead mb-5">Teste seus conhecimentos agora mesmo! Não é necessário cadastro para começar.</p>
                    
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="{{ route('quiz.start') }}" class="btn btn-primary btn-lg px-4 gap-3">
                            <i class="bi bi-play-circle me-2"></i> Começar Quiz
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
