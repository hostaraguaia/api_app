@extends('layouts.app')

@section('content')
@push('styles')
<style>
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .pulse-animation {
        animation: pulse 2s infinite;
    }
</style>
@endpush
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
            <!-- Referral Section -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-share-fill"></i> Indique e Ganhe Pontos!</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-2">Compartilhe seu link exclusivo e ganhe <strong>1 ponto extra</strong> no ranking para cada amigo que se cadastrar!</p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ route('client.register', ['ref' => $client->referral_code]) }}" id="referralLink" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="https://wa.me/?text=Venha%20participar%20do%20Quiz%20B%C3%ADblico!%20Cadastre-se%20pelo%20meu%20link:%20{{ urlencode(route('client.register', ['ref' => $client->referral_code])) }}" target="_blank" class="btn btn-success">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </a>
                                <button onclick="copyLink()" class="btn btn-danger" title="Copiar link para Instagram">
                                    <i class="bi bi-instagram"></i> Instagram
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted">Seus Pontos de Indicação</h6>
                                <h1 class="display-4 text-primary fw-bold" id="referral-points">{{ $client->referral_points }}</h1>
                                <small class="text-muted">amigos indicados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Ranking -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Ranking em Tempo Real</h5>
                    <span class="badge bg-dark pulse-animation">Ao Vivo</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="rankingTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Participante</th>
                                    <th class="text-center">Pontuação Geral</th>
                                    <th class="text-end pe-4">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Ranking loaded via JS -->
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

@push('scripts')
<script>
    function copyLink() {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(copyText.value).then(function() {
                alert("Link copiado para a área de transferência!");
            }, function(err) {
                console.error('Erro ao copiar: ', err);
                fallbackCopyText(copyText);
            });
        } else {
            fallbackCopyText(copyText);
        }
    }

    function fallbackCopyText(inputElement) {
        try {
            document.execCommand('copy');
            alert("Link copiado para a área de transferência!");
        } catch (err) {
            console.error('Erro ao copiar (fallback): ', err);
            alert("Não foi possível copiar automaticamente. Por favor, copie manualmente.");
        }
    }

    // Real-time ranking
    function fetchRanking() {
        fetch('/api/quiz/ranking')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#rankingTable tbody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Nenhum participante ainda. Seja o primeiro!
                            </td>
                        </tr>
                    `;
                    return;
                }

                data.forEach((item, index) => {
                    const isCurrentUser = item.is_current_user;
                    const rowClass = isCurrentUser ? 'table-primary fw-bold' : '';
                    const badge = index < 3 ? `<i class="bi bi-trophy-fill text-${index === 0 ? 'warning' : (index === 1 ? 'secondary' : 'danger')}"></i>` : '';
                    
                    const row = `
                        <tr class="${rowClass}">
                            <td class="ps-4 align-middle">${index + 1} ${badge}</td>
                            <td class="align-middle">
                                ${item.name}
                                ${isCurrentUser ? '<span class="badge bg-primary ms-2">Você</span>' : ''}
                            </td>
                            <td class="text-center align-middle fw-bold">${item.score}</td>
                            <td class="text-end pe-4 align-middle text-muted small">${item.date}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => console.error('Erro ao carregar ranking:', error));
    }

    // Real-time stats
    function fetchStats() {
        fetch('{{ route("client.stats") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('referral-points').textContent = data.referral_points;
                // Update total score if element exists (it might have been removed in previous steps, but let's be safe)
                // Actually, we removed the stats cards, so we only need to update referral points.
                // Wait, we kept the referral points display in the referral section.
            })
            .catch(error => console.error('Erro ao carregar estatísticas:', error));
    }

    // Initial load
    fetchRanking();
    // fetchStats(); // Initial load is already server-side rendered

    // Poll every 10 seconds
    setInterval(() => {
        fetchRanking();
        fetchStats();
    }, 10000);
</script>
@endpush
