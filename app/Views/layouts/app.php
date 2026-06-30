<?php
$currentUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$isActiveRoute = static function (string $route) use ($currentUri): bool {
    $normalizedRoute = trim($route, '/');

    if ($normalizedRoute === '') {
        return $currentUri === '';
    }

    return $currentUri === $normalizedRoute || str_starts_with($currentUri, $normalizedRoute . '/');
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MedCare' ?></title>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('medcare-theme');
            const theme = savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/medcare-elegant.css') ?>" rel="stylesheet">
</head>
<body class="app-shell" data-session-timeout="<?= (int) \App\Helpers\Session::getTimeoutInSeconds() ?>">
    <nav class="navbar navbar-expand-lg navbar-dark topbar-glass fixed-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand brand-lockup" href="<?= url('dashboard') ?>">
                <img src="<?= asset('assets/img/prefeitura-paragominas-2025.png') ?>" alt="Prefeitura de Paragominas" class="brand-logo">
                <span class="brand-copy">
                    <small>Med-Sys | Gestão clínica</small>
                </span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav nav-pills-soft me-auto gap-lg-2 pt-3 pt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $isActiveRoute('dashboard') ? 'active' : '' ?>" href="<?= url('dashboard') ?>">Painel</a>
                    </li>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'chefe_equipe'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isActiveRoute('funcionarios') ? 'active' : '' ?>" href="<?= url('funcionarios') ?>">Funcionários</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isActiveRoute('medicos') ? 'active' : '' ?>" href="<?= url('medicos') ?>">Médicos</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario', 'consultador', 'chefe_equipe', 'medico'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isActiveRoute('pacientes') || $isActiveRoute('prontuarios') ? 'active' : '' ?>" href="<?= url('pacientes') ?>">Pacientes</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'chefe_equipe'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isActiveRoute('relatorios') ? 'active' : '' ?>" href="<?= url('relatorios') ?>">Relatórios</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-lg-flex align-items-center gap-3 pt-3 pt-lg-0">
                    <button type="button" class="theme-toggle-btn" data-theme-toggle aria-label="Alternar tema">◐</button>
                    <div class="user-chip">
                        <div>
                            <strong><?= \App\Helpers\Session::get('usuario_nome') ?></strong>
                            <span><?= roleLabel(\App\Helpers\Session::get('usuario_papel')) ?></span>
                        </div>
                    </div>
                    <a href="<?= url('logout') ?>" class="btn btn-outline-light btn-sm px-3 btn-logout">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container app-main">
        <?= $content ?? '' ?>
    </main>

    <footer class="app-footer ">
        <div class="container footer-content ">
            <div>
                <strong>Med-Sys</strong>
                <span>Copyright &copy; <?= date('Y') ?> Prefeitura de Paragominas. Todos os direitos reservados.</span>
            </div>
            <div>
                <strong>Desenvolvimento</strong>
                <span>Dev-JacksonSF</span>
            </div>
        </div>
    </footer>

    <script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sessionTimeout = Number(document.body.getAttribute('data-session-timeout') || 600) * 1000;
            const sessionPingUrl = <?= json_encode(url('sessao/ping')) ?>;
            const logoutUrl = <?= json_encode(url('logout')) ?>;
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

            const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');
            const applyTheme = function (theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('medcare-theme', theme);
                themeToggleButtons.forEach(function (button) {
                    button.textContent = theme === 'dark' ? '☀' : '◐';
                    button.setAttribute('aria-label', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
                });
            };

            const currentTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            applyTheme(currentTheme);

            themeToggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    applyTheme(nextTheme);
                });
            });

            let logoutTimerId = null;
            let lastPingAt = 0;

            const pingSession = function () {
                const now = Date.now();

                if ((now - lastPingAt) < 60000) {
                    return;
                }

                lastPingAt = now;
                fetch(sessionPingUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).catch(function () {
                });
            };

            const resetInactivityTimer = function (withPing) {
                window.clearTimeout(logoutTimerId);
                logoutTimerId = window.setTimeout(function () {
                    window.location.href = logoutUrl;
                }, sessionTimeout);

                if (withPing) {
                    pingSession();
                }
            };

            ['click', 'keydown', 'scroll', 'touchstart', 'mousemove'].forEach(function (eventName) {
                document.addEventListener(eventName, function () {
                    resetInactivityTimer(true);
                }, { passive: true });
            });

            resetInactivityTimer(false);
        });
    </script>
</body>
</html>
