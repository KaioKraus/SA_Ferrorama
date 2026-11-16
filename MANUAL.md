# CELESTIAL STEEL

## Manual do Administrador

**Data:** 17/09/2026

------------------------------------------------------------------------

## Sumário

1.  [Introdução](#1-introdução)
2.  [Instruções de Uso e Telas do
    Sistema](#2-instruções-de-uso-e-telas-do-sistema)
    -   [2.1. Autenticação (Login)](#21-autenticação-login)
    -   [2.2. Painel Principal /
        Dashboard](#22-painel-principal--dashboard)
    -   [2.3. Tela de Relatórios](#23-tela-de-relatórios)
    -   [2.4. Tela de Usuários](#24-tela-de-usuários)
    -   [2.5. Tela de Rotas](#25-tela-de-rotas)
    -   [2.6. Tela de Sensores](#26-tela-de-sensores)
    -   [2.7. Tela de Trens](#27-tela-de-trens)
    -   [2.8. Encerramento de Sessão
        (Logout)](#28-encerramento-de-sessão-logout)
    -   [2.9. Editar Perfil](#29-editar-perfil)
3.  [Dicas e Boas Práticas](#3-dicas-e-boas-práticas)
4.  [Solução de Problemas (FAQ)](#4-solução-de-problemas-faq)
5.  [Suporte e Contatos](#5-suporte-e-contatos)

------------------------------------------------------------------------

## 1. Introdução

O Celestial Steel é uma plataforma desenvolvida para o monitoramento de
um sistema ferroviário. O sistema processa dados enviados por sensores
IoT instalados em locomotivas e trilhos, permitindo acompanhar
velocidade, consumo de energia, localização e detecção de falhas.

A plataforma apoia a tomada de decisões estratégicas em tempo real,
focando na segurança da via, no desempenho operacional e na manutenção
preditiva da frota.

Este manual atende às pessoas que possuem permissão de administrador no
sistema. O administrador tem acesso a todas as telas e gerencia
usuários, rotas, sensores, trens e relatórios.

## 2. Instruções de Uso e Telas do Sistema

### 2.1. Autenticação (Login)

Na tela inicial, informe o e-mail e a senha cadastrados e selecione
**"Entrar"**.

-   Digite o e-mail utilizado no cadastro.
-   Digite a senha. Ela fica ocultada por padrão.
-   Se necessário, utilize o ícone de visualização para conferir a senha
    digitada.
-   Clique em **"Entrar"**.

O sistema valida as credenciais antes de liberar o acesso. A senha deve
possuir no mínimo 8 caracteres.

### 2.2. Painel Principal / Dashboard

O Dashboard concentra as principais informações de monitoramento em uma
única tela. Ele apresenta indicadores de velocidade, temperatura,
consumo de energia e status, além do mapa gráfico da malha ferroviária.

#### Indicadores

-   **Velocidade:** apresenta a velocidade do trem monitorado.
-   **Temperatura:** apresenta a leitura de temperatura recebida pelo
    sistema.
-   **Consumo de energia:** apresenta o consumo energético monitorado.
-   **Status:** informa a condição operacional apresentada pelo sistema.

#### Mapa do trilho

O mapa gráfico representa a malha ferroviária e seus desvios. A
visualização deve ser utilizada para acompanhar a posição e a situação
operacional representadas pelo sistema.

#### Atualização dos dados

Os dados telemétricos devem ser atualizados em tempo real, sem
necessidade de recarregar manualmente a página. Caso uma informação
pareça desatualizada, verifique a conexão e o status do sistema antes de
repetir ações.

### 2.3. Tela de Relatórios

A área **"Relatórios"** permite consultar registros e gerar relatórios
personalizados a partir de parâmetros definidos pelo usuário.

#### Pesquisar relatórios

-   Acesse **"Relatórios"** no menu lateral.
-   Utilize o campo de pesquisa por ID ou Data.
-   Utilize os filtros de **"Tipo de Falha"**.

#### Gerar um relatório

-   Defina a data de início.
-   Defina a data de fim.
-   Selecione o tipo de relatório.
-   Clique em **"Gerar Relatório"**.

#### Excluir relatório

-   Clique no ícone de lixeira.
-   Confirme a exclusão do relatório.

#### Visualizar relatório

-   Clique no ícone de informação. O sistema abrirá a janela com o mapa
    da rota escolhida.

### 2.4. Tela de Usuários

A seção de usuários permite gerenciar os acessos ao sistema.

#### Pesquisar usuários

-   Acesse **"Usuários"** no menu lateral.
-   Utilize o campo de pesquisa por ID ou Nome.
-   Clique na tag de permissão para filtrar pela permissão desejada.

#### Cadastrar um usuário

-   Na tela de Usuários, clique em **"Adicionar"**.
-   Preencha o formulário de cadastro, como nome, e-mail, senha e perfil
    de acesso (**Administrador** ou **Funcionário**).
-   Defina uma senha com no mínimo 8 caracteres.
-   Clique em **"Salvar"**.

#### Editar um usuário

-   Localize o usuário na lista e clique na opção de editar (botão verde
    com ícone de lápis).
-   Altere os dados necessários.
-   Clique em **"Salvar"**.

#### Excluir um usuário

-   Localize o usuário na lista e utilize a opção de excluir (botão
    vermelho com ícone de lixeira).
-   Confirme a exclusão.

### 2.5. Tela de Rotas

A área **"Rotas"** reúne as rotas cadastradas e apresenta informações de
identificação e horário.

#### Pesquisar rota

-   Acesse **"Rotas"** no menu lateral.
-   Utilize o campo de pesquisa por ID.
-   Utilize os filtros de **"Ponto de Partida"** e **"Destino"**.
-   Clique na tag de Status para filtrar pelo status desejado.

#### Visualizar uma rota

-   Clique no ícone de informação. O sistema abrirá a janela com o mapa
    da rota escolhida.

#### Adicionar uma rota

-   Na tela de Rotas, clique em **"Adicionar"**.
-   Preencha os dados da rota.
-   Clique em **"Salvar"**.

#### Editar uma rota

-   Localize a rota na lista e clique na opção de editar (botão verde
    com ícone de lápis).
-   Altere os dados necessários.
-   Clique em **"Salvar"**.

#### Excluir uma rota

-   Localize a rota e clique na opção de excluir (botão vermelho com
    ícone de lixeira).
-   Confirme a exclusão.

### 2.6. Tela de Sensores

A área **"Sensores"** permite consultar os sensores cadastrados e suas
informações de localização e tipo de dado monitorado.

#### Consultar sensores

-   Acesse **"Sensores"**.
-   Utilize o campo de pesquisa por ID ou Nome do sensor.
-   Utilize os filtros de **"Localização"** e **"Tipo de Dado"**.

#### Adicionar um sensor

-   Na tela de Sensores, clique em **"Adicionar"**.
-   Informe os dados do sensor, como nome, localização e tipo de dado.
-   Clique em **"Salvar"**.

#### Editar um sensor

-   Localize o sensor e clique na opção de editar (botão verde com ícone
    de lápis).
-   Altere os dados.
-   Clique em **"Salvar"**.

Um sensor só pode ser excluído se não houver dados registrados pelo
mesmo.

#### Excluir um sensor

-   Localize o sensor e clique na opção de excluir (botão vermelho com
    ícone de lixeira).
-   Confirme a exclusão.

### 2.7. Tela de Trens

A área **"Trens"** permite consultar os trens cadastrados e, conforme a
permissão do usuário, adicionar, editar ou excluir registros.

#### Pesquisar um trem

-   Acesse **"Trens"** no menu lateral.
-   Digite o ID ou o nome do trem no campo de busca.

#### Visualizar trem

-   Clique no ícone de informação. O sistema abrirá a janela com o mapa
    da rota escolhida.

#### Adicionar um trem

-   Na tela de Trens, clique em **"Adicionar"**.
-   Informe os dados do trem.
-   Clique em **"Salvar"**.

#### Editar um trem

-   No cartão do trem, clique na opção de editar (botão verde com ícone
    de lápis).
-   Altere os dados necessários.
-   Clique em **"Salvar"**.

#### Excluir um trem

-   No cartão do trem, clique na opção de excluir (botão vermelho com
    ícone de lixeira).
-   Confirme a exclusão.

### 2.8. Encerramento de Sessão (Logout)

#### Encerrar sessão

O documento original apresenta os seguintes passos nesta seção:

-   Clique em **"Sair"** no menu lateral esquerdo.
-   Insira uma nova senha.
-   Insira-a novamente para confirmar a alteração.
-   Clique em **"Salvar"**.

> **Observação:** os passos sobre inserir e confirmar uma nova senha
> parecem corresponder à edição de senha, não ao encerramento de sessão.
> O conteúdo foi mantido conforme o PDF original e sinalizado para
> revisão.

### 2.9. Editar Perfil

#### Editar senha

-   Clique em **"Editar"** no menu lateral esquerdo.
-   Insira uma nova senha.
-   Insira-a novamente para confirmar a alteração.
-   Clique em **"Salvar"**.

## 3. Dicas e Boas Práticas

### 3.1. Cuidados no uso

-   Mantenha seu acesso individual e não compartilhe sua senha.
-   Utilize os filtros e campos de pesquisa para localizar registros com
    mais rapidez.
-   Ao acompanhar a operação, observe o status do trem e os dados de
    monitoramento em conjunto.
-   Trate exclusões de relatórios, rotas, sensores, trens e usuários
    como definitivas e confira o registro antes de confirmar.
-   Ao finalizar o uso, encerre a sessão pelo logout.

### 3.2. Recomendações para uso seguro

-   Use uma senha com pelo menos 8 caracteres.
-   Não deixe a sessão aberta em computadores compartilhados.
-   Conceda o perfil de administrador apenas a quem precisa dele.
-   Em caso de suspeita de acesso indevido, comunique o responsável pelo
    sistema.

## 4. Solução de Problemas (FAQ)

**Não consigo entrar no sistema. O que devo verificar?**

Confira o e-mail e a senha digitados. Verifique também se o usuário está
cadastrado e se as credenciais foram informadas corretamente.

**Os dados do Dashboard não mudaram. O que devo fazer?**

O sistema foi especificado para atualizar os dados telemétricos em tempo
real. Verifique a conexão e, se o problema continuar, encaminhe a
ocorrência ao suporte.

**Não vejo o menu "Usuários" ou os botões de adicionar, editar e
excluir. O que fazer?**

Confirme se o seu usuário está com o perfil de administrador. Saia do
sistema e entre novamente. Se o problema continuar, contate o suporte.

**Um sensor aparece como inativo. O que fazer?**

Abra a edição do sensor e confira o status e a localização. Se ele
deveria estar ativo e continua inativo, encaminhe a ocorrência ao
suporte.

**Não consigo excluir um sensor. O que devo verificar?**

Se um sensor não pode ser excluído, é devido ao fato de que ele ainda
está ativo e recebendo dados. Desative-o ou pare o envio de dados para
poder excluir.

**Posso alterar a senha de outro usuário?**

Não diretamente. A regra determina que somente o próprio usuário pode
alterar sua senha, exceto em uma redefinição realizada por
administrador.

## 5. Suporte e Contatos

Em caso de dúvidas operacionais ou falhas técnicas do sistema Celestial
Steel:

-   **E-mail:** suporte@celestialsteel.com.br
-   **Telefone:** (47) 4002-8922
