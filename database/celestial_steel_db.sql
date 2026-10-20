CREATE DATABASE celestial_steel_db;
USE celestial_steel_db;

CREATE TABLE Cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT
);

INSERT INTO Cargos (id, nome, descricao) VALUES
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Funcionário', 'Acesso limitado ao sistema');


CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(155) NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    email VARCHAR(155) NOT NULL UNIQUE,
    senha VARCHAR(255) NULL,
    cargo_id INT NOT NULL,
    FOREIGN KEY (cargo_id) REFERENCES Cargos(id)
);


INSERT INTO usuarios
(nome, telefone, cpf, email, senha, cargo_id)
VALUES
('Administrador', '11999999999', '12345678901', 'admin@teste.com', 'adm1n123', 1),
('Funcionário', '11999999999', '22345678901', 'funcionario@teste.com', 'func1234', 2),
('Mariana Oliveira', '11987654321', '31045678901', 'mariana.oliveira@teste.com', 'Teste123', 2),
('Rafael Santos', '11976543210', '31045678902', 'rafael.santos@teste.com', 'Teste123', 2),
('Camila Souza', '11965432109', '31045678903', 'camila.souza@teste.com', 'Teste123', 2),
('Lucas Pereira', '11954321098', '31045678904', 'lucas.pereira@teste.com', 'Teste123', 2),
('Juliana Costa', '11943210987', '31045678905', 'juliana.costa@teste.com', 'Teste123', 2),
('Felipe Almeida', '11932109876', '31045678906', 'felipe.almeida@teste.com', 'Teste123', 2),
('Beatriz Rodrigues', '11921098765', '31045678907', 'beatriz.rodrigues@teste.com', 'Teste123', 2),
('Gabriel Martins', '11910987654', '31045678908', 'gabriel.martins@teste.com', 'Teste123', 2),
('Larissa Ferreira', '11898765432', '31045678909', 'larissa.ferreira@teste.com', 'Teste123', 2),
('Matheus Gomes', '11887654321', '31045678910', 'matheus.gomes@teste.com', 'Teste123', 2),
('Amanda Ribeiro', '11876543210', '31045678911', 'amanda.ribeiro@teste.com', 'Teste123', 2),
('Bruno Carvalho', '11865432109', '31045678912', 'bruno.carvalho@teste.com', 'Teste123', 2),
('Isabela Lima', '11854321098', '31045678913', 'isabela.lima@teste.com', 'Teste123', 2),
('Pedro Araujo', '11843210987', '31045678914', 'pedro.araujo@teste.com', 'Teste123', 2),
('Leticia Fernandes', '11832109876', '31045678915', 'leticia.fernandes@teste.com', 'Teste123', 2),
('Vinicius Barbosa', '11821098765', '31045678916', 'vinicius.barbosa@teste.com', 'Teste123', 2),
('Ana Paula Mendes', '11710987654', '31045678917', 'ana.mendes@teste.com', 'Teste123', 2),
('Diego Nascimento', '11798765432', '31045678918', 'diego.nascimento@teste.com', 'Teste123', 2),
('Carolina Moreira', '11787654321', '31045678919', 'carolina.moreira@teste.com', 'Teste123', 2),
('Eduardo Cardoso', '11776543210', '31045678920', 'eduardo.cardoso@teste.com', 'Teste123', 2),
('Patricia Teixeira', '11765432109', '31045678921', 'patricia.teixeira@teste.com', 'Teste123', 2),
('Rodrigo Correia', '11754321098', '31045678922', 'rodrigo.correia@teste.com', 'Teste123', 2),
('Fernanda Lopes', '11743210987', '31045678923', 'fernanda.lopes@teste.com', 'Teste123', 2),
('Thiago Batista', '11732109876', '31045678924', 'thiago.batista@teste.com', 'Teste123', 2),
('Renata Dias', '11721098765', '31045678925', 'renata.dias@teste.com', 'Teste123', 2),
('Gustavo Monteiro', '11610987654', '31045678926', 'gustavo.monteiro@teste.com', 'Teste123', 2),
('Bianca Ramos', '11698765432', '31045678927', 'bianca.ramos@teste.com', 'Teste123', 2),
('Leonardo Freitas', '11687654321', '31045678928', 'leonardo.freitas@teste.com', 'Teste123', 2),
('Sabrina Machado', '11676543210', '31045678929', 'sabrina.machado@teste.com', 'Teste123', 2),
('Anderson Vieira', '11665432109', '31045678930', 'anderson.vieira@teste.com', 'Teste123', 2),
('Priscila Moura', '11654321098', '31045678931', 'priscila.moura@teste.com', 'Teste123', 2),
('Henrique Castro', '11643210987', '31045678932', 'henrique.castro@teste.com', 'Teste123', 2),
('Natalia Pinto', '11632109876', '31045678933', 'natalia.pinto@teste.com', 'Teste123', 2),
('Alexandre Cunha', '11621098765', '31045678934', 'alexandre.cunha@teste.com', 'Teste123', 2),
('Claudia Peixoto', '11510987654', '31045678935', 'claudia.peixoto@teste.com', 'Teste123', 2),
('Marcelo Farias', '11598765432', '31045678936', 'marcelo.farias@teste.com', 'Teste123', 2),
('Debora Campos', '11587654321', '31045678937', 'debora.campos@teste.com', 'Teste123', 2),
('Igor Moretti', '11576543210', '31045678938', 'igor.moretti@teste.com', 'Teste123', 2);


CREATE TABLE trens (
    trem_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_trem VARCHAR(155) NOT NULL,
    responsavel_id INT NOT NULL,
    FOREIGN KEY (responsavel_id) REFERENCES usuarios(usuario_id)
);


