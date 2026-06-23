/*Src Schema SQL*/

/*buscas futuras*/
CREATE EXTENSION IF NOT EXISTS pg_trgm;


Função para atualizar automaticamente
CREATE OR REPLACE FUNCTION atualizar_timestamp()
RETURNS TRIGGER AS
$$
BEGIN
    NEW.atualizado_em = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


/*Tabela usuarios*/
CREATE TABLE usuarios (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    papel VARCHAR(20) NOT NULL
        CHECK (papel IN ('administrador','funcionario','medico')),
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    ultimo_login TIMESTAMP,
    criado_em TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMP NOT NULL DEFAULT NOW()
);


/*Trigger usuarios*/
CREATE TRIGGER trg_usuarios_atualizado_em
BEFORE UPDATE ON usuarios
FOR EACH ROW
EXECUTE FUNCTION atualizar_timestamp();


/*Tabela funcionarios*/
CREATE TABLE funcionarios (
    id BIGSERIAL PRIMARY KEY,
    usuario_id BIGINT NOT NULL UNIQUE,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    cargo VARCHAR(100),
    criado_em TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_funcionarios_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*Trigger funcionarios*/
CREATE TRIGGER trg_funcionarios_atualizado_em
BEFORE UPDATE ON funcionarios
FOR EACH ROW
EXECUTE FUNCTION atualizar_timestamp();


/*Tabela medicos*/
CREATE TABLE medicos (
    id BIGSERIAL PRIMARY KEY,
    usuario_id BIGINT NOT NULL UNIQUE,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    crm VARCHAR(20) NOT NULL UNIQUE,
    especialidade VARCHAR(100),
    telefone VARCHAR(20),
    criado_em TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_medicos_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*Trigger medico*/
CREATE TRIGGER trg_medicos_atualizado_em
BEFORE UPDATE ON medicos
FOR EACH ROW
EXECUTE FUNCTION atualizar_timestamp();


/*Tabela pacientes*/
CREATE TABLE pacientes (

    id BIGSERIAL PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20),

    endereco_logradouro VARCHAR(255),
    endereco_numero VARCHAR(20),
    endereco_complemento VARCHAR(100),
    endereco_bairro VARCHAR(100),
    endereco_cidade VARCHAR(100),
    endereco_uf CHAR(2),
    endereco_cep VARCHAR(9),

    cadastrado_por BIGINT,

    criado_em TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_paciente_funcionario
        FOREIGN KEY (cadastrado_por)
        REFERENCES funcionarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);


/*Trigger pacientes*/
CREATE TRIGGER trg_pacientes_atualizado_em
BEFORE UPDATE ON pacientes
FOR EACH ROW
EXECUTE FUNCTION atualizar_timestamp();


/*Tabela prontuarios*/
CREATE TABLE prontuarios (
    id BIGSERIAL PRIMARY KEY,

    paciente_id BIGINT NOT NULL UNIQUE,

    criado_em TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_prontuario_paciente
        FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*Trigger prontuarios*/
CREATE TRIGGER trg_prontuarios_atualizado_em
BEFORE UPDATE ON prontuarios
FOR EACH ROW
EXECUTE FUNCTION atualizar_timestamp();


/*Tabela prontuario_evolucoes*/
CREATE TABLE prontuario_evolucoes (
    id BIGSERIAL PRIMARY KEY,

    prontuario_id BIGINT NOT NULL,

    medico_id BIGINT NOT NULL,

    texto_evolucao TEXT NOT NULL,

    registrado_em TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_evolucao_prontuario
        FOREIGN KEY (prontuario_id)
        REFERENCES prontuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_evolucao_medico
        FOREIGN KEY (medico_id)
        REFERENCES medicos(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


/*Tabela logs*/
CREATE TABLE logs (
    id BIGSERIAL PRIMARY KEY,

    usuario_id BIGINT,

    acao VARCHAR(100) NOT NULL,

    tabela_afetada VARCHAR(100),

    registro_id BIGINT,

    descricao TEXT,

    ip_origem VARCHAR(45),

    criado_em TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_log_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);


/*Tabela login_tentativas*/
CREATE TABLE login_tentativas (
    id BIGSERIAL PRIMARY KEY,

    email VARCHAR(255) NOT NULL UNIQUE,

    tentativas INTEGER NOT NULL DEFAULT 0,

    ultima_tentativa TIMESTAMP,

    bloqueado_ate TIMESTAMP
);


/*Tabela sessoes*/
CREATE TABLE sessoes (
    id BIGSERIAL PRIMARY KEY,

    usuario_id BIGINT NOT NULL,

    session_id VARCHAR(255) NOT NULL,

    ip VARCHAR(45),

    user_agent TEXT,

    ultimo_acesso TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_sessao_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*Tabela configuracoes*/
CREATE TABLE configuracoes (
    id BIGSERIAL PRIMARY KEY,

    nome_sistema VARCHAR(100) NOT NULL,

    versao VARCHAR(20) NOT NULL,

    timezone VARCHAR(100) NOT NULL,

    ambiente VARCHAR(20) NOT NULL,

    empresa VARCHAR(255),

    email_suporte VARCHAR(255),

    criado_em TIMESTAMP NOT NULL DEFAULT NOW()
);


/*Índices*/
CREATE INDEX idx_paciente_nome
ON pacientes(nome_completo);

CREATE INDEX idx_paciente_cidade
ON pacientes(endereco_cidade);

CREATE INDEX idx_prontuario_paciente
ON prontuarios(paciente_id);

CREATE INDEX idx_evolucao_prontuario
ON prontuario_evolucoes(prontuario_id);

CREATE INDEX idx_evolucao_medico
ON prontuario_evolucoes(medico_id);

CREATE INDEX idx_evolucao_data
ON prontuario_evolucoes(registrado_em DESC);

CREATE INDEX idx_log_usuario
ON logs(usuario_id);

CREATE INDEX idx_log_data
ON logs(criado_em DESC);

CREATE INDEX idx_sessao_usuario
ON sessoes(usuario_id);


/*Comentários das tabelas*/
COMMENT ON TABLE usuarios IS 'Usuários autenticados do sistema';
COMMENT ON TABLE funcionarios IS 'Funcionários administrativos';
COMMENT ON TABLE medicos IS 'Profissionais médicos';
COMMENT ON TABLE pacientes IS 'Cadastro geral de pacientes';
COMMENT ON TABLE prontuarios IS 'Prontuário principal do paciente';
COMMENT ON TABLE prontuario_evolucoes IS 'Histórico de evoluções médicas';
COMMENT ON TABLE logs IS 'Auditoria do sistema';
COMMENT ON TABLE login_tentativas IS 'Controle de tentativas de login';
COMMENT ON TABLE sessoes IS 'Sessões ativas';
COMMENT ON TABLE configuracoes IS 'Configurações gerais';