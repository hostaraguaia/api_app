@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Gerenciar Perguntas</h2>
                        <a href="{{ route('user.questions.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Nova Pergunta
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="questionsTable" class="table table-hover table-striped align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 5%">#</th>
                                    <th scope="col" style="width: 45%">Pergunta</th>
                                    <th scope="col" style="width: 15%">Dificuldade</th>
                                    <th scope="col" style="width: 15%">Status</th>
                                    <th scope="col" style="width: 20%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                    <tr>
                                        <td>{{ $question->id }}</td>
                                        <td>{{ Str::limit($question->question, 60) }}</td>
                                        <td>
                                            @if($question->difficulty == 'easy')
                                                <span class="badge bg-success">Fácil</span>
                                            @elseif($question->difficulty == 'medium')
                                                <span class="badge bg-warning text-dark">Média</span>
                                            @else
                                                <span class="badge bg-danger">Difícil</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('user.questions.toggle', $question) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $question->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}">
                                                    {{ $question->is_active ? 'Ativa' : 'Inativa' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.questions.edit', $question) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('user.questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta pergunta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#questionsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            },
            order: [[0, 'desc']], // Order by ID desc
            columnDefs: [
                { orderable: false, targets: 4 } // Disable sorting on Actions column
            ]
        });
    });
</script>
@endpush
@endsection
