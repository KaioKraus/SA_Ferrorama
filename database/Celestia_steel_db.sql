CREATE DATABASE IF NOT EXISTS celestia_steel_db;
USE celestia_steel_db;

CREATE TABLE trens(
	trem_id INT AUTO_INCREMENT PRIMARY KEY,
	nome_trem VARCHAR(155) NOT NULL,
	dias_semana DATE NOT NULL,
	horario_partida DATETIME NOT NULL,
	horario_chegada DATETIME NOT NULL
);

CREATE TABLE rotas(
	rota_id INT AUTO_INCREMENT PRIMARY KEY,s
	dias_semana DATE NOT NULL,
	horario_partida DATETIME NOT NULL,
	horario_chegada DATETIME NOT NULL
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
	tipo_dado VARCHAR(155) NOT NULL
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

CREATE TABLE Cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT
);

CREATE TABLE usuarios(
	usuario_id INT AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(155) NOT NULL,
	telefone VARCHAR(11) NOT NULL,
	cpf VARCHAR(11) NOT NULL,
	email VARCHAR(155) NOT NULL,
    cargo_id INT,
    FOREIGN KEY (cargo_id) REFERENCES Cargos(id)
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