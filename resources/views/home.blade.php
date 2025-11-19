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

            @if($ranking->count() > 0)
            <div class="mt-5">
                <h2 class="text-center mb-5 fw-bold text-uppercase" style="letter-spacing: 2px;">
                    <i class="bi bi-trophy-fill text-warning me-2"></i> Ranking Top 3
                </h2>
                
                <div class="row justify-content-center align-items-end text-center g-3 g-md-4">
                    {{-- 1st Place (Mobile: Top / Desktop: Center) --}}
                    @if($ranking->count() >= 1)
                    <div class="col-12 col-md-4 order-1 order-md-2 mb-4 mb-md-0">
                        <div class="card border-0 shadow mb-3 transform-hover">
                            <div class="card-body bg-white rounded-top p-4 p-md-5 position-relative border border-warning border-bottom-0">
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge rounded-pill bg-warning text-dark border border-4 border-white shadow-sm" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">1</span>
                                </div>
                                <i class="bi bi-trophy-fill text-warning display-1 mb-3 d-block"></i>
                                <h4 class="fw-bold text-truncate">{{ $ranking[0]->user->name ?? 'Anônimo' }}</h4>
                                <p class="mb-0 text-muted fw-bold fs-5">{{ $ranking[0]->score }} pts</p>
                            </div>
                            <div class="bg-warning py-3 rounded-bottom"></div>
                        </div>
                    </div>
                    @endif

                    {{-- 2nd Place (Mobile: Left / Desktop: Left) --}}
                    @if($ranking->count() >= 2)
                    <div class="col-6 col-md-3 order-2 order-md-1">
                        <div class="card border-0 shadow-sm transform-hover" style="transform: translateY(0px) md:translateY(20px);">
                            <div class="card-body bg-light rounded-top p-3 p-md-4 position-relative">
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge rounded-pill bg-secondary border border-4 border-white shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">2</span>
                                </div>
                                <i class="bi bi-trophy-fill text-secondary display-4 mb-3 d-block"></i>
                                <h5 class="fw-bold text-truncate fs-6 fs-md-5">{{ $ranking[1]->user->name ?? 'Anônimo' }}</h5>
                                <p class="mb-0 text-muted fw-bold">{{ $ranking[1]->score }} pts</p>
                            </div>
                            <div class="bg-secondary py-2 rounded-bottom"></div>
                        </div>
                    </div>
                    @endif

                    {{-- 3rd Place (Mobile: Right / Desktop: Right) --}}
                    @if($ranking->count() >= 3)
                    <div class="col-6 col-md-3 order-3 order-md-3">
                        <div class="card border-0 shadow-sm transform-hover" style="transform: translateY(0px) md:translateY(40px);">
                            <div class="card-body bg-light rounded-top p-3 p-md-4 position-relative">
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge rounded-pill" style="background-color: #CD7F32; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 4px solid white;">3</span>
                                </div>
                                <i class="bi bi-trophy-fill display-4 mb-3 d-block" style="color: #CD7F32;"></i>
                                <h5 class="fw-bold text-truncate fs-6 fs-md-5">{{ $ranking[2]->user->name ?? 'Anônimo' }}</h5>
                                <p class="mb-0 text-muted fw-bold">{{ $ranking[2]->score }} pts</p>
                            </div>
                            <div class="py-2 rounded-bottom" style="background-color: #CD7F32;"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
