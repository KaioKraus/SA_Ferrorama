RELATÓRIO DE UTILIZAÇÃO DA INTELIGÊNCIA ARTIFICIAL
Projeto: SA Ferrorama – Sistema de Monitoramento Ferroviário Inteligente
1. Ferramentas ou agentes de IA utilizados
Durante o desenvolvimento do SA Ferrorama, a equipe, formada por quatro integrantes, utilizou ferramentas de Inteligência Artificial como apoio nas etapas de planejamento, programação, desenvolvimento e correção do sistema.

A principal ferramenta utilizada foi o ChatGPT, além de outras ferramentas de IA que auxiliaram em diferentes atividades relacionadas ao projeto.

O objetivo foi facilitar o desenvolvimento, esclarecer dúvidas técnicas, encontrar soluções para problemas de programação e melhorar a qualidade das funcionalidades implementadas.
2. Objetivo de cada utilização
As ferramentas de inteligência artificial foram utilizadas com diferentes objetivos durante o desenvolvimento do sistema:
- Auxiliar na criação de códigos PHP, HTML, CSS e JavaScript.
- Ajudar na conexão entre o sistema e o banco de dados MySQL.
- Desenvolver e aprimorar as telas e funcionalidades.
- Corrigir erros encontrados durante a programação.
- Auxiliar na organização da estrutura de arquivos e pastas.
- Melhorar a aparência e a navegação entre as páginas.
- Explicar comandos e funcionalidades utilizados no desenvolvimento.
- Sugerir soluções para problemas identificados durante os testes.
- Auxiliar na integração com a Brasil API para realizar consultas de CEP e localizar informações de endereço automaticamente.
3. Principais instruções ou prompts fornecidos
Durante o projeto, foram utilizados diferentes prompts para orientar a inteligência artificial. Alguns exemplos são:

Prompt 1 – Desenvolvimento de funcionalidades:

"Crie códigos PHP para implementar as funcionalidades do sistema de monitoramento ferroviário, utilizando MySQL para armazenar e consultar as informações."

Prompt 2 – Correção de erros:

"Analise o código PHP enviado, identifique os erros existentes e apresente uma versão corrigida que funcione com a estrutura atual do projeto."

Prompt 3 – Organização das telas:

"Ajude a organizar a interface do sistema, mantendo uma barra lateral fixa com as opções Dashboard, Relatórios, Usuários, Rotas e Sensores."

Prompt 4 – Conexão com o banco de dados:

"Analise o erro de conexão entre PHP e MySQL no XAMPP e explique quais configurações precisam ser verificadas para corrigir o problema."

Prompt 5 – Monitoramento ferroviário:

"Auxilie no desenvolvimento de uma tela de monitoramento ferroviário que apresente informações dos trens cadastrados e permita acompanhar os dados disponíveis no sistema."

Prompt 6 – Consulta de CEP com Brasil API:

"Ajude a integrar a Brasil API ao sistema para permitir a consulta de CEP e o preenchimento automático das informações de endereço, como rua, bairro, cidade e estado."
4. Trechos, arquivos ou funcionalidades produzidos com auxílio da IA
A inteligência artificial contribuiu para o desenvolvimento de diferentes partes do SA Ferrorama, auxiliando tanto na criação de códigos quanto no aperfeiçoamento de funcionalidades existentes.

As principais áreas trabalhadas foram:
- Login: desenvolvimento e correção dos códigos responsáveis pela autenticação e pelo acesso dos usuários.
- Dashboard: organização da tela inicial e apresentação das principais informações do sistema.
- Cadastro de trens: criação e adaptação de formulários para registrar informações dos veículos ferroviários.
- Usuários: auxílio na organização das funcionalidades relacionadas ao cadastro e gerenciamento de usuários.
- Rotas: apoio na estruturação da área destinada às informações das rotas ferroviárias.
- Relatórios: desenvolvimento da estrutura de apresentação e consulta de informações.
- Sensores: organização da área responsável pela visualização e consulta dos dados dos sensores.
- Monitoramento em tempo real: auxílio na estruturação da interface de acompanhamento das informações ferroviárias.
- Consulta de CEP: integração com a Brasil API para consultar CEPs e obter informações de endereço, como rua, bairro, cidade e estado, facilitando o preenchimento dos formulários.
Também houve auxílio na configuração de arquivos de conexão com o banco de dados, consultas SQL e integração entre as páginas do sistema.

Além disso, foi utilizada a Brasil API como serviço externo para a consulta de CEPs. Essa integração permitiu buscar automaticamente informações de endereço a partir do CEP informado pelo usuário, tornando os cadastros mais práticos e reduzindo a necessidade de digitação manual.
5. Alterações realizadas pela equipe no conteúdo gerado
Os códigos e sugestões fornecidos pelas ferramentas de IA passaram por análise e adaptações realizadas pelos quatro integrantes da equipe.

