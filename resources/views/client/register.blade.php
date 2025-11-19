@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0 font-weight-bold">Criar Nova Conta</h3>
                    <p class="mb-0 opacity-75">Junte-se a nós para salvar seu progresso</p>
                </div>
                <div class="card-body p-5">
                    <!-- Progress Bar -->
                    <div class="position-relative mb-5">
                        <div class="progress" style="height: 2px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;" id="progressBar"></div>
                        </div>
                        <div class="d-flex justify-content-between position-absolute w-100" style="top: -14px;">
                            <div class="step-indicator active" data-step="1">
                                <div class="step-circle bg-primary text-white">1</div>
                                <div class="step-label mt-1 small fw-bold text-primary">Dados Pessoais</div>
                            </div>
                            <div class="step-indicator" data-step="2">
                                <div class="step-circle bg-secondary text-white">2</div>
                                <div class="step-label mt-1 small text-muted">Endereço</div>
                            </div>
                            <div class="step-indicator" data-step="3">
                                <div class="step-circle bg-secondary text-white">3</div>
                                <div class="step-label mt-1 small text-muted">Segurança</div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('client.register.submit') }}" id="registerForm">
                        @csrf
                        <input type="hidden" name="quiz_attempt_id" value="{{ request('quiz_attempt_id') }}">
                        <input type="hidden" name="referral_code" value="{{ $referralCode ?? request('ref') }}">

                        <!-- Step 1: Dados Pessoais -->
                        <div class="step-content" id="step1">
                            <h4 class="mb-4 text-primary">Dados Pessoais</h4>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Seu nome completo">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cpf" class="form-label">CPF <span class="text-muted small">(Opcional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-card-heading"></i></span>
                                        <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00">
                                        @error('cpf')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Telefone/WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-whatsapp"></i></span>
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="(00) 00000-0000">
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary px-4 next-step">
                                    Próximo <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Endereço e Paróquia -->
                        <div class="step-content d-none" id="step2">
                            <h4 class="mb-4 text-primary">Localização e Paróquia</h4>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="zip_code" class="form-label">CEP</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                        <input id="zip_code" type="text" class="form-control @error('zip_code') is-invalid @enderror" name="zip_code" value="{{ old('zip_code') }}" placeholder="00000-000">
                                        @error('zip_code')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="city" class="form-label">Cidade</label>
                                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" placeholder="Sua cidade">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">Estado</label>
                                    <select id="state" class="form-select @error('state') is-invalid @enderror" name="state">
                                        <option value="">Selecione</option>
                                        <option value="AC">Acre</option>
                                        <option value="AL">Alagoas</option>
                                        <option value="AP">Amapá</option>
                                        <option value="AM">Amazonas</option>
                                        <option value="BA">Bahia</option>
                                        <option value="CE">Ceará</option>
                                        <option value="DF">Distrito Federal</option>
                                        <option value="ES">Espírito Santo</option>
                                        <option value="GO">Goiás</option>
                                        <option value="MA">Maranhão</option>
                                        <option value="MT">Mato Grosso</option>
                                        <option value="MS">Mato Grosso do Sul</option>
                                        <option value="MG">Minas Gerais</option>
                                        <option value="PA">Pará</option>
                                        <option value="PB">Paraíba</option>
                                        <option value="PR">Paraná</option>
                                        <option value="PE">Pernambuco</option>
                                        <option value="PI">Piauí</option>
                                        <option value="RJ">Rio de Janeiro</option>
                                        <option value="RN">Rio Grande do Norte</option>
                                        <option value="RS">Rio Grande do Sul</option>
                                        <option value="RO">Rondônia</option>
                                        <option value="RR">Roraima</option>
                                        <option value="SC">Santa Catarina</option>
                                        <option value="SP">São Paulo</option>
                                        <option value="SE">Sergipe</option>
                                        <option value="TO">Tocantins</option>
                                    </select>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="parish" class="form-label">Sua Paróquia</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-house-heart"></i></span>
                                        <input id="parish" type="text" class="form-control @error('parish') is-invalid @enderror" name="parish" value="{{ old('parish') }}" placeholder="Nome da sua paróquia">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4 prev-step">
                                    <i class="bi bi-arrow-left me-2"></i> Voltar
                                </button>
                                <button type="button" class="btn btn-primary px-4 next-step">
                                    Próximo <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Segurança -->
                        <div class="step-content d-none" id="step3">
                            <h4 class="mb-4 text-primary">Segurança da Conta</h4>

                            <div class="mb-3">
                                <label for="email" class="form-label">Endereço de Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seu@email.com">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password-confirm" class="form-label">Confirmar Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Repita a senha">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4 prev-step">
                                    <i class="bi bi-arrow-left me-2"></i> Voltar
                                </button>
                                <button type="submit" class="btn btn-success px-4 btn-lg shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> Criar Conta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <p class="mb-0">Já tem uma conta? <a href="{{ route('client.login') }}" class="text-primary fw-bold text-decoration-none">Fazer Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    .step-indicator.active .step-circle {
        transform: scale(1.2);
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
    }
    .step-indicator.completed .step-circle {
        background-color: #198754 !important; /* Success color */
    }
    .step-label {
        font-size: 0.8rem;
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function(){
        $('#cpf').mask('000.000.000-00');
        $('#phone').mask('(00) 00000-0000');
        $('#zip_code').mask('00000-000');

        let currentStep = 1;
        const totalSteps = 3;

        function updateProgress() {
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            $('#progressBar').css('width', progress + '%');

            $('.step-indicator').each(function() {
                const step = $(this).data('step');
                const circle = $(this).find('.step-circle');
                const label = $(this).find('.step-label');

                if (step < currentStep) {
                    $(this).removeClass('active').addClass('completed');
                    circle.removeClass('bg-secondary bg-primary').addClass('bg-success');
                    circle.html('<i class="bi bi-check"></i>');
                    label.removeClass('text-muted text-primary').addClass('text-success');
                } else if (step === currentStep) {
                    $(this).addClass('active').removeClass('completed');
                    circle.removeClass('bg-secondary bg-success').addClass('bg-primary');
                    circle.text(step);
                    label.removeClass('text-muted text-success').addClass('text-primary fw-bold');
                } else {
                    $(this).removeClass('active completed');
                    circle.removeClass('bg-primary bg-success').addClass('bg-secondary');
                    circle.text(step);
                    label.removeClass('text-primary text-success fw-bold').addClass('text-muted');
                }
            });
        }

        $('.next-step').click(function() {
            // Simple validation before proceeding
            let isValid = true;
            const currentStepEl = $('#step' + currentStep);
            
            currentStepEl.find('input[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (isValid) {
                $('#step' + currentStep).addClass('d-none');
                currentStep++;
                $('#step' + currentStep).removeClass('d-none');
                updateProgress();
            }
        });

        $('.prev-step').click(function() {
            $('#step' + currentStep).addClass('d-none');
            currentStep--;
            $('#step' + currentStep).removeClass('d-none');
            updateProgress();
        });

        // Auto-fill address from CEP
        $('#zip_code').blur(function() {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                    if (!data.erro) {
                        $('#city').val(data.localidade);
                        $('#state').val(data.uf);
                        $('#parish').focus();
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
