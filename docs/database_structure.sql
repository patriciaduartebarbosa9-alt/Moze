-- ============================================
-- MOZE - Estrutura da Base de Dados
-- ============================================

-- Tabela de Utilizadores (base para clientes e fotógrafos)
CREATE TABLE utilizadores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    tipo ENUM('cliente', 'fotografo') NOT NULL,
    foto_perfil VARCHAR(255),
    bio TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilizador_id INT NOT NULL UNIQUE,
    localizacao VARCHAR(255),
    preferencias TEXT,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Tabela de Fotógrafos
CREATE TABLE fotografos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilizador_id INT NOT NULL UNIQUE,
    especialidades VARCHAR(255), -- ex: "retrato,casamento,evento"
    preco_hora DECIMAL(10, 2),
    avaliacoes_media FLOAT DEFAULT 0,
    numero_avaliacoes INT DEFAULT 0,
    bio_profissional TEXT,
    portfolio_url VARCHAR(255),
    disponivel BOOLEAN DEFAULT 1,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Tabela de Disponibilidades
CREATE TABLE disponibilidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fotografo_id INT NOT NULL,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    reservado BOOLEAN DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fotografo_id) REFERENCES fotografos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (fotografo_id, data, hora_inicio)
);

-- Tabela de Reservas
CREATE TABLE reservas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    fotografo_id INT NOT NULL,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    local_destino VARCHAR(255) NOT NULL,
    tipo_fotografia VARCHAR(100) NOT NULL, -- ex: "retrato", "casamento", etc
    preco DECIMAL(10, 2),
    status ENUM('pendente', 'confirmada', 'concluida', 'cancelada') DEFAULT 'pendente',
    notas_cliente TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (fotografo_id) REFERENCES fotografos(id) ON DELETE CASCADE
);

-- Tabela de Avaliações
CREATE TABLE avaliacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reserva_id INT NOT NULL UNIQUE,
    cliente_id INT NOT NULL,
    fotografo_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comentario TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (fotografo_id) REFERENCES fotografos(id) ON DELETE CASCADE
);

-- Tabela de Mensagens (Chat entre cliente e fotografo)
CREATE TABLE mensagens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reserva_id INT,
    remetente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (remetente_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Índices para melhorar performance
CREATE INDEX idx_utilizadores_tipo ON utilizadores(tipo);
CREATE INDEX idx_clientes_utilizador ON clientes(utilizador_id);
CREATE INDEX idx_fotografos_utilizador ON fotografos(utilizador_id);
CREATE INDEX idx_fotografos_especialidades ON fotografos(especialidades);
CREATE INDEX idx_disponibilidades_fotografo ON disponibilidades(fotografo_id);
CREATE INDEX idx_disponibilidades_data ON disponibilidades(data);
CREATE INDEX idx_reservas_cliente ON reservas(cliente_id);
CREATE INDEX idx_reservas_fotografo ON reservas(fotografo_id);
CREATE INDEX idx_reservas_data ON reservas(data);
CREATE INDEX idx_reservas_status ON reservas(status);
CREATE INDEX idx_avaliacoes_fotografo ON avaliacoes(fotografo_id);
CREATE INDEX idx_mensagens_reserva ON mensagens(reserva_id);
CREATE INDEX idx_mensagens_remetente ON mensagens(remetente_id);
CREATE INDEX idx_mensagens_destinatario ON mensagens(destinatario_id);