Entre as principais alterações estão:
- Adaptação dos códigos à estrutura de pastas e arquivos do projeto.
- Alteração de variáveis, nomes de arquivos e caminhos de acesso.
- Ajustes no HTML e CSS para manter a identidade visual do sistema.
- Adaptação das consultas SQL às tabelas existentes no banco de dados.
- Correção de configurações relacionadas à conexão com o MySQL.
- Integração dos códigos gerados com funcionalidades já existentes.
- Modificação de elementos das telas para atender aos requisitos definidos para o sistema.
- Ajustes na integração com a Brasil API para adequar a consulta de CEP à estrutura dos formulários do sistema.
A equipe não utilizou as respostas da IA de maneira automática. Foi necessário analisar, adaptar e verificar os códigos para garantir que estivessem de acordo com as necessidades do projeto.
6. Testes executados para verificar o funcionamento e a qualidade do resultado
Os testes do sistema foram realizados utilizando o XAMPP como ambiente de desenvolvimento local, permitindo executar os arquivos PHP e verificar a comunicação com o banco de dados MySQL.

Durante essa etapa, foram verificadas as seguintes funcionalidades:
- Funcionamento da tela de login e acesso ao sistema.
- Carregamento e navegação pelo Dashboard.
- Cadastro e consulta de informações dos trens.
- Funcionamento das áreas de gerenciamento de usuários.
- Acesso às páginas de rotas e relatórios.
- Organização e navegação pela área de sensores.
- Apresentação das informações na tela de monitoramento.
- Comunicação entre os arquivos PHP e o banco de dados.
- Funcionamento da consulta de CEP por meio da Brasil API.
- Preenchimento automático das informações de endereço após a consulta do CEP.
Os testes permitiram identificar problemas de conexão, erros de programação e dificuldades de integração entre algumas funcionalidades. A equipe utilizou essas informações para realizar correções e melhorar o funcionamento do sistema.
7. Problemas identificados e como foram corrigidos
Durante o desenvolvimento do SA Ferrorama, alguns problemas técnicos foram encontrados. A inteligência artificial foi utilizada como apoio na identificação das causas e na busca por soluções.

Problema 1 – Falha na conexão com o MySQL

O sistema apresentou erros ao tentar estabelecer conexão com o banco de dados. A IA auxiliou na análise do arquivo conexao.php, orientando a equipe a verificar se o serviço MySQL estava iniciado no XAMPP e se as configurações de endereço, porta, usuário e senha estavam corretas.

Problema 2 – Tabela de usuários não encontrada

Foi identificado um erro relacionado à tabela usuarios, que não estava sendo localizada corretamente pelo sistema. A IA auxiliou na análise da consulta SQL e na verificação da estrutura do banco de dados.

Problema 3 – Erros nos códigos PHP

Algumas páginas apresentaram falhas durante a execução. Com o auxílio da IA, a equipe conseguiu analisar os códigos, localizar possíveis erros de sintaxe, verificar consultas e realizar adaptações.

Problema 4 – Integração entre as funcionalidades

Durante a implementação das diferentes áreas do sistema, foi necessário ajustar caminhos de arquivos, referências e conexões entre páginas. A IA apresentou sugestões para organizar esses elementos, facilitando a integração.

Problema 5 – Consulta e preenchimento de CEP

Durante a implementação da consulta de CEP, foi necessário integrar o sistema com a Brasil API para obter as informações de endereço. A IA auxiliou na compreensão da integração e na organização do código responsável por realizar a consulta e preencher os campos correspondentes.
8. Reflexão sobre as contribuições e limitações da ferramenta durante o desenvolvimento
A utilização da inteligência artificial foi importante durante o desenvolvimento do SA Ferrorama, pois ajudou a equipe a resolver dúvidas, encontrar soluções para erros e desenvolver funcionalidades com mais agilidade.

O ChatGPT e as demais ferramentas utilizadas contribuíram principalmente na programação, na análise de erros e na organização dos códigos. Outro ponto positivo foi a possibilidade de compreender melhor determinadas tecnologias e aprender novas formas de resolver problemas.

Além disso, a utilização da Brasil API possibilitou trabalhar com a integração de serviços externos, permitindo que o sistema consultasse informações de endereço por meio do CEP. Essa implementação também contribuiu para o aprendizado sobre APIs e comunicação entre sistemas.

Entretanto, a utilização dessas ferramentas também apresentou limitações. Nem todos os códigos fornecidos funcionavam diretamente no projeto, sendo necessário adaptá-los à estrutura existente. Além disso, algumas respostas precisaram ser verificadas e testadas antes de serem aplicadas.

Os testes realizados no XAMPP foram importantes para identificar essas situações e verificar se as alterações realmente funcionavam.

Concluímos que a inteligência artificial foi uma ferramenta de apoio muito útil para o desenvolvimento do projeto, mas não substituiu a participação dos integrantes da equipe. A análise dos códigos, as adaptações, as decisões técnicas e a validação das funcionalidades continuaram sendo responsabilidades da equipe.

Dessa forma, o uso da IA contribuiu não apenas para o desenvolvimento do sistema, mas também para o aprendizado e o aprimoramento dos conhecimentos técnicos dos integrantes.