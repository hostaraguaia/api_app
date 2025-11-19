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

            <!-- Ranking Section -->
            <div id="ranking-container" class="mt-5" style="display: none;">
                <h2 class="text-center mb-5 fw-bold text-uppercase" style="letter-spacing: 2px;">
                    <i class="bi bi-trophy-fill text-warning me-2"></i> Ranking Top 3
                </h2>
                
                <div class="row justify-content-center align-items-end text-center g-3 g-md-4" id="ranking-items">
                    <!-- Items will be injected via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateRanking() {
        fetch('/api/quiz/ranking')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('ranking-container');
                const itemsContainer = document.getElementById('ranking-items');
                
                if (data.length === 0) {
                    container.style.display = 'none';
                    return;
                }

                container.style.display = 'block';
                itemsContainer.innerHTML = '';

                // Helper to create card HTML
                const createCard = (item, rank) => {
                    let colClass, orderClass, badgeColor, iconColor, sizeClass, translateY;
                    
                    if (rank === 1) {
                        colClass = 'col-12 col-md-4 mb-4 mb-md-0';
                        orderClass = 'order-1 order-md-2';
                        badgeColor = 'bg-warning text-dark';
                        iconColor = 'text-warning';
                        sizeClass = 'p-4 p-md-5';
                        translateY = '';
                    } else if (rank === 2) {
                        colClass = 'col-6 col-md-3';
                        orderClass = 'order-2 order-md-1';
                        badgeColor = 'bg-secondary';
                        iconColor = 'text-secondary';
                        sizeClass = 'p-3 p-md-4';
                        translateY = 'transform: translateY(0px) md:translateY(20px);';
                    } else {
                        colClass = 'col-6 col-md-3';
                        orderClass = 'order-3 order-md-3';
                        badgeColor = 'bg-bronze'; // Custom class or inline style
                        iconColor = 'text-bronze';
                        sizeClass = 'p-3 p-md-4';
                        translateY = 'transform: translateY(0px) md:translateY(40px);';
                    }

                    const badgeStyle = rank === 3 ? 'background-color: #CD7F32;' : '';
                    const iconStyle = rank === 3 ? 'color: #CD7F32;' : '';
                    const bottomBarStyle = rank === 3 ? 'background-color: #CD7F32;' : '';
                    const bottomBarClass = rank === 1 ? 'bg-warning' : (rank === 2 ? 'bg-secondary' : '');

                    return `
                        <div class="${colClass} ${orderClass}">
                            <div class="card border-0 shadow${rank === 1 ? '' : '-sm'} mb-3 transform-hover" style="${translateY}">
                                <div class="card-body ${rank === 1 ? 'bg-white border border-warning border-bottom-0' : 'bg-light'} rounded-top ${sizeClass} position-relative">
                                    <div class="position-absolute top-0 start-50 translate-middle">
                                        <span class="badge rounded-pill ${badgeColor} border border-4 border-white shadow-sm" 
                                              style="width: ${rank === 1 ? '50' : '40'}px; height: ${rank === 1 ? '50' : '40'}px; display: flex; align-items: center; justify-content: center; font-size: ${rank === 1 ? '1.5' : '1.2'}rem; ${badgeStyle}">
                                            ${rank}
                                        </span>
                                    </div>
                                    <i class="bi bi-trophy-fill ${iconColor} display-${rank === 1 ? '1' : '4'} mb-3 d-block" style="${iconStyle}"></i>
                                    <h${rank === 1 ? '4' : '5'} class="fw-bold text-truncate ${rank !== 1 ? 'fs-6 fs-md-5' : ''}">${item.name}</h${rank === 1 ? '4' : '5'}>
                                    <p class="mb-0 text-muted fw-bold ${rank === 1 ? 'fs-5' : ''}">${item.score} pts</p>
                                </div>
                                <div class="${bottomBarClass} py-${rank === 1 ? '3' : '2'} rounded-bottom" style="${bottomBarStyle}"></div>
                            </div>
                        </div>
                    `;
                };

                // We need to re-order data to match visual layout: 2nd, 1st, 3rd
                // But HTML order is controlled by flex order classes, so we can just append in rank order (1, 2, 3)
                // and the CSS classes (order-1, order-2, etc) will handle the visual position.
                
                if (data[0]) itemsContainer.innerHTML += createCard(data[0], 1);
                if (data[1]) itemsContainer.innerHTML += createCard(data[1], 2);
                if (data[2]) itemsContainer.innerHTML += createCard(data[2], 3);
            })
            .catch(error => console.error('Error fetching ranking:', error));
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', updateRanking);

    // Poll every 10 seconds
    setInterval(updateRanking, 10000);
</script>
@endpush
        </div>
    </div>
</div>
@endsection
