# Pesquisa: APIs (Application Programming Interfaces)

> Projeto: **SA_Ferrorama** — sistema web de gerenciamento ferroviário (usuários, trens, rotas, sensores e relatórios).
> Neste documento usamos o próprio domínio do projeto (trens, rotas e sensores) para exemplificar os conceitos.

## Sumário

1. [O que é uma API e qual é sua finalidade](#1-o-que-é-uma-api-e-qual-é-sua-finalidade)
2. [Como funciona a comunicação entre sistemas](#2-como-funciona-a-comunicação-entre-sistemas-por-meio-de-apis)
3. [O que são APIs REST](#3-o-que-são-apis-rest)
4. [Métodos HTTP](#4-o-papel-dos-métodos-http)
5. [Requisições e respostas HTTP](#5-requisições-e-respostas-http)
6. [Endpoints](#6-endpoints)
7. [Troca de dados em JSON](#7-troca-de-dados-no-formato-json)
8. [API x sistema web tradicional](#8-diferenças-entre-uma-api-e-um-sistema-web-tradicional)
9. [Consumindo uma API com PHP](#9-como-uma-aplicação-php-pode-consumir-uma-api)
10. [Disponibilizando uma API em PHP](#10-como-uma-api-pode-disponibilizar-informações-para-outros-sistemas)
11. [Autenticação, autorização e segurança](#11-autenticação-autorização-e-segurança)
12. [Exemplos em sistemas empresariais](#12-exemplos-de-utilização-de-apis-em-sistemas-empresariais)
13. [Conclusão](#13-conclusão)
14. [Fontes consultadas](#fontes-consultadas)

---

## 1. O que é uma API e qual é sua finalidade

**API** significa *Application Programming Interface* (Interface de Programação de Aplicações). É um conjunto de regras e contratos que define **como um programa pode pedir algo a outro programa** e **como a resposta será devolvida**, sem que quem pede precise conhecer o código interno de quem responde.

Uma analogia comum é a do garçom em um restaurante: o cliente (aplicação que consome) não entra na cozinha (sistema que fornece os dados). Ele faz o pedido ao garçom (API), que leva à cozinha e traz o prato pronto. O cliente só precisa conhecer o cardápio (a documentação da API).

**Finalidades principais:**

- **Integração:** permitir que sistemas diferentes, escritos em linguagens diferentes, troquem informações.
- **Reaproveitamento:** a mesma regra de negócio serve ao site, ao aplicativo móvel e a sistemas de terceiros.
- **Abstração e proteção:** o consumidor não acessa o banco de dados diretamente, apenas o que a API decide expor.
- **Separação de responsabilidades:** o *front-end* (telas) e o *back-end* (regras e dados) podem evoluir de forma independente.

> **No nosso projeto:** hoje as telas em PHP acessam o banco MySQL diretamente. Uma API permitiria, por exemplo, que os sensores dos trens enviassem leituras ao sistema, ou que um aplicativo móvel consultasse as rotas, sem acesso direto ao banco.

---

## 2. Como funciona a comunicação entre sistemas por meio de APIs

A comunicação segue o modelo **cliente–servidor**:

```
 ┌──────────────┐   1. Requisição (pedido)    ┌──────────────┐     ┌────────────┐
 │   CLIENTE    │ ──────────────────────────▶ │   SERVIDOR   │ ──▶ │   Banco    │
 │ (app, site,  │                              │    (API)     │ ◀── │  de dados  │
 │  sensor...)  │ ◀────────────────────────── │              │     └────────────┘
 └──────────────┘   2. Resposta (resultado)   └──────────────┘
```

Passo a passo:

1. O **cliente** monta uma requisição informando *o que quer* (endereço), *o que quer fazer* (método) e, se necessário, *os dados* (corpo).
2. A requisição viaja pela rede, normalmente usando o protocolo **HTTP/HTTPS**.
3. O **servidor** recebe, valida (autenticação, permissões, dados), executa a regra de negócio e consulta/altera o banco de dados.
4. O servidor devolve uma **resposta** com um **código de status** (deu certo? deu erro?) e, geralmente, um corpo em **JSON**.
5. O cliente interpreta a resposta e age (mostra na tela, grava, tenta de novo etc.).

Cada requisição é **independente**: o servidor não "lembra" da anterior (característica chamada *stateless*). Por isso, informações como a identificação do usuário precisam ir em cada chamada (por exemplo, em um *token*).

---

## 3. O que são APIs REST

**REST** (*Representational State Transfer*) é um **estilo de arquitetura** proposto por Roy Fielding em sua tese de doutorado (2000). Não é um protocolo nem uma tecnologia: é um conjunto de princípios. Uma API que os segue é chamada de **API RESTful**.

Princípios principais:

| Princípio | Significado | Exemplo no projeto |
|---|---|---|
| **Cliente–servidor** | Interface e dados ficam separados | Tela de trens × servidor PHP |
| **Stateless (sem estado)** | Cada requisição carrega tudo o que o servidor precisa | Token enviado em toda chamada |
| **Recursos identificados por URL** | Tudo é um "recurso" com endereço próprio | `/api/trens/5` é o trem de id 5 |
| **Interface uniforme** | Usa os métodos HTTP de forma padronizada | `GET` lê, `POST` cria, `DELETE` remove |
| **Representações** | O recurso é trocado em um formato (geralmente JSON) | `{ "nome_trem": "Expresso Norte" }` |
| **Cache** | Respostas podem indicar se podem ser reaproveitadas | Lista de rotas que muda pouco |

Na prática, uma API REST modela o sistema como **substantivos** (recursos: `trens`, `rotas`, `sensores`) e usa os **verbos HTTP** para as ações sobre eles. Isso conversa diretamente com o CRUD que já estudamos (ver `doc/pesquisa_crud.md`):

| CRUD | Método HTTP |
|---|---|
| Create (criar) | `POST` |
| Read (ler) | `GET` |
| Update (atualizar) | `PUT` / `PATCH` |
| Delete (excluir) | `DELETE` |

---

## 4. O papel dos métodos HTTP

O método diz ao servidor **qual operação** o cliente quer realizar sobre o recurso. Dois conceitos importam:

- **Seguro:** não altera dados no servidor (só consulta).
- **Idempotente:** repetir a mesma requisição várias vezes tem o mesmo efeito de fazê-la uma vez.

| Método | Finalidade | Seguro | Idempotente | Possui corpo |
|---|---|:---:|:---:|:---:|
| `GET` | Buscar/consultar um recurso ou lista | Sim | Sim | Não |
| `POST` | Criar um novo recurso (ou executar uma ação) | Não | **Não** | Sim |
| `PUT` | **Substituir** um recurso inteiro | Não | Sim | Sim |
| `PATCH` | Alterar **parte** de um recurso | Não | Não necessariamente | Sim |
| `DELETE` | Remover um recurso | Não | Sim | Normalmente não |

### Exemplos práticos (recurso `trens`)

Os exemplos usam a tabela `trens` do banco `celestial_steel_db` e um endereço fictício `https://api.ferrorama.exemplo`.

#### `GET` — consultar

Lista todos os trens:

```http
GET /api/trens HTTP/1.1
Host: api.ferrorama.exemplo
Accept: application/json
```

Resposta:

```http
HTTP/1.1 200 OK
Content-Type: application/json

[
  { "trem_id": 1, "nome_trem": "Expresso Norte", "dias_semana": "Seg,Qua,Sex", "horario_partida": "08:00", "horario_chegada": "12:30" },
  { "trem_id": 2, "nome_trem": "Cargueiro Sul",  "dias_semana": "Ter,Qui",     "horario_partida": "14:00", "horario_chegada": "19:45" }
]
```

Busca de um trem específico (`GET /api/trens/1`) devolve um único objeto; com filtro (`GET /api/trens?nome=Expresso`) devolve só os que combinam. É o equivalente à **busca** das nossas telas.

#### `POST` — criar

```http
POST /api/trens HTTP/1.1
Host: api.ferrorama.exemplo
Content-Type: application/json
Authorization: Bearer <token>

{
  "nome_trem": "Litorâneo",
  "dias_semana": "Sab,Dom",
  "horario_partida": "09:15",
  "horario_chegada": "11:50"
}
```

Resposta (criado, com o `id` gerado pelo banco):

```http
HTTP/1.1 201 Created
Location: /api/trens/3
Content-Type: application/json

{ "trem_id": 3, "nome_trem": "Litorâneo", "dias_semana": "Sab,Dom", "horario_partida": "09:15", "horario_chegada": "11:50" }
```

Se enviarmos o mesmo `POST` duas vezes, serão criados **dois** trens — por isso o `POST` não é idempotente.

#### `PUT` — substituir por completo

O cliente envia **todos** os campos; o que faltar é considerado removido/zerado.

```http
PUT /api/trens/3 HTTP/1.1
Content-Type: application/json
Authorization: Bearer <token>

{
  "nome_trem": "Litorâneo Express",
  "dias_semana": "Sab,Dom",
  "horario_partida": "09:00",
  "horario_chegada": "11:30"
}
```

```http
HTTP/1.1 200 OK
Content-Type: application/json

{ "trem_id": 3, "nome_trem": "Litorâneo Express", "dias_semana": "Sab,Dom", "horario_partida": "09:00", "horario_chegada": "11:30" }
```

Repetir esse `PUT` deixa o trem exatamente no mesmo estado: é idempotente.

#### `PATCH` — alterar apenas um campo

```http
PATCH /api/trens/3 HTTP/1.1
Content-Type: application/json
Authorization: Bearer <token>

{ "horario_partida": "09:30" }
```

```http
HTTP/1.1 200 OK
Content-Type: application/json

{ "trem_id": 3, "nome_trem": "Litorâneo Express", "dias_semana": "Sab,Dom", "horario_partida": "09:30", "horario_chegada": "11:30" }
```

Só o horário de partida mudou; os demais campos foram preservados. Na tela de edição, é o que ocorre quando o usuário altera apenas um campo.

#### `DELETE` — excluir

```http
DELETE /api/trens/3 HTTP/1.1
Authorization: Bearer <token>
```

```http
HTTP/1.1 204 No Content
```

O `204` indica sucesso sem corpo. Uma segunda chamada normalmente devolve `404 Not Found`, mas o **estado final** do servidor é o mesmo (o trem não existe), por isso o método é idempotente.

> **PUT × PATCH:** use `PUT` quando enviar o recurso completo e `PATCH` quando enviar só as mudanças.

---

## 5. Requisições e respostas HTTP

### Anatomia de uma requisição

```http
POST /api/sensores HTTP/1.1                  ← linha inicial: método + caminho + versão
Host: api.ferrorama.exemplo                  ┐
Content-Type: application/json               │ cabeçalhos (headers)
Authorization: Bearer eyJhbGciOi...          ┘
                                             ← linha em branco
{ "nome_sensor": "Temp. Eixo 1" }            ← corpo (body), opcional
```

| Parte | Função |
|---|---|
| **Método** | Ação desejada (`GET`, `POST`...) |
| **URL / caminho** | Qual recurso (inclui *query string*, como `?status=Ativo`) |
| **Cabeçalhos** | Metadados: formato dos dados, credenciais, idioma |
| **Corpo** | Os dados enviados (em `POST`, `PUT`, `PATCH`) |

### Anatomia de uma resposta

```http
HTTP/1.1 201 Created                         ← versão + código de status + texto
Content-Type: application/json               ← cabeçalhos
Location: /api/sensores/7

{ "sensor_id": 7, "nome_sensor": "Temp. Eixo 1" }   ← corpo
```

### Códigos de status mais usados

| Faixa | Significado | Códigos comuns |
|---|---|---|
| **2xx** | Sucesso | `200 OK`, `201 Created`, `204 No Content` |
| **3xx** | Redirecionamento | `301 Moved Permanently`, `304 Not Modified` |
| **4xx** | Erro do **cliente** | `400 Bad Request`, `401 Unauthorized`, `403 Forbidden`, `404 Not Found`, `422 Unprocessable Content` |
| **5xx** | Erro do **servidor** | `500 Internal Server Error`, `503 Service Unavailable` |

Exemplo de resposta de erro de validação:

```http
HTTP/1.1 422 Unprocessable Content
Content-Type: application/json

{
  "erro": "dados_invalidos",
  "mensagem": "O horário de chegada deve ser posterior ao de partida.",
  "campos": ["horario_chegada"]
}
```

---

## 6. Endpoints

**Endpoint** é o endereço (URL) específico em que a API oferece um recurso ou operação. É a combinação de **método + URL**.

```
https://api.ferrorama.exemplo/api/trens/5?formato=resumido
└─┬─┘   └──────────┬─────────┘ └──┬──┘└┬┘ └──────┬───────┘
esquema        domínio        base  recurso  parâmetro de consulta
                                      + id
```

Exemplo de conjunto de endpoints do nosso domínio:

| Método | Endpoint | O que faz |
|---|---|---|
| `GET` | `/api/trens` | Lista os trens |
| `GET` | `/api/trens/{id}` | Detalha um trem |
| `POST` | `/api/trens` | Cadastra um trem |
| `PUT` | `/api/trens/{id}` | Substitui os dados de um trem |
| `PATCH` | `/api/trens/{id}` | Altera parcialmente um trem |
| `DELETE` | `/api/trens/{id}` | Exclui um trem |
| `GET` | `/api/rotas/{id}/trens` | Lista trens de uma rota (relação `rotas_trens`) |
| `POST` | `/api/sensores/{id}/leituras` | Registra uma leitura (`valor_sensor`) |

Boas práticas de nomenclatura: usar **substantivos no plural** (`/trens`, não `/buscarTrens`), deixar a ação por conta do método HTTP, usar `kebab-case` ou minúsculas e **versionar** a API (`/api/v1/trens`) para permitir mudanças sem quebrar clientes antigos.

---

## 7. Troca de dados no formato JSON

**JSON** (*JavaScript Object Notation*) é um formato de texto leve para representar dados estruturados. É independente de linguagem: PHP, JavaScript, Java, Python etc. leem e geram JSON. Hoje é o formato mais usado em APIs REST (alternativas: XML, que é mais verboso, e outros).

### Tipos de dados

| Tipo | Exemplo |
|---|---|
| Objeto | `{ "chave": "valor" }` |
| Array (lista) | `[1, 2, 3]` |
| String | `"Expresso Norte"` (sempre com aspas duplas) |
| Número | `42`, `3.14` |
| Booleano | `true`, `false` |
| Nulo | `null` |

### Exemplo completo com objetos aninhados e listas

```json
{
  "rota_id": 10,
  "dias_semana": "Seg,Qua,Sex",
  "horario_partida": "08:00",
  "horario_chegada": "12:30",
  "trens": [
    { "trem_id": 1, "nome_trem": "Expresso Norte" },
    { "trem_id": 4, "nome_trem": "Expresso Leste" }
  ],
  "ativa": true,
  "observacao": null
}
```

### Em PHP

O PHP possui as funções nativas `json_encode()` (array → JSON) e `json_decode()` (JSON → array/objeto):

```php
<?php
$trem = [
    'trem_id'   => 1,
    'nome_trem' => 'Expresso Norte',
    'ativo'     => true,
];

$json = json_encode($trem, JSON_UNESCAPED_UNICODE);
// {"trem_id":1,"nome_trem":"Expresso Norte","ativo":true}

$dados = json_decode($json, true); // true => devolve array associativo
echo $dados['nome_trem'];          // Expresso Norte
```

### Em JavaScript (como já fazemos em `script/trens.js` e `script/usuarios.js`)

```js
const resposta = await fetch('/api/trens/1');
const trem = await resposta.json();   // converte o JSON em objeto
console.log(trem.nome_trem);
```

> Nosso projeto já usa JSON: `cadastrar_trem.php` e `cadastrar_usuario.php` respondem com `header('Content-Type: application/json')` e `json_encode(['success' => true, 'message' => '...'])`, e o JavaScript consome essa resposta com `fetch`. Ou seja, essas partes já se comportam como pequenos *endpoints* de uma API.

---

## 8. Diferenças entre uma API e um sistema web tradicional

| Aspecto | Sistema web tradicional | API |
|---|---|---|
| **Quem consome** | Pessoa, pelo navegador | Outro programa (app, sistema, sensor, script) |
| **O que devolve** | Página **HTML** pronta (com layout) | **Dados** (geralmente JSON/XML), sem layout |
| **Foco** | Apresentação + regra de negócio juntas | Regra de negócio e dados; a apresentação fica por conta do cliente |
| **Navegação** | Links e formulários que recarregam a página | Requisições HTTP programáticas |
| **Autenticação** | Em geral sessão + cookie | Em geral token (Bearer, JWT, chave de API) |
| **Reuso** | Atende só ao site | Atende site, app móvel, integrações e parceiros |
| **Erros** | Página de erro amigável | Código de status + JSON descrevendo o erro |

**No Ferrorama hoje:** `tela_trens.php` mistura consulta ao banco e geração de HTML no mesmo arquivo (modelo tradicional). Numa arquitetura com API, haveria um endpoint `GET /api/trens` devolvendo JSON, e a tela apenas o consumiria via JavaScript.

Os dois modelos não competem: é comum um sistema ter **ambos**, com o site tradicional e uma API para integrações.

---

## 9. Como uma aplicação PHP pode consumir uma API

Consumir uma API é fazer o PHP agir como **cliente** HTTP. As opções mais comuns:

- **cURL** (extensão `curl`): completa e muito usada.
- **`file_get_contents()`** com contexto de *stream*: simples, para casos básicos.
- Bibliotecas como **Guzzle** (via Composer), que facilitam o trabalho.

### Exemplo 1 — `GET` com cURL

```php
<?php
function buscarTrens(string $token): array
{
    $ch = curl_init('https://api.ferrorama.exemplo/api/trens');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,          // devolve o corpo como string
        CURLOPT_TIMEOUT        => 10,            // não ficar esperando para sempre
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ],
        CURLOPT_SSL_VERIFYPEER => true,          // nunca desative a verificação do certificado
    ]);

    $corpo  = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erro   = curl_error($ch);
    curl_close($ch);

    if ($corpo === false) {
        throw new RuntimeException("Falha de rede: $erro");
    }
    if ($status !== 200) {
        throw new RuntimeException("API respondeu com status $status");
    }

    $dados = json_decode($corpo, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException('Resposta não é um JSON válido');
    }

    return $dados;
}

foreach (buscarTrens('MEU_TOKEN') as $trem) {
    echo htmlspecialchars($trem['nome_trem']) . '<br>';   // escapa antes de exibir
}
```

### Exemplo 2 — `POST` enviando JSON

```php
<?php
$payload = [
    'nome_trem'       => 'Litorâneo',
    'dias_semana'     => 'Sab,Dom',
    'horario_partida' => '09:15',
    'horario_chegada' => '11:50',
];

$ch = curl_init('https://api.ferrorama.exemplo/api/trens');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer MEU_TOKEN',
    ],
]);

$resposta = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status === 201) {
    $trem = json_decode($resposta, true);
    echo 'Criado com id ' . (int) $trem['trem_id'];
} else {
    echo 'Erro ' . $status . ': ' . htmlspecialchars((string) $resposta);
}
```

Para `PUT`, `PATCH` e `DELETE` basta usar `CURLOPT_CUSTOMREQUEST => 'PUT'` (ou `'PATCH'`, `'DELETE'`).

### Exemplo 3 — alternativa simples com `file_get_contents`

```php
<?php
$contexto = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'header'  => "Accept: application/json\r\n",
        'timeout' => 10,
    ],
]);

$json = file_get_contents('https://api.ferrorama.exemplo/api/rotas', false, $contexto);
$rotas = $json !== false ? json_decode($json, true) : [];
```

### Cuidados ao consumir

- Sempre definir **timeout** e tratar falhas de rede.
- Conferir o **código de status** antes de usar o corpo.
- Validar o JSON (`json_last_error()`), pois a resposta pode vir malformada.
- **Escapar** dados externos antes de exibir em HTML (`htmlspecialchars`), pois não são confiáveis.
- Guardar chaves e tokens em arquivo de configuração/variável de ambiente, **nunca** no código versionado no GitHub.

---

## 10. Como uma API pode disponibilizar informações para outros sistemas

Para **oferecer** uma API em PHP, criamos scripts que recebem a requisição, consultam o banco e respondem em JSON. Um exemplo mínimo de `GET /api/trens` com **PDO** (ver `doc/pesquisa_PDO.md`) e *prepared statements*:

```php
<?php
// api/trens.php
header('Content-Type: application/json; charset=utf-8');

$pdo = new PDO('mysql:host=127.0.0.1;dbname=celestial_steel_db;charset=utf8mb4', 'usuario', 'senha', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM trens WHERE trem_id = ?');
            $stmt->execute([$id]);
            $trem = $stmt->fetch();

            if (!$trem) {
                http_response_code(404);
                echo json_encode(['erro' => 'Trem não encontrado']);
                break;
            }
            echo json_encode($trem);
        } else {
            echo json_encode($pdo->query('SELECT * FROM trens')->fetchAll());
        }
        break;

    case 'POST':
        $dados = json_decode(file_get_contents('php://input'), true);

        if (empty($dados['nome_trem'])) {
            http_response_code(422);
            echo json_encode(['erro' => 'nome_trem é obrigatório']);
            break;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO trens (nome_trem, dias_semana, horario_partida, horario_chegada) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $dados['nome_trem'],
            $dados['dias_semana'] ?? '',
            $dados['horario_partida'] ?? null,
            $dados['horario_chegada'] ?? null,
        ]);

        http_response_code(201);
        echo json_encode(['trem_id' => (int) $pdo->lastInsertId()]);
        break;

    default:
        http_response_code(405);
        header('Allow: GET, POST');
        echo json_encode(['erro' => 'Método não permitido']);
}
```

Pontos importantes para o servidor:

- Ler o corpo JSON com `php://input` (em requisições JSON o `$_POST` fica vazio).
- Devolver o **status HTTP correto** com `http_response_code()`.
- Usar *prepared statements* para evitar **SQL Injection**.
- Responder sempre com `Content-Type: application/json`, inclusive nos erros.
- **Nunca** devolver campos sensíveis (como o hash da senha de `usuarios`). Selecionar só as colunas necessárias, em vez de `SELECT *`, quando houver dados assim.
- Em projetos maiores, usar um *framework* ou roteador (Laravel, Slim) para organizar as rotas, em vez de um `switch` por arquivo.

**Documentar** a API também faz parte de disponibilizá-la. O padrão mais usado é a **especificação OpenAPI** (antigo Swagger), que descreve endpoints, parâmetros e respostas de forma que pessoas e ferramentas consigam ler.

---

## 11. Autenticação, autorização e segurança

Como a API é um "portão de entrada" para os dados, ela precisa de proteção em camadas.

### Autenticação × Autorização

| Conceito | Pergunta que responde | Exemplo no Ferrorama |
|---|---|---|
| **Autenticação** | *Quem é você?* | Login com e-mail e senha, ou token válido |
| **Autorização** | *O que você pode fazer?* | Administrador cadastra e exclui; funcionário só consulta |

Códigos relacionados: `401 Unauthorized` (não autenticado/credencial inválida) e `403 Forbidden` (autenticado, mas sem permissão).

### Formas comuns de autenticação

- **Chave de API (*API key*):** string secreta enviada em um cabeçalho. Simples, indicada para identificar sistemas.
- **Token Bearer / JWT:** após o login, o servidor emite um token assinado e o cliente o envia em `Authorization: Bearer <token>`.
- **OAuth 2.0:** padrão de autorização delegada (ex.: "Entrar com Google"), em que o usuário permite o acesso sem entregar a senha.
- **Basic Auth:** usuário e senha codificados em Base64 — só aceitável sobre HTTPS e em cenários simples.

### Boas práticas de segurança

1. **Sempre HTTPS**, para que credenciais e dados não trafeguem em texto aberto.
2. **Autorização em cada endpoint, no servidor.** Esconder um botão na tela **não** é segurança: quem conhece a URL consegue chamar o endpoint direto. A verificação de cargo precisa existir em cada ação (criar, editar, excluir).
3. **Princípio do menor privilégio:** cada papel recebe apenas o acesso necessário.
4. **Validar e sanitizar toda entrada** (tipos, tamanhos, formatos) e usar *prepared statements*.
5. **Senhas com hash** (`password_hash()` / `password_verify()` em PHP), nunca em texto puro.
6. **Não expor detalhes internos** em mensagens de erro (nada de erro SQL ou *stack trace* para o cliente).
7. **Limitar a taxa de requisições** (*rate limiting*) contra abuso e força bruta.
8. **Controlar CORS** para dizer quais origens de navegador podem chamar a API.
9. **Proteger segredos:** tokens e chaves fora do repositório, em variáveis de ambiente, e com possibilidade de revogação/expiração.
10. **Registrar logs** de acessos e falhas para auditoria.

O **OWASP API Security Top 10** reúne as falhas mais comuns em APIs; entre as primeiras estão *autorização quebrada em nível de objeto* e *autenticação quebrada* — exatamente o tipo de erro que acontece quando a permissão só é verificada na interface.

> **Lição aprendida no projeto:** ao revisar o controle de acesso do Ferrorama, percebemos que a tela de usuários estava bloqueada para o funcionário, mas os *endpoints* `cadastrar_usuario.php` e `excluir_usuario.php` não conferiam o cargo. Qualquer requisição enviada diretamente ao endpoint contornaria a tela. É o exemplo concreto de por que a autorização precisa estar no servidor.

---

## 12. Exemplos de utilização de APIs em sistemas empresariais

| Área | Uso de API | Benefício |
|---|---|---|
| **Pagamentos** | Loja virtual chama a API de um gateway (ex.: Stripe, Mercado Pago, PagSeguro) para cobrar no cartão ou gerar Pix/boleto | Não precisa lidar diretamente com dados de cartão |
| **Logística** | Sistema de pedidos consulta a API dos Correios ou de transportadoras para calcular frete e rastrear entregas | Informação em tempo real |
| **ERP / contabilidade** | ERP troca dados com sistemas fiscais para emissão de notas fiscais eletrônicas | Evita redigitação e erros |
| **CRM e marketing** | Formulário do site envia contatos à API do CRM; sistema dispara e-mails via API de e-mail | Dados centralizados |
| **Mapas e localização** | Aplicativos usam APIs de mapas (Google Maps, OpenStreetMap) para rotas e geolocalização | Reaproveita serviço especializado |
| **Bancos (Open Finance)** | Instituições compartilham dados com autorização do cliente via APIs padronizadas | Integração segura entre bancos e fintechs |
| **RH** | Sistema de folha integra-se ao ponto eletrônico e ao e-social/benefícios | Automação de processos |
| **Comunicação** | Aplicações enviam notificações por SMS/WhatsApp via API | Atendimento automatizado |
| **IoT / indústria** | Sensores enviam leituras a uma API central, que alimenta *dashboards* e alertas | Monitoramento em tempo real |
| **Microsserviços** | Serviços internos (estoque, pedidos, usuários) conversam entre si por APIs | Evolução e escala independentes |

### Aplicação direta ao SA_Ferrorama

- **Sensores:** equipamentos nos trens enviariam leituras por `POST /api/sensores/{id}/leituras`, alimentando a tabela `valor_sensor` e disparando registros na tabela `falha`.
- **Relatórios:** um sistema de BI poderia consumir `GET /api/relatorios?data=2026-10-21` para gerar gráficos.
- **Aplicativo móvel para maquinistas:** consulta `GET /api/rotas` e `GET /api/trens` sem acessar o banco diretamente.
- **Integração externa:** consulta a uma API de previsão do tempo para alertar sobre condições que afetem rotas.

---

## 13. Conclusão

Uma API é o meio padronizado pelo qual sistemas se comunicam: o cliente envia uma **requisição** HTTP a um **endpoint**, usando o **método** adequado, e o servidor devolve uma **resposta** com **código de status** e dados em **JSON**. O estilo **REST** organiza isso em torno de recursos e dos verbos `GET`, `POST`, `PUT`, `PATCH` e `DELETE`, que correspondem às operações do CRUD que já estudamos.

Em PHP, é possível tanto **consumir** APIs (cURL, `file_get_contents`) quanto **oferecê-las** (recebendo `php://input`, respondendo com `json_encode` e status corretos). Por ser uma porta de entrada para os dados, a API exige **autenticação, autorização verificada no servidor, HTTPS e validação de entrada**.

Aplicar esses conceitos ao Ferrorama mostrou que parte do sistema já usa o modelo (os endpoints que respondem JSON para o `fetch`), e que migrar o restante para uma API traria reuso, integração com sensores e apps, e uma base mais segura para o controle de acesso entre administrador e funcionário.

---

## Fontes consultadas

**Documentação oficial e padrões**

1. MDN Web Docs — *HTTP* (visão geral do protocolo): <https://developer.mozilla.org/pt-BR/docs/Web/HTTP>
2. MDN Web Docs — *Métodos de requisição HTTP*: <https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Methods>
3. MDN Web Docs — *Códigos de status de respostas HTTP*: <https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Status>
4. MDN Web Docs — *Cabeçalhos HTTP*: <https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Headers>
5. MDN Web Docs — *Trabalhando com JSON*: <https://developer.mozilla.org/pt-BR/docs/Learn/JavaScript/Objects/JSON>
6. MDN Web Docs — *Using the Fetch API*: <https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch>
7. IETF — *RFC 9110: HTTP Semantics* (semântica de métodos, status e cabeçalhos): <https://www.rfc-editor.org/rfc/rfc9110>
8. IETF — *RFC 8259: The JSON Data Interchange Format*: <https://www.rfc-editor.org/rfc/rfc8259>
9. IETF — *RFC 6749: The OAuth 2.0 Authorization Framework*: <https://www.rfc-editor.org/rfc/rfc6749>
10. IETF — *RFC 6750: OAuth 2.0 Bearer Token Usage*: <https://www.rfc-editor.org/rfc/rfc6750>
11. Fielding, R. T. — *Architectural Styles and the Design of Network-based Software Architectures* (tese que define o REST), cap. 5: <https://ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm>
12. JSON.org — *Introducing JSON*: <https://www.json.org/json-pt.html>

**PHP**

13. PHP Manual — *cURL*: <https://www.php.net/manual/pt_BR/book.curl.php>
14. PHP Manual — *`json_encode`* e *`json_decode`*: <https://www.php.net/manual/pt_BR/function.json-encode.php> e <https://www.php.net/manual/pt_BR/function.json-decode.php>
15. PHP Manual — *PDO*: <https://www.php.net/manual/pt_BR/book.pdo.php>
16. PHP Manual — *`password_hash`*: <https://www.php.net/manual/pt_BR/function.password-hash.php>
17. PHP Manual — *`http_response_code`*: <https://www.php.net/manual/pt_BR/function.http-response-code.php>

**Segurança e especificação de APIs**

18. OWASP — *API Security Top 10*: <https://owasp.org/API-Security/>
19. OWASP — *REST Security Cheat Sheet*: <https://cheatsheetseries.owasp.org/cheatsheets/REST_Security_Cheat_Sheet.html>
20. OpenAPI Initiative — *OpenAPI Specification*: <https://spec.openapis.org/oas/latest.html>

**Material interno do projeto**

21. `doc/pesquisa_PDO.md` e `doc/pesquisa_crud.md` (pesquisas anteriores da equipe).