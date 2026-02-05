CREATE TABLE
    tipo_diretorio (
        id CHAR(36) NOT NULL COMMENT 'UUID do tipo de diretório',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição do tipo de diretório',
        PRIMARY KEY (id),
        UNIQUE KEY tipos_diretorio_descricao_unico (descricao)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    tipo_diretorio (id, descricao)
VALUES
    (1, 'Diretório Estadual'),
    (2, 'Diretório Municipal');

CREATE TABLE
    diretorio (
        id CHAR(36) NOT NULL COMMENT 'UUID do diretório',
        tipo_id CHAR(36) NOT NULL COMMENT 'UUID do tipo de diretório',
        municipio VARCHAR(255) NOT NULL COMMENT 'Nome do diretório',
        endereco VARCHAR(255) NULL DEFAULT NULL COMMENT 'Endereço do diretório',
        telefone VARCHAR(20) NULL DEFAULT NULL COMMENT 'Telefone do diretório',
        email VARCHAR(255) NULL DEFAULT NULL COMMENT 'E-mail do diretório',
        created_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data e hora de criação do registro',
        updated_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data e hora da última atualização do registro',
        PRIMARY KEY (id),
        UNIQUE KEY diretorios_nome_unico (municipio),
        CONSTRAINT diretorios_tipo_id_fk FOREIGN KEY (tipo_id) REFERENCES tipo_diretorio (id) ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    DIRETORIO (ID, TIPO_ID, MUNICIPIO)
VALUES
    (UUID (), 1, 'DIRETÓRIO ESTADUAL'),
    (UUID (), 2, 'AMAPÁ'),
    (UUID (), 2, 'CALÇOENE'),
    (UUID (), 2, 'CUTIAS'),
    (UUID (), 2, 'FERREIRA GOMES'),
    (UUID (), 2, 'ITAUBAL'),
    (UUID (), 2, 'LARANJAL DO JARI'),
    (UUID (), 2, 'MACAPÁ'),
    (UUID (), 2, 'MAZAGÃO'),
    (UUID (), 2, 'OIAPOQUE'),
    (UUID (), 2, 'PEDRA BRANCA DO AMAPARI'),
    (UUID (), 2, 'PORTO GRANDE'),
    (UUID (), 2, 'PRACUÚBA'),
    (UUID (), 2, 'SANTANA'),
    (UUID (), 2, 'SERRA DO NAVIO'),
    (UUID (), 2, 'TARTARUGALZINHO'),
    (UUID (), 2, 'VITÓRIA DO JARI');

CREATE TABLE
    permissao (
        id CHAR(36) NOT NULL COMMENT 'UUID da permissão',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição da permissão',
        PRIMARY KEY (id),
        UNIQUE KEY permissoes_descricao_unico (descricao)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    permissao (id, descricao)
VALUES
    (1, 'Administrador'),
    (2, 'Usuário');

CREATE TABLE
    usuario (
        id CHAR(36) NOT NULL COMMENT 'UUID do usuário',
        nome VARCHAR(255) NOT NULL COMMENT 'Nome do usuário',
        email VARCHAR(255) NOT NULL COMMENT 'E-mail do usuário',
        senha VARCHAR(255) NOT NULL COMMENT 'Hash da senha do usuário',
        permissao_id CHAR(36) NOT NULL COMMENT 'UUID da permissão do usuário',
        diretorio_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do diretório associado ao usuário',
        ativo BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Usuário está ativo',
        token VARCHAR(255) NULL DEFAULT NULL COMMENT 'Token de autenticação',
        created_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data e hora de criação do registro',
        updated_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data e hora da última atualização do registro',
        PRIMARY KEY (id),
        CONSTRAINT usuarios_permissao_id_fk FOREIGN KEY (permissao_id) REFERENCES permissao (id) ON DELETE RESTRICT,
        CONSTRAINT usuarios_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE SET NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    usuario_log (
        usuario_id CHAR(36) NOT NULL COMMENT 'UUID do usuário',
        created_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data e hora de criação do registro',
        updated_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data e hora da última atualização do registro',
        CONSTRAINT usuarios_log_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    filiado (
        id CHAR(36) NOT NULL COMMENT 'UUID do filiado',
        nome VARCHAR(255) NOT NULL COMMENT 'Nome do filiado',
        email VARCHAR(255) DEFAULT NULL COMMENT 'E-mail do filiado',
        telefone VARCHAR(20) DEFAULT NULL COMMENT 'Telefone do filiado',
        data_nascimento VARCHAR(5) DEFAULT NULL COMMENT 'Data de nascimento do filiado',
        data_filiacao VARCHAR(15) DEFAULT NULL COMMENT 'Data de filiação do filiado',
        data_desfiliacao VARCHAR(15) DEFAULT NULL COMMENT 'Data de desfilição do filiado',
        endereco VARCHAR(100) DEFAULT NULL COMMENT 'Endereço do filiado',
        bairro VARCHAR(100) DEFAULT NULL COMMENT 'Bairro do filiado',
        cidade VARCHAR(100) DEFAULT NULL COMMENT 'Cidade do filiado',
        estado CHAR(2) DEFAULT NULL COMMENT 'Estado do filiado',
        cep VARCHAR(10) DEFAULT NULL COMMENT 'CEP do filiado',
        cpf VARCHAR(14) DEFAULT NULL COMMENT 'CPF do filiado',
        rg VARCHAR(20) DEFAULT NULL COMMENT 'RG do filiado',
        titulo_eleitoral VARCHAR(20) DEFAULT NULL COMMENT 'Título de eleitor do filiado',
        zona_eleitoral VARCHAR(10) DEFAULT NULL COMMENT 'Zona eleitoral do filiado',
        secao_eleitoral VARCHAR(10) DEFAULT NULL COMMENT 'Seção eleitoral do filiado',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado ao filiado',
        sexo ENUM ('MASCULINO', 'FEMININO', 'OUTRO') DEFAULT NULL COMMENT 'Sexo do filiado',
        ativo BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Indica se o filiado está ativo',
        foto VARCHAR(255) DEFAULT NULL COMMENT 'Caminho da foto do filiado',
        informacoes_adicionais TEXT DEFAULT NULL COMMENT 'Informações adicionais sobre o filiado',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        usuario_id CHAR(36) DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        PRIMARY KEY (id),
        CONSTRAINT filiados_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT,
        CONSTRAINT filiados_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    cargo_eletivo (
        id CHAR(36) NOT NULL COMMENT 'UUID do cargo eletivo',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição do cargo eletivo',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado ao cargo eletivo',
        multiplo BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica se o cargo permite múltiplos ocupantes',
        usuario_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        CONSTRAINT fk_cargo_usuario FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE,
        CONSTRAINT fk_cargo_diretorio FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT,
        PRIMARY KEY (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    cargo_eletivo_membros (
        filiado_id CHAR(36) NOT NULL COMMENT 'UUID do filiado',
        cargo_id CHAR(36) NOT NULL COMMENT 'UUID do cargo eletivo',
        inicio_mandato DATE NULL DEFAULT NULL COMMENT 'Data de início do mandato',
        fim_mandato DATE NULL DEFAULT NULL COMMENT 'Data de fim do mandato',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (filiado_id, cargo_id),
        CONSTRAINT filiados_cargos_eletivos_filiado_id_fk FOREIGN KEY (filiado_id) REFERENCES filiado (id) ON DELETE CASCADE,
        CONSTRAINT filiados_cargos_eletivos_cargo_id_fk FOREIGN KEY (cargo_id) REFERENCES cargo_eletivo (id) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    comissao_tipo (
        id CHAR(36) NOT NULL COMMENT 'UUID do tipo de comissão',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição do tipo de comissão',
        PRIMARY KEY (id),
        UNIQUE KEY comissoes_tipos_descricao_unico (descricao)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    comissao_tipo (id, descricao)
VALUES
    (1, 'Comissão Definitiva'),
    (2, 'Comissão Provisória');

CREATE TABLE
    comissao (
        id CHAR(36) NOT NULL COMMENT 'UUID da comissão',
        tipo_id CHAR(36) NOT NULL COMMENT 'UUID do tipo de comissão',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado à comissão',
        data_inicio DATE NULL DEFAULT NULL COMMENT 'Data de início da comissão',
        data_fim DATE NULL DEFAULT NULL COMMENT 'Data de fim da comissão',
        usuario_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        CONSTRAINT comissoes_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT,
        CONSTRAINT comissoes_tipo_id_fk FOREIGN KEY (tipo_id) REFERENCES comissao_tipo (id) ON DELETE RESTRICT,
        CONSTRAINT comissoes_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    cargo_comissao (
        id CHAR(36) NOT NULL COMMENT 'UUID do cargo eletivo',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição do cargo eletivo',
        comissao_id CHAR(36) NOT NULL COMMENT 'UUID da comissão associada ao cargo eletivo',
        multiplo BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica se o cargo permite múltiplos ocupantes',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        usuario_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        CONSTRAINT cargos_eletivos_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE,
        CONSTRAINT cargos_eletivos_comissao_id_fk FOREIGN KEY (comissao_id) REFERENCES comissao (id) ON DELETE CASCADE,
        PRIMARY KEY (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    cargo_comissao_membros (
        filiado_id CHAR(36) NOT NULL,
        cargo_id CHAR(36) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (filiado_id, cargo_id),
        CONSTRAINT fk_ccm_filiado FOREIGN KEY (filiado_id) REFERENCES filiado (id) ON DELETE CASCADE,
        CONSTRAINT fk_ccm_cargo FOREIGN KEY (cargo_id) REFERENCES cargo_comissao (id) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    nucleo (
        id CHAR(36) NOT NULL COMMENT 'UUID do tipo de núcleo',
        nome VARCHAR(100) NOT NULL COMMENT 'Nome do tipo de núcleo',
        descricao TEXT DEFAULT NULL COMMENT 'Descrição do tipo de núcleo',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado ao tipo de núcleo',
        usuario_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        CONSTRAINT nucleos_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT,
        CONSTRAINT nucleos_tipos_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT,
        PRIMARY KEY (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    documento_tipo (
        id CHAR(36) NOT NULL COMMENT 'UUID do tipo de documento',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição do tipo de documento',
        PRIMARY KEY (id),
        UNIQUE KEY documentos_tipos_descricao_unico (descricao)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    documento_tipo (id, descricao)
VALUES
    (1, 'Ata'),
    (2, 'Relatório'),
    (3, 'Ofício'),
    (4, 'Parecer'),
    (5, 'Memorando'),
    (6, 'Portaria'),
    (7, 'Convocação'),
    (8, 'Despacho'),
    (9, 'Requerimento'),
    (10, 'Ofício Circular'),
    (11, 'Edital'),
    (12, 'Projeto de Lei'),
    (13, 'Ata de Reunião'),
    (14, 'Relatório Técnico'),
    (15, 'Notificação');

CREATE TABLE
    documento (
        id CHAR(36) NOT NULL COMMENT 'UUID do documento',
        tipo_id CHAR(36) NOT NULL COMMENT 'UUID do tipo de documento',
        titulo VARCHAR(255) NOT NULL COMMENT 'Título do documento',
        arquivo VARCHAR(255) NOT NULL COMMENT 'Caminho do arquivo do documento',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado ao documento',
        usuario_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        CONSTRAINT documentos_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT,
        CONSTRAINT documentos_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT,
        CONSTRAINT documentos_tipo_id_fk FOREIGN KEY (tipo_id) REFERENCES documento_tipo (id) ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    tipo_agenda (
        id CHAR(36) NOT NULL COMMENT 'UUID do tipo de agenda',
        descricao VARCHAR(100) NOT NULL COMMENT 'Descrição do tipo de agenda',
        PRIMARY KEY (id),
        UNIQUE KEY tipos_agenda_descricao_unico (descricao)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    tipo_agenda (id, descricao)
VALUES
    (1, 'Reunião'),
    (2, 'Evento'),
    (3, 'Compromisso'),
    (4, 'Outro'),
    (5, 'Comício'),
    (6, 'Encontro com eleitores'),
    (7, 'Reunião de diretoria'),
    (8, 'Sessão legislativa'),
    (9, 'Debate público'),
    (10, 'Campanha de voluntariado'),
    (11, 'Treinamento interno'),
    (12, 'Entrevista ou coletiva de imprensa'),
    (13, 'Visita institucional'),
    (14, 'Planejamento estratégico');

CREATE TABLE
    agenda (
        id CHAR(36) NOT NULL COMMENT 'UUID da agenda',
        tipo_id CHAR(36) NOT NULL COMMENT 'UUID do tipo de agenda',
        titulo VARCHAR(255) NOT NULL COMMENT 'Título da agenda',
        descricao TEXT DEFAULT NULL COMMENT 'Descrição da agenda',
        data_inicio DATETIME NOT NULL COMMENT 'Data e hora de início da agenda',
        data_fim DATETIME NOT NULL COMMENT 'Data e hora de fim da agenda',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado à agenda',
        usuario_id CHAR(36) NULL DEFAULT NULL COMMENT 'UUID do usuário que criou o registro',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        CONSTRAINT agendas_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT,
        CONSTRAINT agendas_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE RESTRICT,
        CONSTRAINT agendas_tipo_id_fk FOREIGN KEY (tipo_id) REFERENCES tipo_agenda (id) ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    presenca_agenda (
        agenda_id CHAR(36) NOT NULL,
        filiado_id CHAR(36) NOT NULL,
        presente BOOLEAN DEFAULT TRUE,
        PRIMARY KEY (agenda_id, filiado_id),
        CONSTRAINT fk_presenca_agenda FOREIGN KEY (agenda_id) REFERENCES agenda (id) ON DELETE CASCADE,
        CONSTRAINT fk_presenca_filiado FOREIGN KEY (filiado_id) REFERENCES filiado (id) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    grupo_politico (
        id CHAR(36) NOT NULL COMMENT 'UUID do grupo político',
        nome VARCHAR(100) NOT NULL COMMENT 'Nome do grupo político',
        descricao TEXT DEFAULT NULL COMMENT 'Descrição do grupo político',
        diretorio_id CHAR(36) NOT NULL COMMENT 'UUID do diretório associado ao grupo político',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        CONSTRAINT grupo_politico_diretorio_id_fk FOREIGN KEY (diretorio_id) REFERENCES diretorio (id) ON DELETE RESTRICT,
        PRIMARY KEY (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    grupo_politico_membros (
        grupo_id CHAR(36) NOT NULL COMMENT 'UUID do grupo político',
        filiado_id CHAR(36) NOT NULL COMMENT 'UUID do filiado',
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (grupo_id, filiado_id),
        CONSTRAINT grupo_politico_membros_grupo_id_fk FOREIGN KEY (grupo_id) REFERENCES grupo_politico (id) ON DELETE CASCADE,
        CONSTRAINT grupo_politico_membros_filiado_id_fk FOREIGN KEY (filiado_id) REFERENCES filiado (id) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;