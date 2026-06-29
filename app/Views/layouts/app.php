<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MedCare' ?></title>
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body { padding-top: 70px; }
        .card { border-radius: 0.5rem; }
        .btn-view {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
        .btn-view:hover,
        .btn-view:focus {
            background-color: #badbcc;
            border-color: #a3cfbb;
            color: #0a3622;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('dashboard') ?>">🏥 MedCare</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('dashboard') ?>">Início</a>
                    </li>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('funcionarios') ?>">Funcionários</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('medicos') ?>">Médicos</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario', 'medico'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('pacientes') ?>">Pacientes</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">Olá, <?= \App\Helpers\Session::get('usuario_nome') ?></span>
                    <a href="<?= url('logout') ?>" class="btn btn-outline-light btn-sm">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <?= $content ?? '' ?>
    </main>

    <script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formatCpf = function (value) {
                const digits = value.replace(/\D/g, '').slice(0, 11);
                return digits
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            };

            const formatPhone = function (value) {
                const digits = value.replace(/\D/g, '').slice(0, 11);

                if (digits.length <= 10) {
                    return digits
                        .replace(/(\d{2})(\d)/, '($1) $2')
                        .replace(/(\d{4})(\d)/, '$1-$2');
                }

                return digits
                    .replace(/(\d{2})(\d)/, '($1) $2')
                    .replace(/(\d{5})(\d)/, '$1-$2');
            };

            const formatCep = function (value) {
                const digits = value.replace(/\D/g, '').slice(0, 8);
                return digits.replace(/(\d{5})(\d)/, '$1-$2');
            };

            document.querySelectorAll('[data-mask="cpf"]').forEach(function (input) {
                input.addEventListener('input', function () {
                    input.value = formatCpf(input.value);
                });
                input.value = formatCpf(input.value);
            });

            document.querySelectorAll('[data-mask="phone"]').forEach(function (input) {
                input.addEventListener('input', function () {
                    input.value = formatPhone(input.value);
                });
                input.value = formatPhone(input.value);
            });

            document.querySelectorAll('[data-mask="cep"]').forEach(function (input) {
                input.addEventListener('input', function () {
                    input.value = formatCep(input.value);
                });
                input.value = formatCep(input.value);
            });

            document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.getAttribute('data-toggle-password'));
                    const showing = input.getAttribute('type') === 'text';

                    input.setAttribute('type', showing ? 'password' : 'text');
                    button.textContent = showing ? 'Mostrar' : 'Ocultar';
                });
            });

            const ufSelect = document.querySelector('[data-uf-select]');
            const cityInput = document.querySelector('[data-city-input]');
            const cityList = document.querySelector('[data-city-list]');
            const cepInput = document.querySelector('[data-cep]');
            const streetInput = document.querySelector('[name="endereco_logradouro"]');
            const neighborhoodInput = document.querySelector('[name="endereco_bairro"]');

            const loadCities = function (uf, selectedCity) {
                if (!ufSelect || !cityInput || !cityList || !uf) {
                    return;
                }

                cityInput.setAttribute('list', cityList.id);
                cityInput.placeholder = 'Carregando cidades...';

                fetch('https://brasilapi.com.br/api/ibge/municipios/v1/' + uf + '?providers=dados-abertos-br,gov')
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Falha ao carregar cidades');
                        }

                        return response.json();
                    })
                    .then(function (cities) {
                        cityList.innerHTML = '';

                        cities.forEach(function (city) {
                            const option = document.createElement('option');
                            option.value = city.nome;
                            cityList.appendChild(option);
                        });

                        cityInput.placeholder = 'Selecione ou digite a cidade';

                        if (selectedCity) {
                            cityInput.value = selectedCity;
                        }
                    })
                    .catch(function () {
                        cityInput.placeholder = 'Digite a cidade';
                    });
            };

            if (ufSelect && cityInput) {
                ufSelect.addEventListener('change', function () {
                    cityInput.value = '';

                    if (ufSelect.value) {
                        loadCities(ufSelect.value, '');
                    }
                });

                if (ufSelect.value) {
                    loadCities(ufSelect.value, cityInput.value);
                }
            }

            if (cepInput) {
                cepInput.addEventListener('blur', function () {
                    const cep = cepInput.value.replace(/\D/g, '');

                    if (cep.length !== 8) {
                        return;
                    }

                    fetch('https://viacep.com.br/ws/' + cep + '/json/')
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Falha ao consultar CEP');
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            if (data.erro) {
                                return;
                            }

                            if (streetInput && !streetInput.value) {
                                streetInput.value = data.logradouro || '';
                            }

                            if (neighborhoodInput && !neighborhoodInput.value) {
                                neighborhoodInput.value = data.bairro || '';
                            }

                            if (ufSelect && data.uf) {
                                ufSelect.value = data.uf;
                                loadCities(data.uf, data.localidade || '');
                            } else if (cityInput && data.localidade) {
                                cityInput.value = data.localidade;
                            }
                        })
                        .catch(function () {
                        });
                });
            }
        });
    </script>
</body>
</html>

