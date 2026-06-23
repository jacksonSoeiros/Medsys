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

