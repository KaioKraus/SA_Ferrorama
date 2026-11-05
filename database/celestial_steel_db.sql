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
    cep VARCHAR(9) NULL,
    logradouro VARCHAR(255) NULL,
    bairro VARCHAR(155) NULL,
    cidade VARCHAR(155) NULL,
    estado VARCHAR(2) NULL,
    senha VARCHAR(255) NULL,
    cargo_id INT NOT NULL,
    FOREIGN KEY (cargo_id) REFERENCES Cargos(id)
);


INSERT INTO usuarios
(nome, telefone, cpf, email, senha, cargo_id)
VALUES
('Administrador', '11999999999', '12345678901', 'admin@teste.com', '$2y$10$XlUDJRdZWNfi6LS3SrqRMOiDHTLkGCrW4FxziHX3A6DMTZejjhnp2', 1),
('Funcionário', '11999999999', '22345678901', 'funcionario@teste.com', '$2y$10$EHrC83tEebabBtyjtIse3ONviOwI6MhU8hJiAKlSDuCkZJ1LXtnYO', 2),
('Mariana Oliveira', '11987654321', '31045678901', 'mariana.oliveira@teste.com', '$2y$10$Du4DJ00zPkC0jsIwBa2kz.GujMsQ2EDujpDnlgHgKbWm1C1/MLKu.', 2),
('Rafael Santos', '11976543210', '31045678902', 'rafael.santos@teste.com', '$2y$10$2dhgZAAQOl6xMzDVq1NunOvQOtQFP1/rKgZcc1ykh0QmKH9U2XjHq', 2),
('Camila Souza', '11965432109', '31045678903', 'camila.souza@teste.com', '$2y$10$ozpkAf64LbIYMexztHjzy.U1TRSTzKw12qEf5fmvjdInfZqTiPhdC', 2),
('Lucas Pereira', '11954321098', '31045678904', 'lucas.pereira@teste.com', '$2y$10$MLEvFICf0A1C6V4X62EVDuT1sZVSYrPQNPrmF/jEwRKIzIhJOof4i', 2),
('Juliana Costa', '11943210987', '31045678905', 'juliana.costa@teste.com', '$2y$10$EOszL/12lUOd8LPMY65hV.Q6jeoro3jlgpqz7IjkkcekhhiH1qTJ2', 2),
('Felipe Almeida', '11932109876', '31045678906', 'felipe.almeida@teste.com', '$2y$10$xwJ43FlzHzTmhzex7ToeRuyPcTt7DagROSS0DC6sAZxmskNT/21/.', 2),
('Beatriz Rodrigues', '11921098765', '31045678907', 'beatriz.rodrigues@teste.com', '$2y$10$Cw1/QGZqzbwNqJ8EVCshQe1M3vGyPfxkGJQspXgmsVCPCrbA1juIy', 2),
('Gabriel Martins', '11910987654', '31045678908', 'gabriel.martins@teste.com', '$2y$10$mndihRFdK1pzh4WXf2lTtuTltuGzR8AhbovDJ4o78bGCAjU7rGBs.', 2),
('Larissa Ferreira', '11898765432', '31045678909', 'larissa.ferreira@teste.com', '$2y$10$jG/5ibGGD/OUzmPGKoXBcuYc.Cm9sqbOlNfrBHm1PSjxwuuuB2A6i', 2),
('Matheus Gomes', '11887654321', '31045678910', 'matheus.gomes@teste.com', '$2y$10$4.w3c0588wlcbwZCWIjuxOz50g7di7TVyHfwdjVNunO61KI78dNwW', 2),
('Amanda Ribeiro', '11876543210', '31045678911', 'amanda.ribeiro@teste.com', '$2y$10$EWoGs8rfNXw0/o7rNTLTxOFmuzrR5P.n3jSpomb4DMiwOe5Q4tJqq', 2),
('Bruno Carvalho', '11865432109', '31045678912', 'bruno.carvalho@teste.com', '$2y$10$wVA8b7.E1..tCkj6Pwtdnu5BGOLZUysQe702Ea4nhWU1HPuLCfjmy', 2),
('Isabela Lima', '11854321098', '31045678913', 'isabela.lima@teste.com', '$2y$10$woUxoAG3ddHzdgri9.Tpk.12aYk3KWnWy34dZbmEOhdd.rVWg1yNe', 2),
('Pedro Araujo', '11843210987', '31045678914', 'pedro.araujo@teste.com', '$2y$10$B/EowHc1CXJak73JZFunxu4fVLUyNg16tYcHOWOYmdX9E4Z6H7TU6', 2),
('Leticia Fernandes', '11832109876', '31045678915', 'leticia.fernandes@teste.com', '$2y$10$aMMrbcPIi0ybi/PZ53hDt.FlueiA9PXo5m54Qx.V0GaAzg8RnKIcu', 2),
('Vinicius Barbosa', '11821098765', '31045678916', 'vinicius.barbosa@teste.com', '$2y$10$iUM/P6xumNgkCaEUzB8DneOpz.WHd6d0yj8uLTlLbgepD22bLrP5y', 2),
('Ana Paula Mendes', '11710987654', '31045678917', 'ana.mendes@teste.com', '$2y$10$neysOitqD2z88/sA9F9vzun6Oja5CvWU6681jMV5xnhmtHyPOUu0q', 2),
('Diego Nascimento', '11798765432', '31045678918', 'diego.nascimento@teste.com', '$2y$10$asIyQfNCIyjC2DqfYFgeo.QCrGjD.TyCnumbKE7qKvUcc.7asP11i', 2),
('Carolina Moreira', '11787654321', '31045678919', 'carolina.moreira@teste.com', '$2y$10$AOsTJoj5GOcisAzfvcpsAekwHMeaE2Idc0qAUKtE7mta89ILChDj6', 2),
('Eduardo Cardoso', '11776543210', '31045678920', 'eduardo.cardoso@teste.com', '$2y$10$9v5bSUSfzxpwJETfCsdBvuHpgivt9O.W0jmaiPuBIq9AH3S9lnynW', 2),
('Patricia Teixeira', '11765432109', '31045678921', 'patricia.teixeira@teste.com', '$2y$10$1SbzKia3XzTxZnUSthZdheyKSHqwbvkxRed/gz2bLnlXnJjzQ/6SO', 2),
('Rodrigo Correia', '11754321098', '31045678922', 'rodrigo.correia@teste.com', '$2y$10$YVNOECLpBQQ.oKoB7ewSCua477v/OiYluHsp3rneWNSRskSEeKaKG', 2),
('Fernanda Lopes', '11743210987', '31045678923', 'fernanda.lopes@teste.com', '$2y$10$Qc7KwkT.6ze/8C4btq0gb.PznJAvHMUnBkgD3nm/9tUT1eXZsFPuC', 2),
('Thiago Batista', '11732109876', '31045678924', 'thiago.batista@teste.com', '$2y$10$4/PC0lB7HiXT5y3Lu2Rz7uzgt.nLgs.KA6ps9MD./dyg7w4FeSFJK', 2),
('Renata Dias', '11721098765', '31045678925', 'renata.dias@teste.com', '$2y$10$4gSKCBZuljWQMIuZeZHGDeEvjWNlws368s7ICcTofAvcwhYHvhuza', 2),
('Gustavo Monteiro', '11610987654', '31045678926', 'gustavo.monteiro@teste.com', '$2y$10$WAmJ.zrRV1sL1LTikMpHaOSeBrpwS2CE.AqI89oC0mewAassjvcN2', 2),
('Bianca Ramos', '11698765432', '31045678927', 'bianca.ramos@teste.com', '$2y$10$fiKbRAPWoexJLxn8NOUOROTnuxRMrXP7i8KpRhR7J9fr7Fn3R8OZO', 2),
('Leonardo Freitas', '11687654321', '31045678928', 'leonardo.freitas@teste.com', '$2y$10$e/oi2cXlWIQ./Vuv/GhHmut0Pa7qKAeOrnXXf6No5oD/tw2dm0TjO', 2),
('Sabrina Machado', '11676543210', '31045678929', 'sabrina.machado@teste.com', '$2y$10$vAzh94W4wUuV7HqXcF4sfu8/ljWS4gB5Xw349r7UTStC/RRwR.bc2', 2),
('Anderson Vieira', '11665432109', '31045678930', 'anderson.vieira@teste.com', '$2y$10$53iyCyheEjLc8TwAnkYWj.SprgE1s4H7.4wUmfhtp9i3pIO9R6Ll2', 2),
('Priscila Moura', '11654321098', '31045678931', 'priscila.moura@teste.com', '$2y$10$uJGIgrNZgS2eqA46K0JWAeQxETtlfWD4tMC7XRiF.IiiyH9.qYORe', 2),
('Henrique Castro', '11643210987', '31045678932', 'henrique.castro@teste.com', '$2y$10$910mX60loxBCCHb9qbgN8enaTNfkCCpQlE7r3r865ZutqmbcclS8S', 2),
('Natalia Pinto', '11632109876', '31045678933', 'natalia.pinto@teste.com', '$2y$10$caiZVYhb.uqvKXuZbkVEZ.JxhI/csec4O9SOCS/9c/hTE/Ubnp7hi', 2),
('Alexandre Cunha', '11621098765', '31045678934', 'alexandre.cunha@teste.com', '$2y$10$o/HBUew5MWJE.zMttdo.JeTo3cBWH8vxGJ2ACA.S/olUClf6rixHy', 2),
('Claudia Peixoto', '11510987654', '31045678935', 'claudia.peixoto@teste.com', '$2y$10$r41.rvc8mmk8fQ82S/41eeUfw/YDRHm4ZnTbiZRSvrxyjJSHq2ote', 2),
('Marcelo Farias', '11598765432', '31045678936', 'marcelo.farias@teste.com', '$2y$10$sVXsbVEJvDnvm/nqdwbX2.s5MoqItGdfSYAi0l1BK2rT8EwCZGg7m', 2),
('Debora Campos', '11587654321', '31045678937', 'debora.campos@teste.com', '$2y$10$07gyt7EcS0B48hiIJFgw2u.IKazVvVAxetWyNmpT2m.TUzVpcAi3m', 2),
('Igor Moretti', '11576543210', '31045678938', 'igor.moretti@teste.com', '$2y$10$IWz6oeOKP.TvnIVq9LfW4.yDCUuFepoza062X2nG5GpBWHrsakfrm', 2);