INSERT INTO trens (trem_id, nome_trem, responsavel_id) VALUES
(100, 'Trem Expresso Norte', 1),
(200, 'Trem Noturno Central', 12),
(300, 'Trem Regional Sul', 2),
(400, 'Trem de Carga Leste', 38),
(500, 'Trem Regional Aeroporto', 7),
(600, 'Trem Universitário', 15),
(900, 'Trem Regional Noroeste', 7);


CREATE TABLE estacoes (
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
('Terminal de Carga Porto', 'Região Portuária', 'Ativo'),
('Estação Industrial', 'Distrito Industrial', 'Ativo'),
('Estação Aeroporto', 'Região Aeroportuária', 'Ativo'),
('Estação Universitária', 'Região Universitária', 'Ativo');


CREATE TABLE rotas (
    rota_id INT AUTO_INCREMENT PRIMARY KEY,
    ponto_partida VARCHAR(155) NOT NULL,
    ponto_chegada VARCHAR(155) NOT NULL,
    nome VARCHAR(155) NOT NULL,
    trem_id INT NOT NULL,
    descricao TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'Ativo',
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trem_id) REFERENCES trens(trem_id)
);


INSERT INTO rotas
(ponto_partida, ponto_chegada, nome, trem_id, descricao, status)
VALUES
('Estação Central', 'Estação Norte', 'Rota Norte Expressa', 100,
 'Ligação rápida entre o centro e a região norte.', 'Ativo'),
('Estação Central', 'Estação Sul', 'Rota Sul Regional', 300,
 'Transporte regional para a zona sul.', 'Ativo'),
('Estação Leste', 'Estação Oeste', 'Rota Transversal', 200,
 'Conexão entre as regiões leste e oeste.', 'Ativo'),
('Terminal de Carga Porto', 'Estação Industrial', 'Rota de Carga Industrial', 400,
 'Transporte de mercadorias entre o porto e o distrito industrial.', 'Ativo'),
('Estação Norte', 'Estação Aeroporto', 'Rota Aeroporto Norte', 500,
 'Conexão da região norte com o aeroporto.', 'Ativo'),
('Estação Universitária', 'Estação Central', 'Rota Universitária', 600,
 'Transporte de passageiros entre a universidade e o centro.', 'Ativo');


CREATE TABLE sensores (
    sensor_id INT AUTO_INCREMENT PRIMARY KEY,
    nome_sensor VARCHAR(155) NOT NULL,
    localizacao VARCHAR(155) NOT NULL,
    tipo_dado VARCHAR(155) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Ativo'
);


INSERT INTO sensores
(nome_sensor, localizacao, tipo_dado, status)
VALUES
('Sensor de Temperatura 01', 'Motor do Trem 100', 'Temperatura (°C)', 'Ativo'),
('Sensor de Temperatura 02', 'Motor do Trem 200', 'Temperatura (°C)', 'Ativo'),
('Sensor de Temperatura 03', 'Motor do Trem 300', 'Temperatura (°C)', 'Ativo'),
('Sensor de Temperatura 04', 'Motor do Trem 400', 'Temperatura (°C)', 'Ativo'),
('Sensor de Peso 01', 'Vagão do Trem 100', 'Peso (kg)', 'Ativo'),
('Sensor de Peso 02', 'Vagão do Trem 200', 'Peso (kg)', 'Ativo'),
('Sensor de Peso 03', 'Vagão do Trem 300', 'Peso (kg)', 'Ativo'),
('Sensor de Proximidade 01', 'Frente do Trem 400', 'Distância (cm)', 'Ativo'),
('Sensor de Proximidade 02', 'Frente do Trem 500', 'Distância (cm)', 'Ativo'),
('Sensor de Proximidade 03', 'Frente do Trem 600', 'Distância (cm)', 'Ativo');


CREATE TABLE valor_sensor (
    valor_sensor_id INT AUTO_INCREMENT PRIMARY KEY,
    valor_sensor_ FLOAT,
    sensor_id INT NOT NULL,
    FOREIGN KEY (sensor_id) REFERENCES sensores(sensor_id)
);


INSERT INTO valor_sensor (valor_sensor_, sensor_id) VALUES
(72.5, 1),
(76.2, 2),
(68.4, 3),
(81.3, 4),
(1250.0, 5),
(1380.5, 6),
(970.2, 7),
(150.0, 8),
(85.5, 9),
(230.0, 10);


CREATE TABLE falha (
    falha_id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id INT NOT NULL,
    trem_id INT NOT NULL,
    FOREIGN KEY (sensor_id) REFERENCES sensores(sensor_id),
    FOREIGN KEY (trem_id) REFERENCES trens(trem_id)
);


INSERT INTO falha (sensor_id, trem_id) VALUES
(1, 100),
(2, 200),
(3, 300),
(4, 400),
(5, 100),
(6, 200),
(7, 300),
(8, 400),
(9, 500),
(10, 600);


CREATE TABLE relatorio (
    relatorio_id INT AUTO_INCREMENT PRIMARY KEY,
    data_relatorio DATE NOT NULL,
    falha_id INT,
    FOREIGN KEY (falha_id) REFERENCES falha(falha_id)
);


INSERT INTO relatorio (data_relatorio, falha_id) VALUES
('2026-01-05', 1),
('2026-01-18', 2),
('2026-02-03', 3),
('2026-02-21', 4),
('2026-03-10', 5),
('2026-03-25', 6),
('2026-04-08', 7),
('2026-04-19', 8),
('2026-05-02', 9),
('2026-05-16', 10);