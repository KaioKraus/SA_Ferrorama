# 🚆 SA_Ferrorama

##  Sobre o Projeto

O **SA_Ferrorama** é um sistema web desenvolvido para ajudar no gerenciamento e na visualização de informações relacionadas ao setor ferroviário.

A ideia do projeto é reunir algumas informações importantes em um único sistema, facilitando o acesso e a organização dos dados sobre usuários, trens, rotas, sensores e relatórios.

Durante o desenvolvimento, foram aplicados conhecimentos de desenvolvimento web, criação de interfaces, validação de dados, CRUD e organização de projetos.

---

## Objetivo

O principal objetivo do projeto é criar um sistema simples e organizado que permita:

- Fazer login no sistema;
- Cadastrar usuários;
- Visualizar informações dos trens;
- Consultar rotas;
- Visualizar informações dos sensores;
- Acessar relatórios;
- Ter um painel para facilitar o acesso às funcionalidades.

---

## Tecnologias  que foram utilizadas

Para desenvolver o projeto foram utilizadas as seguintes tecnologias:

- HTML5
- CSS3
- JavaScript
- PHP
- Bootstrap
- Git
- GitHub
- XAMPP

---

##  Funcionalidades

### Login

O sistema possui uma tela de login para permitir o acesso do usuário.

### Cadastro de Usuários

Possui uma tela para realizar o cadastro de novos usuários.

### Dashboard

O dashboard funciona como o painel principal do sistema, permitindo acessar as principais funcionalidades.

### Trens

Permite visualizar as informações relacionadas aos trens cadastrados no sistema.

### Rotas

Possui uma tela para consultar as rotas relacionadas ao sistema ferroviário.

### Sensores

Possui uma área destinada à visualização das informações dos sensores.

### Relatórios

O sistema possui uma tela para visualizar os relatórios disponíveis.

---

## Estrutura do Projeto

```text
SA_Ferrorama/
│
├── assets/
│   ├── img/
│   └── style/
│
├── doc/
│   ├── identidade_visual.md
│   ├── pesquisa_crud.md
│   ├── pesquisa_scrum.md
│   └── pesquisa_xampp.md
│
├── public/
│   ├── dashboard.html
│   ├── tela_cadastro_user.html
│   ├── tela_login.html
│   ├── tela_relatorios.html
│   ├── tela_rotas.html
│   ├── tela_sensores.html
│   └── tela_trens.html
│
├── script/
│   ├── botoes.js
│   ├── cadastro.js
│   ├── scripts.js
│   └── validacao.js
│
├── index.html
├── LICENSE
└── README.md
```

### Pastas e arquivos