CREATE TABLE trens (
    trem_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_trem VARCHAR(155) NOT NULL,
    responsavel_id INT NOT NULL,
    velocidade DECIMAL(10,2) NOT NULL DEFAULT 0,
    temperatura DECIMAL(10,2) NOT NULL DEFAULT 0,
    consumo DECIMAL(10,2) NOT NULL DEFAULT 0,
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
    data_inicio DATE NULL,
    data_fim DATE NULL,
    falha_id INT,
    FOREIGN KEY (falha_id) REFERENCES falha(falha_id)
);


INSERT INTO relatorio (data_relatorio, data_inicio, data_fim, falha_id) VALUES
('2026-01-05', '2026-01-01', '2026-01-05', 1),
('2026-01-18', '2026-01-10', '2026-01-18', 2),
('2026-02-03', '2026-01-28', '2026-02-03', 3),
('2026-02-21', '2026-02-15', '2026-02-21', 4),
('2026-03-10', '2026-03-01', '2026-03-10', 5),
('2026-03-25', '2026-03-18', '2026-03-25', 6),
('2026-04-08', '2026-04-02', '2026-04-08', 7),
('2026-04-19', '2026-04-12', '2026-04-19', 8),
('2026-05-02', '2026-04-26', '2026-05-02', 9),
('2026-05-16', '2026-05-09', '2026-05-16', 10);