CREATE DATABASE IF NOT EXISTS celestial_steel_db;
USE celestial_steel_db;

CREATE TABLE Cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT
);

INSERT INTO Cargos (id, nome, descricao) VALUES 
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Funcionário', 'Acesso limitado ao sistema');

CREATE TABLE usuarios(
	usuario_id INT AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(155) NOT NULL,
	telefone VARCHAR(11) NOT NULL,
	cpf VARCHAR(11) NOT NULL,
	email VARCHAR(155) NOT NULL,
	senha VARCHAR(255) NULL,
    cargo_id INT,
    FOREIGN KEY (cargo_id) REFERENCES Cargos(id)
);

INSERT INTO usuarios (nome, telefone, cpf, email, senha, cargo_id) VALUES 
('Administrador', '11999999999', '12345678901', 'admin@teste.com', 'adm1n123', 1),
('Funcionário', '11999999999', '22345678901', 'funcionario@teste.com', 'func1234', 2);

CREATE TABLE trens(
	trem_id INT PRIMARY KEY,
	nome_trem VARCHAR(155) NOT NULL,
	responsavel_id INT NOT NULL,
	FOREIGN KEY (responsavel_id) REFERENCES usuarios(usuario_id)
);

INSERT INTO trens (trem_id, nome_trem, responsavel_id) VALUES 
(100, 'Trem Expresso Norte', 1),
(200, 'Trem Noturno Central', 1),
(300, 'Trem Regional Sul', 2),
(400, 'Trem de Carga Leste', 1);

CREATE TABLE estacoes(
	estacao_id INT AUTO_INCREMENT PRIMARY KEY,
	nome_estacao VARCHAR(155) NOT NULL UNIQUE,
	localizacao VARCHAR(255),
	status VARCHAR(20) NOT NULL DEFAULT 'Ativo'
);

INSERT INTO estacoes (nome_estacao, localizacao, status) VALUES 
('Estação Central', 'Centro', 'Ativo'),
('Estação Norte', 'Zona Norte', 'Ativo'),
('Estação Sul', 'Zona Sul', 'Ativo'),
('Estação Leste', 'Zona Leste', 'Ativo'),
('Estação Oeste', 'Zona Oeste', 'Ativo'),
('Terminal de Carga Porto', 'Região Portuária', 'Ativo');

CREATE TABLE rotas(
	rota_id INT AUTO_INCREMENT PRIMARY KEY,
	ponto_partida VARCHAR(155) NOT NULL,
	ponto_chegada VARCHAR(155) NOT NULL,
	nome VARCHAR(155) NOT NULL,
	trem_id INT NOT NULL,
	descricao TEXT,
	status VARCHAR(20) NOT NULL DEFAULT 'Ativo',
	FOREIGN KEY (trem_id) REFERENCES trens(trem_id)
);

CREATE TABLE rotas_trens (
	rotas_trens_id INT AUTO_INCREMENT PRIMARY KEY,
	rota_id INT,
	trem_id INT,
	FOREIGN KEY (rota_id) REFERENCES rotas(rota_id),
	FOREIGN KEY (trem_id) REFERENCES trens(trem_id)
);

CREATE TABLE sensores(
	sensor_id INT AUTO_INCREMENT PRIMARY KEY,
	nome_sensor VARCHAR(155) NOT NULL,
	localizacao VARCHAR(155) NOT NULL,
	tipo_dado VARCHAR(155) NOT NULL,
	status VARCHAR(20) NOT NULL DEFAULT 'Ativo'
);

CREATE TABLE valor_sensor(
	valor_sensor_id INT AUTO_INCREMENT PRIMARY KEY,
    valor_sensor_ FLOAT,
    sensor_id INT,
    FOREIGN KEY (sensor_id) REFERENCES sensores(sensor_id)
);

CREATE TABLE falha(
	falha_id INT AUTO_INCREMENT PRIMARY KEY,
	sensor_id INT NOT NULL,
	trem_id  INT NOT NULL,
	FOREIGN KEY (sensor_id) REFERENCES sensores(sensor_id),
	FOREIGN KEY (trem_id) REFERENCES trens(trem_id)
);

CREATE TABLE relatorio (
	relatorio_id INT AUTO_INCREMENT PRIMARY KEY,
    data_relatorio DATE NOT NULL,
    falha_id INT,
    FOREIGN KEY (falha_id) REFERENCES falha(falha_id)
);

CREATE TABLE Permissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_acao VARCHAR(100) NOT NULL, 
    descricao TEXT
);

CREATE TABLE Cargos_Permissoes (
    cargo_id INT,
    permissao_id INT,
    PRIMARY KEY (cargo_id, permissao_id),
    FOREIGN KEY (cargo_id) REFERENCES Cargos(id),
    FOREIGN KEY (permissao_id) REFERENCES Permissoes(id)
);