**assets/**  
Contém os arquivos utilizados no visual do sistema, como imagens e estilos.

**doc/**  
Contém as pesquisas e documentos utilizados durante o desenvolvimento do projeto.

**public/**  
Contém as principais páginas HTML do sistema.

**script/**  
Contém os arquivos JavaScript responsáveis por algumas funções, botões e validações do sistema.

**index.html**  
É a página inicial do projeto.

**LICENSE**  
Contém as informações sobre a licença do projeto.

**README.md**  
É o arquivo que contém as informações e explicações sobre o projeto.

----

## Como Executar

Para executar o projeto localmente, primeiro é necessário ter um ambiente para rodar os arquivos.

Como o projeto utiliza PHP e XAMPP, o XAMPP pode ser utilizado para executar o sistema.

Depois de colocar o projeto na pasta `htdocs` do XAMPP, basta iniciar o **Apache** e acessar o projeto pelo navegador.

Também é possível visualizar as páginas HTML utilizando o **Live Server** no VS Code.

A tela de login está localizada em:

```text
public/tela_login.html
```

---

## Requisitos Funcionais

| Código | Descrição |
|:------:|-----------|
| RF01 | O sistema deve permitir cadastrar usuário. |
| RF02 | O sistema deve permitir cadastrar trens. |
| RF03 | O sistema deve listar trens. |
| RF04 | O sistema deve permitir pesquisar trens. |
| RF05 | O sistema deve permitir excluir trens. |
| RF06 | O sistema deve permitir realizar logout. |
| RF07 | O sistema deve permitir visualizar detalhes do monitoramento. |
| RF08 | O sistema deve atualizar os dados telemétricos em tempo real, sem necessidade de recarregamento manual da página. |
| RF09 | O sistema deve exibir mapa gráfico da malha ferroviária e desvios em tempo real. |
| RF10 | O sistema deve listar rotas. |
| RF11 | O sistema deve permitir pesquisar rotas. |
| RF12 | O sistema deve permitir visualizar detalhes técnicos das rotas. |
| RF13 | O sistema deve listar usuários cadastrados. |
| RF14 | O sistema deve permitir editar dados de usuários. |
| RF15 | O sistema deve permitir excluir usuários do sistema. |
| RF16 | O sistema deve permitir pesquisar usuários. |
| RF17 | O sistema deve listar sensores. |
| RF18 | O sistema deve permitir cadastrar sensores. |
| RF19 | O sistema deve permitir excluir sensores. |
| RF20 | O sistema deve listar relatórios. |
| RF21 | O sistema deve permitir buscar relatórios. |
| RF22 | O sistema deve permitir gerar relatórios personalizados. |
| RF23 | O sistema deve permitir que usuários cadastrados realizem upload, alteração e remoção da foto de perfil. |
| RF24 | O sistema deve permitir cadastrar rotas. |
| RF25 | O sistema deve permitir realizar login. |
| RF26 | O sistema deve permitir excluir rotas. |
| RF27 | O sistema deve validar o usuário ao realizar o login. |

---

## Requisitos Não Funcionais

RNF01
O sistema deve aceitar apenas imagens nos formatos JPG, JPEG ou PNG.
RNF02
O tamanho máximo permitido para a foto de perfil deve ser de 5 MB.
RNF03
A senha deve possuir no mínimo 8 caracteres.
RNF04
O sistema deve ocultar a senha digitada por padrão, permitindo sua visualização apenas por ação do usuário.
RNF05
O sistema deve manter uma interface responsiva.
RNF06
O sistema deve manter padrão visual consistente entre telas, utilizando o mesmo cabeçalho, menu lateral, cores, etc.

---

## Regras de Negócio

RN01
O usuário deve estar cadastrado para ter acesso ao sistema.
RN02
O administrador deve cadastrar os demais funcionários.
RN03
Todo sensor deve estar vinculado a um trem ou a um trecho da ferrovia.
RN04
Um sensor não pode ser excluído caso possua dados de monitoramento.
RN05
O status operacional do trem deve ser classificado como normal, alerta ou falha.
RN06
Somente usuários com permissão administrativa podem cadastrar, editar ou excluir trens, sensores, rotas, usuários e relatórios.
RN07
O encerramento da sessão deve remover o acesso do usuário autenticado e redirecioná-lo para a tela de login.
RN08
Cada usuário pode possuir apenas uma foto de perfil ativa por vez.
RN09
Apenas o próprio usuário poderá alterar sua senha, exceto em casos de redefinição feita por administrador.

---

## Documentação

Durante o desenvolvimento foram feitas algumas pesquisas e documentos para ajudar na construção do projeto.

Eles estão localizados dentro da pasta `doc/`:

- `identidade_visual.md` → informações sobre a identidade visual do projeto;
- `pesquisa_crud.md` → pesquisa sobre CRUD;
- `pesquisa_scrum.md` → pesquisa sobre a metodologia Scrum;
- `pesquisa_xampp.md` → pesquisa sobre o XAMPP.

---

## Equipe

O projeto foi desenvolvido pela equipe:

- **Isabela de Oliveira** — Chefe de Equipe
- **Henrique Kiesewetter**
- **Kaio Carmelindo Kraus**
- **Davi Francisco Freitas**

---

## Status do Projeto

**Em desenvolvimento**

Qualquer duvida consultar a aba do canban no github do projeto

---

## Licença

Este projeto está sob a licença MIT.

Para mais informações, consulte o arquivo [LICENSE](LICENSE).

---

## Considerações Finais

O desenvolvimento do **SA_Ferrorama** foi uma forma de colocar em prática os conhecimentos aprendidos durante o desenvolvimento de sistemas web.

Durante o projeto, trabalhamos com HTML, CSS, JavaScript, PHP, Bootstrap, Git, GitHub e XAMPP, além de conceitos como CRUD e Scrum.

O objetivo foi criar um sistema organizado e funcional, voltado para o gerenciamento de informações do setor ferroviário.