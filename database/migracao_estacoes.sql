CREATE TABLE IF NOT EXISTS estacoes (
    estacao_id INT AUTO_INCREMENT PRIMARY KEY,
    nome_estacao VARCHAR(155) NOT NULL UNIQUE,
    localizacao VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'Ativo'
);

INSERT IGNORE INTO estacoes (nome_estacao, localizacao, status) VALUES
('Estação Central', 'Centro', 'Ativo'),
('Estação Norte', 'Zona Norte', 'Ativo'),
('Estação Sul', 'Zona Sul', 'Ativo'),
('Estação Leste', 'Zona Leste', 'Ativo'),
('Estação Oeste', 'Zona Oeste', 'Ativo'),
('Terminal de Carga Porto', 'Região Portuária', 'Ativo'),
('Estação Industrial', 'Distrito Industrial', 'Ativo'),
('Estação Aeroporto', 'Região Aeroportuária', 'Ativo'),
('Estação Universitária', 'Região Universitária', 'Ativo');
