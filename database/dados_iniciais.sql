
/*Configuração inicial do sistema*/
INSERT INTO configuracoes (
    nome_sistema,
    versao,
    timezone,
    ambiente,
    empresa,
    email_suporte
)
VALUES (
    'MedCare',
    '1.0.0',
    'America/Sao_Paulo',
    'production',
    'MedCare',
    'suporte@medcare.local'
);


/*Usuário Administrador*/

/*
Email:
admin@medcare.local

Senha:
Admin@123
*/

INSERT INTO usuarios (
    email,
    senha_hash,
    papel,
    ativo
)
VALUES (
    'admin@medcare.local',
    '$2y$12$Ctt6MLkvvYKSTth5aYnMU.yPRh5ll58h/ixRCay5h0XK57aQWcSum',
    'administrador',
    TRUE
);


/*Funcionário Administrador*/
INSERT INTO funcionarios (
    usuario_id,
    nome_completo,
    cpf,
    telefone,
    cargo
)
VALUES (
    1,
    'Administrador do Sistema',
    '000.000.000-00',
    '(00)00000-0000',
    'Administrador'
);


/*Futuro Instalador*/