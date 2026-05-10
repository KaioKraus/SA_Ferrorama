# Identidade Visual — SA Ferrorama

## Sistema de Monitoramento Ferroviário em Tempo Real

---

## 1. Introdução

Este documento apresenta a identidade visual do **SA Ferrorama**, um sistema inteligente de monitoramento ferroviário em tempo real. A plataforma utiliza dados coletados por sensores IoT instalados em locomotivas e trilhos, como velocidade, localização, consumo de energia e falhas operacionais, exibindo essas informações em dashboards claros e objetivos.

A proposta visual do projeto foi desenvolvida com base no conceito de **Mobilidade Inteligente**, unindo a força e a tradição do setor ferroviário com a modernidade das tecnologias conectadas. Cada escolha de design foi pensada para ajudar o operador a entender rapidamente as informações da tela e tomar decisões com mais segurança.

> **Protótipo completo no Figma:**  
> [Acesse aqui](https://www.figma.com/design/k22twqvJ4LfpBGfUcLpvKB/Untitled?node-id=0-1&t=FKKv3AUsI9v2gI2L-0)

---

## 2. Logotipo

O logotipo representa, de forma minimalista, um trem de alta velocidade visto de lado. Ele foi construído com linhas curvas e contínuas na cor laranja (`#ED6A1A`), transmitindo movimento, velocidade e dinamismo.

**Elementos do logo:**

- **Silhueta do Trem:** faz referência direta ao setor ferroviário.
- **Linhas entrelaçadas na parte traseira:** representam a conexão entre rotas, dados e sistemas IoT.
- **Cor laranja:** cria destaque visual e funciona muito bem sobre fundos escuros.

**Aplicações:**  
O logotipo aparece na tela de login, logo abaixo do título, e também no canto superior direito do header nas telas do sistema.

---

## 3. Paleta de Cores

A paleta de cores foi organizada em três grupos principais, cada um com uma função específica dentro da interface.

### 3.1 Cores de Destaque

| Cor | HEX | Uso |
|---|---|---|
| 🟠 Laranja | `#ED6A1A` | Botões de ação, como Entrar, Buscar e Sair; item de menu ativo |


### 3.2 Cores Estruturais

| Cor | HEX | Uso |
|---|---|---|
| 🟢 Verde-Escuro (Teal) | `#072A2A` | Header, fundo da tela de login e sombras |
| 🟢 Verde-Petróleo | `#1E4D4D` | Sidebar de navegação lateral |
| ⬜ Verde-Claro | `#CFD8D6` | Backgrounds do sistema |
| ⚪ Branco | `#FFFFFF` | Cards, tabelas, inputs e textos em fundos escuros |


### 3.3 Cores Semânticas

| Cor | Estado | Uso |
|---|---|---|
| 🟢 Verde | Normal / Operacional | Badges de status e permissão de Administrador |
| 🟡 Amarelo | Alerta / Atenção | Manutenção preventiva e permissão de Funcionário |
| 🔴 Vermelho | Falha Crítica | Botões de exclusão, alertas de falha e erros de validação |
| 🟩 Verde Escuro | Informação | Botões de detalhes nos cards |


---

## 4. Escolha de Cores

A paleta foi pensada para transmitir segurança, inovação, controle e sustentabilidade.

- **Verde-Escuro e Verde-Petróleo:**  
  São usados na base da interface, principalmente no header e na sidebar. Essas cores transmitem uma sensação de estabilidade e seriedade, além de formarem a ligação do projeto com tecnologia e sustentabilidade.

- **Laranja:**  
  Funciona como a cor de ação do sistema. Ele aparece em botões, alertas e elementos interativos, ajudando o usuário a identificar rapidamente onde precisa clicar ou prestar atenção. Essa cor transmite energia, ação e dinamismo.

- **Verde-Claro, Branco e Tons Neutros:**  
  São usados nas áreas de conteúdo, tabelas e dashboards. Eles ajudam a deixar a interface mais leve, organizada e fácil de ler, principalmente em situações de uso prolongado.

- **Responsabilidade com a Sustentabilidade:**  
  A predominância dos tons de verde reforça a ideia de mobilidade inteligente, eficiência energética e redução de impactos ambientais.

---

## 5. Tipografia

**Fonte principal:** `Segoe UI`  
A fonte escolhida é uma sans-serif humanista, bastante adequada para interfaces digitais.

- **Boa legibilidade:**  
  A fonte apresenta ótima leitura em telas, inclusive para números, IDs e dados de sensores.

- **Visual profissional:**  
  Mantém uma aparência séria e moderna, sem parecer excessivamente formal.

- **Disponibilidade nativa:**  
  Está presente no Windows, evitando a necessidade de carregar fontes externas.

**Hierarquia visual:**  
Os títulos utilizam o negrito (Bold), como o título “Login”. Labels aparecem normalmente (Regular). O contraste também foi pensado para manter boa leitura: textos brancos sobre fundos escuros e textos escuros sobre fundos claros.

---

## 6. Layout e Estrutura das Telas

### 6.1 Tela de Login

A tela de login apresenta um card centralizado com cantos arredondados sobre um fundo escuro. Ela conta com o título “Login”, o logotipo da marca, campos de e-mail e senha e o botão “Entrar” em laranja.

O campo de senha possui ícone de visibilidade integrado, tornando a experiência mais prática. O layout é direto e sem distrações, focado apenas no acesso do usuário ao sistema.

### 6.2 Dashboard — Cards de Trens

O dashboard utiliza um grid de cards brancos para exibir os trens cadastrados. Cada card apresenta a imagem do trem, seu ID, nome e botões de ação, como informações em verde escuro e exclusão em vermelho.

Na parte superior, há uma barra com campo de busca e o botão “Add+”, facilitando a localização e o cadastro de novos trens.

### 6.3 Monitoramento de Rotas

A tela de monitoramento apresenta uma representação visual da malha ferroviária. Nela, o usuário acompanha dados em tempo real, como:

- **Velocidade** em km/h;
- **Temperatura** em °C;
- **Status** por meio de badges coloridos;
- **Consumo** em kWh.

Os pontos de sensores mudam de cor conforme o estado operacional, permitindo que o operador identifique rapidamente situações normais, alertas ou falhas.

### 6.4 Telas de Cadastro (Modais)

As telas de cadastro aparecem em modais centralizados, com fundo branco e overlay escuro. Essa escolha ajuda a manter o foco do usuário na ação que está sendo realizada.

Os formulários permitem o cadastro de trens, com informações como nome, localização e sensores, além do cadastro de usuários, com nome, CPF, e-mail, matrícula e tipo de permissão.

### 6.5 Tela de Usuários e Rotas

As telas de usuários e rotas utilizam tabelas com linhas alternadas, facilitando a leitura das informações. As permissões aparecem em badges coloridos, sendo verde para Administrador e amarelo para Funcionário.

Cada linha da tabela possui botões de ação, permitindo editar, visualizar ou excluir informações de forma simples e rápida.

---

## 7. Componentes Visuais

| Componente | Estilo |
|---|---|
| **Botão Entrar** | Fundo laranja `#ED6A1A`, texto branco e `border-radius: 50px` |
| **Botão de ícone** | Informação em verde escuro, exclusão em vermelho e edição em verde claro |
| **Cards** | Fundo branco, sombra sutil e cantos arredondados |
| **Modais** | Fundo branco, overlay escuro e botão “X” para fechar |
| **Badges de status** | Elementos arredondados nas cores verde, amarelo e vermelho |

---

## 8. Framework e Tecnologias

O projeto utiliza **Bootstrap 5.3** via CDN como base para a construção da interface. Além disso, conta com um CSS customizado (`style.css`), responsável por ajustar os estilos padrão e aproximar a interface da identidade visual do SA Ferrorama.

Também são utilizados **Bootstrap Icons** para ícones da interface, como o `bi-eye`, usado no campo de senha para controlar a visualização do conteúdo digitado.

A abordagem do layout é **desktop-first**, pensada principalmente para centros de controle e monitores maiores. Ainda assim, a estrutura possui layouts responsivos.