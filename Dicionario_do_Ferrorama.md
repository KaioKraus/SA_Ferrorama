# PHP — Dicionário do Ferrorama

## A ideia geral (ler primeiro)

O jeito mais fácil de não se perder é lembrar quem faz o quê:

- **JavaScript** → roda no navegador e manda os pedidos.
- **PHP** → roda no servidor, confere os dados e decide o que fazer.
- **MySQL** → é o banco, onde os registros ficam guardados.
- **SQL** → a linguagem pra consultar ou mudar o que está no banco.
- **MySQLi** → a ferramenta que o PHP usa pra conversar com o banco.

Exemplo do cadastro de usuário, passo a passo:

**Formulário → JS envia os dados → PHP valida → MySQL grava → PHP responde em JSON → JS mostra o resultado**

Para analisar um código novo, o caminho é descobrir (1) de onde o dado veio, (2) onde ele é validado, (3) qual consulta usa esse dado e (4) o que volta na resposta. Exemplo do login: o email vem do `$_POST`, passa pelo `filter_var()`, entra no `?` da consulta preparada e localiza a conta usada para conferir a senha.

---

## 1. Consultas preparadas (a parte mais importante)

Esse é o trecho do login (`public/acesso/tela_login.php:17`):

```php
$sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
if ($stmt = mysqli_prepare($conexao, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
```

Linha por linha:

- `$conexao` → a conexão aberta entre o PHP e o banco. Vem do arquivo `infra/conexao.php`.
- `$sql` → o texto da consulta. Aqui ela pede as colunas do usuário cujo e-mail corresponde ao valor informado.
- `?` → espaço reservado para o valor. Aqui vai receber o e-mail.
- `mysqli_prepare($conexao, $sql)` → prepara a consulta. O valor do e-mail é enviado separado do comando SQL, e é isso que protege o sistema.
- `if ($stmt = ...)` → atenção: o `=` guarda o resultado em `$stmt`, não é uma comparação. O `if` entra no bloco quando a preparação devolve uma instrução válida.
- `mysqli_stmt_bind_param($stmt, 's', $email)` → liga a variável `$email` ao primeiro `?`. O `'s'` indica que o valor é texto.
- `mysqli_stmt_execute($stmt)` → executa a consulta com o valor ligado.
- `mysqli_stmt_get_result($stmt)` → pega o conjunto de resultados devolvido pela consulta. Ele pode estar vazio.
- `mysqli_fetch_assoc($result)` → lê uma linha como array com os nomes das colunas. Por isso é possível escrever `$user['nome']` ou `$user['senha']`. Se não houver linha disponível, devolve `null`.
- `mysqli_stmt_close($stmt)` → aparece mais adiante no login e libera os recursos da instrução preparada.

> **A ordem importa!** Se há três `?`, são necessários três valores na mesma ordem. Exemplo: `'sis'` significa texto, inteiro, texto (essa combinação aparece na atualização do hash da senha, no login).

Letras do `bind_param` para memorizar:

- `s` → texto (string)
- `i` → inteiro
- `d` → decimal (ponto flutuante)
- `b` → dados binários

### A versão curta que aparece no CRUD

```php
$resultado = $conexao->execute_query(
    'SELECT cargo_id FROM usuarios WHERE usuario_id = ?',
    [$usuarioId]
);
$usuario = $resultado->fetch_assoc();
```

`execute_query()` existe desde o PHP 8.2 e faz tudo numa chamada só: prepara, liga os valores e executa. A lista `[$usuarioId]` preenche o único `?`.

O login usa a forma com os passos separados, e várias telas reorganizadas usam a forma curta. Nas duas, o valor que o usuário digitou fica separado do texto SQL, e isso ajuda a evitar injeção de SQL.

### Outros termos do MySQLi

- `new mysqli(...)` / `mysqli_connect(...)` → abrem a conexão usando endereço, usuário, senha e nome do banco.
- `connect_error` / `mysqli_connect_errno()` → mostram a mensagem ou o número de um erro de conexão.
- `set_charset('utf8mb4')` → configura a conexão para tratar corretamente acentos, emojis e outros caracteres Unicode.
- `query()` / `mysqli_query()` → executam SQL diretamente. O projeto usa, por exemplo, para buscar a lista de estações, que é uma consulta fixa e sem valor digitado pelo usuário.
- `prepare()`, `bind_param()`, `execute()` → são os mesmos passos do exemplo do login, escritos como métodos de objeto.
- `fetch_all(MYSQLI_ASSOC)` → lê todas as linhas de uma vez. O `MYSQLI_ASSOC` faz cada linha usar os nomes das colunas como chaves.
- `num_rows` → quantidade de linhas devolvidas por um `SELECT`.
- `mysqli_stmt_store_result()` e `mysqli_stmt_num_rows()` → o primeiro guarda o resultado da instrução para consulta; o segundo lê a quantidade de linhas desse resultado.
- `affected_rows` / `mysqli_stmt_affected_rows()` → quantidade de linhas afetadas por uma alteração. Atenção: num `UPDATE`, zero pode significar que o registro já tinha exatamente os mesmos valores, e não necessariamente que houve erro.
- `insert_id` → ID gerado pelo banco na última inserção. O projeto usa para devolver os IDs dos relatórios criados.
- `mysqli_free_result()` → libera os recursos ocupados por um resultado.
- `mysqli_error()` / `mysqli_stmt_error()` → leem a mensagem de erro da conexão ou de uma instrução específica.
- `mysqli_sql_exception` → tipo de exceção que o MySQLi pode lançar quando ocorre um erro. O código usa `catch` para lidar com ela.

---

## 2. Como ler código PHP

### Variáveis, tipos e arrays

- `$usuario`, `$email`, `$id` → variáveis. O `$` indica que é uma variável.
- `string`, `int`, `float`, `bool` → texto, número inteiro, número decimal e verdadeiro/falso.
- `array` → guarda vários valores. `['Ativo', 'Inativo']` é uma lista. `['nome' => 'Trem 100']` é um array associativo (tem uma chave chamada `nome`).
- `$usuario['nome']` → acessa o valor guardado na chave `nome`.
- `$lista[] = $item` → acrescenta um item no fim da lista.

### Símbolos que confundem

- `=` → atribui um valor. Em `$id = 7`, a variável recebe 7.
- `=>` → liga chave e valor nos arrays. Também separa a declaração da expressão nas funções curtas `fn`.
- `===` / `!==` → comparam valor e tipo. `7 === '7'` dá falso, porque um é número e o outro é texto.
- `==` / `!=` → comparam permitindo a conversão de tipo. São mais permissivos, então o melhor é evitar.
- `<`, `>`, `<=` → comparam ordem ou tamanho.
- `!`, `&&`, `||` → negação, "e" e "ou".
- `??` → dá um valor alternativo quando o primeiro está ausente ou é `null`. `$_POST['email'] ?? ''` usa texto vazio se o e-mail não veio.
- `condição ? a : b` → operador ternário. Escolhe `a` quando a condição é verdadeira e `b` quando é falsa. Já a forma `valor ?: alternativa` usa a alternativa quando o valor é avaliado como falso.
- `(int)`, `(float)`, `(string)` → convertem um valor para inteiro, decimal ou texto.
- `.` e `.=` → juntam textos. O `.=` acrescenta o novo trecho ao texto que já está na variável.

### Estruturas

- `if`, `elseif`, `else` → escolhem qual bloco executar conforme as condições.
- `for`, `foreach`, `while` → repetem um bloco. O `foreach` percorre listas e o `while` repete enquanto a condição for verdadeira.
- `break` / `continue` → o `break` encerra a repetição e o `continue` pula para a próxima volta.
- `function`, `return`, `: void` → `function` declara a função, `return` devolve um resultado ou sai dela, e `: void` indica que ela termina sem devolver valor.
- `fn(...) => ...` / `function (...) use (...)` → criam funções curtas ou anônimas. O projeto usa para filtrar listas. O `use` disponibiliza uma variável de fora dentro da função.
- `new`, `->`, `::` → `new` cria um objeto, `->` acessa métodos ou propriedades dele e `::` aparece em chamadas da classe, como `DateTime::createFromFormat()`.
- `&$variavel` → trabalha por referência. Em um `foreach`, permite alterar o item original da lista.
- `<<<SQL` → heredoc. Permite escrever uma consulta longa, em várias linhas, dentro de uma variável PHP.
- `echo` / `<?= ... ?>` → enviam um valor para a resposta. O `<?= ... ?>` é a forma curta do `echo`.
- `include`, `require_once`, `__DIR__` → incluem outro arquivo. O `require_once` exige que ele exista e o carrega uma única vez. O `__DIR__` indica a pasta do arquivo atual e ajuda a montar o caminho.
- `exit` / `die` → encerram a execução. São usados após respostas, redirecionamentos e alguns erros.

### Erros

- `try`, `catch`, `throw` → o `try` executa um bloco que pode lançar uma exceção, o `throw` lança a exceção e o `catch` trata a exceção recebida.
- `RuntimeException`, `getMessage()`, `getCode()` → representam um erro durante a execução e permitem ler a mensagem e o código desse erro.
- `error_log()` → registra informações no log do servidor.
- `@` → suprime alguns avisos da expressão seguinte. Aparece na tentativa de conexão da página inicial.

### Dados que chegam no PHP

- `$_GET` → recebe parâmetros da URL. Em `?id=42`, o 42 fica disponível em `$_GET['id']`.
- `$_POST` → recebe os campos enviados no corpo de um formulário. É de onde o cadastro lê valores como nome, cpf e email.
- `$_SERVER` → reúne informações da requisição. `$_SERVER['REQUEST_METHOD']` informa se foi GET ou POST. O `HTTP_REFERER`, quando presente, indica a página que originou a navegação.
- `$_SESSION` → guarda informações que o servidor consegue recuperar em outras requisições da mesma sessão, como o ID e o cargo da conta autenticada.

---

## 3. Validação, texto e listas

### Segurança e validação

- `htmlspecialchars()` → transforma caracteres especiais em representações que podem ser exibidas como texto. Por exemplo, `<Trem>` vira `&lt;Trem&gt;`. O `ENT_QUOTES` inclui as aspas simples e duplas nessa conversão.
- `filter_var()` → aplica um filtro a um valor que já temos. `FILTER_VALIDATE_EMAIL` verifica o formato do e-mail e `FILTER_VALIDATE_INT` tenta obter um inteiro válido. Se o valor for reprovado, devolve `false`.
- `filter_input()` → lê uma entrada da requisição e já aplica o filtro. `filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)` tenta ler o ID inteiro da URL. Por padrão, devolve `null` se ele estiver ausente e `false` se for inválido.
- `min_range` → opção do filtro de inteiro. Com `min_range => 1`, o ID precisa ser maior que zero.
- `isset()` / `empty()` / `unset()` → o `isset` verifica se existe um valor diferente de `null`. O `empty` também considera vazios valores como `''`, `0` e `'0'`. O `unset` remove a variável ou a chave.
- `is_string()`, `is_array()`, `is_numeric()` → conferem se o valor é texto, array ou número. Atenção: o `is_numeric()` também aceita textos numéricos, como `'42'`.
- `ctype_digit()` → verifica se um texto contém somente dígitos. É usado na validação do trem em relatórios.

### Texto

- `trim()` → tira espaços e algumas quebras de linha do começo e do fim do texto.
- `strlen()` / `mb_strlen()` → o `strlen` conta bytes e o `mb_strlen` conta caracteres na codificação usada. Para nomes com acento, o ideal é usar o `mb_`.
- `substr()` / `stripos()` → o `substr` corta parte de um texto. O `stripos` procura a posição de um trecho ignorando maiúsculas e minúsculas (posição 0 significa que o encontrou no início).
- `strtolower()` / `strtoupper()` → convertem letras para minúsculas ou maiúsculas. O projeto usa em e-mails e na sigla do estado.
- `str_replace()` / `str_repeat()` → substituem trechos literais ou repetem um texto. Aparecem na montagem do PDF.
- `preg_match()`, `preg_match_all()`, `preg_replace()` → usam expressões regulares, que são regras para reconhecer texto. O `preg_match` procura uma ocorrência, o `preg_match_all` conta as ocorrências e o `preg_replace` faz substituições. O padrão `\D` identifica o que não é dígito e ajuda a limpar CPF, CEP e telefone.
- `sprintf()` / `number_format()` → o `sprintf` monta texto seguindo um molde e o `number_format` prepara um número para exibição, com separadores e casas decimais.

### Listas (arrays)

- `in_array()` → verifica se um valor está na lista. Com `true` no terceiro argumento, exige também o mesmo tipo. O projeto usa para aceitar somente os status previstos.
- `array_filter()` / `array_map()` → o `filter` mantém os itens que atendem a uma condição e o `map` transforma cada item.
- `array_column()` / `array_keys()` → extraem os valores de uma coluna ou as chaves de um array.
- `array_merge()` / `array_unique()` → juntam listas ou removem valores repetidos. A tela de rotas usa para reunir os pontos de partida e chegada.
- `array_slice()` / `array_chunk()` → o `slice` pega um pedaço da lista e o `chunk` divide a lista em grupos, como as linhas que cabem em cada página do PDF.
- `sort()` / `ksort()` → o `sort` ordena pelos valores e o `ksort` pelas chaves. O `SORT_NATURAL` aproxima a ordem da leitura humana (Trem 2 antes de Trem 10) e o `SORT_FLAG_CASE` ignora diferenças de maiúsculas e minúsculas nessa ordenação.
- `count()` / `implode()` → o `count` conta itens e o `implode` junta uma lista de textos usando um separador.
- `round()`, `floor()`, `sin()` → arredondam, arredondam para baixo e calculam o seno. Aparecem nos cálculos do painel, inclusive no consumo simulado.

---

## 4. Login, senha e sessão

**Não confundir (costuma ser cobrado em prova):**

- **Autenticação** = confirmar quem é a pessoa. No login, o PHP procura a conta e verifica a senha.
- **Autorização** = decidir o que a pessoa já autenticada pode fazer. O projeto consulta o cargo e reserva as operações administrativas pra esse perfil.

### Senha

- **Hash de senha** → representação calculada a partir da senha. O banco guarda o hash, e não a senha, para permitir a verificação no próximo login.
- `password_hash($senha, PASSWORD_DEFAULT)` → gera o hash. O `PASSWORD_DEFAULT` pede o algoritmo padrão escolhido pelo PHP.
- `password_get_info($hash)` → devolve informações sobre o hash, como o algoritmo reconhecido em `['algo']`. O login usa isso para identificar como a senha armazenada deve ser verificada.
- `password_verify($senha, $hash)` → confere se a senha digitada corresponde ao hash armazenado.
- `password_needs_rehash()` → verifica se um hash reconhecido precisa ser recalculado com o algoritmo ou as configurações atuais.
- `hash_equals()` → compara dois textos de forma resistente a diferenças no tempo de comparação. No projeto, aparece na migração de senhas antigas em texto puro: após um login válido, o PHP gera e grava o hash.

O trecho que identifica a senha antiga e atualiza o hash fica em `public/acesso/tela_login.php:27`.

### Sessão e cookie

- `session_start()` → abre ou recupera a sessão para que o PHP possa ler e gravar `$_SESSION`.
- `session_status()` / `PHP_SESSION_ACTIVE` → verificam se a sessão já está aberta.
- **Cookie** → pequeno dado guardado pelo navegador. O cookie de sessão ajuda o servidor a reconhecer qual sessão pertence àquela requisição.
- `session_name()` / `session_get_cookie_params()` → informam o nome e as configurações do cookie da sessão.
- `ini_get()` / `setcookie()` / `session_destroy()` → consultam uma configuração do PHP, alteram o cookie e descartam a sessão no servidor. O logout usa essas operações para encerrar o acesso.
- **Mensagem de sessão** → aviso como "Usuário excluído com sucesso". Um arquivo grava o aviso em `$_SESSION`, redireciona, e a próxima tela exibe e remove a mensagem.

---

## 5. HTTP, APIs e JSON

**API** = jeito combinado de um programa pedir informação ou ação pra outro. **Endpoint** = o endereço que atende um desses pedidos.

No Ferrorama: `dados_velocidade.php` é um endpoint interno; a Open-Meteo e a BrasilAPI são serviços externos.

### Termos

- **Requisição** → o pedido enviado. Tem um endereço, um método e pode ter parâmetros, cabeçalhos e corpo.
- **Resposta** → o que o servidor devolve. Tem código de status, cabeçalhos e pode ter um corpo com JSON, um arquivo ou outro conteúdo.
- **GET** → método usado para leitura. O projeto usa para buscar a telemetria e consultar relatórios. Os filtros podem viajar na URL.
- **POST** → método que leva os campos no corpo do pedido. O projeto usa para cadastrar, editar e excluir registros.
- **Query string** → a parte depois do `?` na URL. Em `?trem=100&formato=csv`, são dois parâmetros.
- `http_build_query()` → transforma um array PHP em parâmetros de URL. O `PHP_QUERY_RFC3986` usa uma regra padrão de codificação, como `%20` para espaços.
- `header()` → define um cabeçalho HTTP. O `Content-Type` informa o formato da resposta, o `Location` redireciona e o `Content-Disposition` indica o download e o nome do arquivo.
- `Allow` / `Cache-Control` → o `Allow` informa os métodos aceitos. O `Cache-Control` orienta o navegador sobre guardar a resposta (`no-store` pede que ela não seja armazenada).
- `http_response_code()` → define o status HTTP. O status e o corpo são partes diferentes da mesma resposta.

### JSON

- `json_encode()` → transforma um valor PHP em texto JSON. Um array como `['success' => true]` vira `{"success":true}`.
- `json_decode($texto, true)` → lê um texto JSON. O `true` transforma os objetos JSON em arrays associativos no PHP.
- `JSON_UNESCAPED_UNICODE` → mantém caracteres como ç e ã legíveis no texto JSON.
- `JSON_INVALID_UTF8_SUBSTITUTE` → substitui trechos com codificação inválida durante a conversão.
- `JSON_HEX_TAG` → representa `<` e `>` por sequências Unicode. Aparece quando o PHP coloca dados de rotas dentro de um script da página.
- `success`, `message`, `data`, `reload` → nomes escolhidos pelo projeto para indicar resultado, mensagem, dados devolvidos e pedido de recarregamento. Não são uma regra do PHP.

Exemplo de resposta do PHP:

```php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'message' => 'Sensor cadastrado com sucesso.'
], JSON_UNESCAPED_UNICODE);
```

O navegador recebe o texto e usa `resposta.json()` pra acessar `dados.success` e `dados.message`.

### Códigos HTTP que aparecem no projeto

- **200** → o pedido foi atendido. Alguns formulários também usam `success` no JSON para informar o resultado da operação.
- **201** → o servidor criou registros. Usado na geração de relatórios.
- **302** → o servidor está redirecionando para outro endereço.
- **400** → o pedido contém dados inválidos.
- **401** → o pedido exige autenticação. A API de relatórios usa quando a sessão expirou.
- **403** → a conta não tem permissão para aquela ação.
- **404** → o recurso informado não foi encontrado.
- **405** → o método enviado não é aceito pelo endpoint.
- **422** → o servidor entendeu o pedido, mas não conseguiu completar a ação com aqueles dados. O projeto usa quando nenhum registro de falha corresponde aos filtros de geração.
- **500** → ocorreu um erro interno no servidor.
- **502** → o projeto usa quando o serviço meteorológico falha ou devolve dados inválidos.

### Como cada API do Ferrorama funciona

- **Telemetria** → o navegador pede `dados_velocidade.php`. O PHP consulta o banco e devolve as medições em JSON para os gráficos.
- **Clima** → o navegador pede `dados_clima.php`. O PHP consulta a Open-Meteo com as coordenadas de Joinville, lê o JSON recebido e devolve os dados que o painel usa. (`public/dashboard/dados_clima.php:26`)
- **CEP** → aqui é o próprio JavaScript que consulta a BrasilAPI com o CEP e, ao receber a resposta, preenche os campos de endereço. (`script/usuarios.js:77`)
- **Relatórios** → o `gerar_relatorio.php` aceita POST para criar registros e GET para consultar ou baixar resultados. A resposta pode ser JSON, CSV ou PDF, conforme o pedido.

### cURL (no clima)

No PHP, o cURL faz o papel que o `fetch()` faz no navegador:

- `curl_init()` → prepara o pedido.
- `curl_setopt_array()` → configura as opções.
- `curl_exec()` → faz a chamada.
- `curl_error()` → lê o erro de comunicação.
- `curl_getinfo(..., CURLINFO_HTTP_CODE)` → pega o status HTTP recebido.
- `curl_close()` → encerramento usado no arquivo.

Opções que o projeto usa:

- `CURLOPT_RETURNTRANSFER` → a resposta volta como texto.
- `CURLOPT_CONNECTTIMEOUT` e `CURLOPT_TIMEOUT` → limitam a espera.
- `CURLOPT_FOLLOWLOCATION` → controla redirecionamentos.
- `CURLOPT_SSL_VERIFYPEER` e `CURLOPT_SSL_VERIFYHOST` → verificam o certificado e o nome do endereço HTTPS.
- `CURLOPT_USERAGENT` → identifica o cliente.

---

## 6. SQL e banco de dados

### Estrutura

- **Tabela, linha e coluna** → a tabela reúne registros de um assunto, cada linha é um registro e cada coluna é um campo, como `email` ou `nome_trem`.
- `CREATE DATABASE` / `USE` → criam um banco e escolhem qual banco os próximos comandos usarão.
- `CREATE TABLE` / `IF NOT EXISTS` → criam uma tabela. O `IF NOT EXISTS` permite executar a criação mesmo quando ela já pode existir, como na migração de estações.
- `INT`, `VARCHAR(n)`, `TEXT` → inteiro, texto com tamanho máximo e texto que pode ser mais longo.
- `DECIMAL(10,2)` → número com até dez dígitos no total, sendo dois depois da vírgula. Aparece nas medições dos trens.
- `DATE` / `DATETIME` → data e data com horário, respectivamente.
- `NULL` / `NOT NULL` → valor ausente ou exigência de que o campo tenha um valor. `NULL`, zero e texto vazio são valores diferentes.
- `DEFAULT` → valor usado quando a inserção deixa aquele campo de fora.
- `PRIMARY KEY` → chave primária. Identifica uma linha de forma única, como `usuario_id`.
- `AUTO_INCREMENT` → faz o banco gerar o próximo ID automaticamente.
- `UNIQUE` → impede valores repetidos em um campo ou combinação de campos. É usado em CPF e e-mail.
- `FOREIGN KEY` / `REFERENCES` → ligam um registro a outro. Por exemplo, `trens.responsavel_id` aponta para um usuário cadastrado. Se o trem ainda referencia o usuário, o banco pode impedir a exclusão da conta.

Dá pra ver essas relações no script de instalação: `database/celestial_steel_db.sql:15`.

### Operações e filtros

- **CRUD** → as quatro operações sobre registros: Create cria, Read lê, Update atualiza e Delete exclui.
- `INSERT INTO ... VALUES ...` → insere um registro. O `VALUES` fornece os valores das colunas.
- `INSERT IGNORE` → na migração de estações, permite pular os nomes já protegidos pela regra de unicidade.
- `SELECT ... FROM ...` → escolhe quais colunas ler e de qual tabela. O `SELECT *` pede todas as colunas da consulta.
- `UPDATE ... SET ...` → atualiza os campos indicados no `SET`.
- `DELETE FROM ...` → exclui linhas. O `WHERE` determina quais serão atingidas, e sem ele o comando atinge todas as linhas.
- `WHERE` → aplica condições. Em `WHERE usuario_id = ?`, só entra a linha que corresponde ao ID informado.
- `AND`, `OR`, `=`, `<>` → exigem as duas condições, aceitam uma das condições, comparam igualdade e comparam diferença, respectivamente.
- `LIKE` / `%` → procuram um padrão de texto. `LIKE '%trem%'` encontra textos que tenham "trem" em qualquer posição.
- `BETWEEN ... AND ...` → filtra por um intervalo que inclui os dois limites. O projeto usa para buscar relatórios por data.
- `ORDER BY`, `ASC`, `DESC` → ordenam os resultados. `ASC` é crescente e `DESC` é decrescente.
- `LIMIT` → limita a quantidade de linhas. O login pede até um registro com `LIMIT 1`.
- `DISTINCT` → remove valores duplicados do resultado. A tela de relatórios usa para listar os tipos de dados dos sensores.
- `AS` / apelido → define um nome temporário. Em `usuarios u`, `u` é o apelido da tabela. Em `COUNT(*) AS total`, `total` é o nome da coluna devolvida.

### Juntando e agrupando

- `INNER JOIN ... ON ...` → junta tabelas quando existe correspondência na condição do `ON`. Um relatório pode trazer dados da falha, do sensor e do trem na mesma consulta.
- `LEFT JOIN ... ON ...` → mantém as linhas da tabela da esquerda e traz os dados da direita quando existe correspondência.
- `UNION` / `UNION ALL` → juntam resultados de consultas. O `UNION` remove duplicatas e o `UNION ALL` mantém todas as linhas.
- **Subconsulta** → uma consulta dentro de outra. A listagem usa uma subconsulta para reunir informações de rotas por trem.
- `GROUP BY`, `COUNT`, `MIN`, `MAX` → agrupam linhas, contam registros e calculam o menor e o maior valor dentro de um conjunto.
- `CASE WHEN ... THEN ... ELSE ... END` → é a estrutura de decisão do SQL. O projeto usa para calcular o status do trem a partir das rotas.
- `COALESCE(a, b)` → pega o primeiro valor diferente de `NULL`. Em `senha = COALESCE(?, senha)`, quando o PHP envia `NULL`, o banco mantém a senha já armazenada.
- `CAST(... AS CHAR)` / `BINARY` → o `CAST` converte um valor, como um ID numérico em texto para poder usar `LIKE`. O `BINARY` faz uma comparação byte a byte, distinguindo maiúsculas de minúsculas.
- `CURDATE()`, `CURRENT_TIMESTAMP`, `DATE_FORMAT()` → obtêm a data atual, representam a data e a hora atuais e formatam uma data para exibição.
- `SHOW COLUMNS` → lista as colunas de uma tabela. O projeto usa para verificar se um banco antigo já tem os campos de telemetria ou de período do relatório.

### Transações e exclusões

- **Transação** → grupo de alterações tratado como uma unidade. O `begin_transaction()` abre o grupo, o `commit()` confirma as alterações e o `rollback()` desfaz as que ainda não foram confirmadas. O projeto usa para excluir em conjunto os registros ligados a um trem (`public/trens/excluir_trem.php:27`).
- `FOR UPDATE` → bloqueia as linhas selecionadas para alteração por outras transações enquanto a operação está em andamento. Na exclusão de um trem, ajuda a proteger a verificação feita antes de remover os registros.
- **Erro 1451** → indica uma tentativa de excluir ou alterar um registro que ainda é referenciado por uma chave estrangeira. O projeto usa esse código para explicar por que uma conta vinculada a um trem não pôde ser excluída.

---

## 7. JavaScript que conversa com o backend

- `const`, `let`, `var` → declaram variáveis. O `const` mantém a mesma referência. O `let` e o `var` permitem atribuir outro valor, mas têm regras de escopo diferentes.
- `fetch()` → faz um pedido HTTP. Pode chamar um arquivo PHP do projeto ou uma API externa.
- `Promise`, `async`, `await` → uma promessa representa um resultado que chegará depois. O `async` marca uma função assíncrona e o `await` espera o resultado dentro dela enquanto o navegador segue funcionando.
- `.then()`, `.catch()`, `.finally()` → continuam uma operação assíncrona, tratam uma falha e executam uma etapa final, respectivamente.
- `response.ok`, `.json()`, `.text()`, `.blob()` → o `ok` indica status HTTP de 200 a 299. Os outros leem o corpo da resposta como dados JSON, texto ou arquivo. Atenção: um 404 recebido pelo `fetch()` pode chegar normalmente como resposta, por isso o código verifica o `ok`.
- `FormData` / `URLSearchParams` → o `FormData` reúne os campos enviados no formulário e o `URLSearchParams` monta ou lê os parâmetros de uma URL.
- `JSON.parse()` → transforma texto JSON em dados do JavaScript. O projeto usa em trechos que leem primeiro a resposta como texto.
- `addEventListener()`, `Event`, `dispatchEvent()` → escutam, criam e disparam eventos como clique, mudança de campo ou envio do formulário.
- `preventDefault()` / `reportValidity()` → o primeiro impede o comportamento padrão do evento e o segundo pede que o navegador mostre a validação dos campos.
- `querySelector()`, `querySelectorAll()`, `getElementById()`, `closest()` → encontram elementos da tela. O `closest()` procura o próprio elemento ou um elemento que o contém.
- `dataset`, `textContent`, `innerHTML`, `hidden` → leem dados associados aos elementos, manipulam texto, manipulam conteúdo interpretado como marcação (HTML) e controlam a visibilidade.
- `createElement()`, `appendChild()`, `replaceChildren()`, `remove()` → criam, acrescentam, substituem ou removem elementos. A listagem de relatórios usa essas operações para montar as linhas recebidas.
- `map()`, `filter()`, `find()`, `some()`, `forEach()` → transformam uma lista, filtram, acham o primeiro item, verificam se algum item atende ao teste e percorrem a lista.
- `push()`, `pop()`, `shift()`, `slice()` → acrescentam ao fim, retiram do fim, retiram do começo e copiam parte de uma lista.
- `normalize()`, `replace()`, `includes()`, `startsWith()` → padronizam caracteres, substituem texto, verificam se um trecho existe e se o texto começa de uma determinada forma.
- `setTimeout()`, `clearTimeout()`, `setInterval()` → agendam uma execução, cancelam um agendamento e repetem a execução em intervalos. O painel usa atualização periódica.
- `Map` → guarda valores por chave. No gráfico, ajuda a localizar os trens por ID.
- `URL.createObjectURL()` / `URL.revokeObjectURL()` → criam e liberam um endereço temporário para um arquivo, como o CSV que o navegador vai baixar.
- **Chart.js / Bootstrap** → o Chart.js desenha os gráficos. O JavaScript do Bootstrap abre e fecha os modais usados em confirmações, formulários e detalhes.

---

## 8. Datas, arquivos e exportação

- `date()` / `time()` → o `date` formata data e hora e o `time` devolve o horário atual como número de segundos desde uma data de referência.
- `strtotime()` → tenta converter um texto com data ou horário em um valor que o PHP possa calcular ou formatar.
- `date_default_timezone_set()` → define o fuso horário usado pelas funções de data do PHP.
- `DateTime::createFromFormat()` / `format()` → o primeiro lê uma data seguindo um molde, como `Y-m-d`, e o segundo devolve a data no formato escolhido, como `d/m/Y`.
- `filemtime()` → lê quando um arquivo foi modificado. O projeto inclui esse valor na URL de alguns scripts para que o navegador reconheça a versão atualizada.
- **CSV** → arquivo de texto organizado em linhas e colunas. No projeto, os campos são separados por `;`.
- `fopen()`, `fwrite()`, `fputcsv()`, `fclose()` → abrem um destino de escrita, escrevem conteúdo, escrevem uma linha CSV e fecham o destino.
- `php://output` → destino especial que manda o conteúdo direto para a resposta. A exportação usa para transmitir o CSV ao navegador.
- **BOM UTF-8** → marcador no começo do CSV que ajuda programas de planilha a reconhecer a codificação e exibir os acentos corretamente.
- `iconv()` → converte texto de uma codificação para outra. O PDF muda de UTF-8 para Windows-1252 ao preparar o texto.
- `protegerCelulaCsv()` → função criada no projeto para que textos que começam com símbolos de fórmula sejam tratados como texto pela planilha.
- `gerarPdfRelatorio()` / `baixarRelatorios()` → funções do projeto. A primeira monta o PDF e a segunda envia ao navegador o CSV ou PDF solicitado.

---

## Resumo final (para uma consulta rápida)

- O valor digitado pelo usuário nunca vai direto no SQL: usar `?` e `bind_param` (ou `execute_query`).
- `===` compara valor e tipo; `==` não.
- `$_GET` vem da URL, `$_POST` vem do corpo do formulário e `$_SESSION` é a memória do servidor sobre a sessão.
- Autenticação = quem é; autorização = o que pode fazer.
- No banco, a senha fica guardada como hash e é conferida com `password_verify`.
- Códigos 4xx indicam problema no pedido; códigos 5xx indicam problema no servidor.
- O `fetch()` não gera erro em um 404, então é preciso conferir `response.ok`.
- `affected_rows = 0` em um `UPDATE` pode apenas significar que nada mudou.
- Sempre perguntar: de onde vem o dado, onde é validado, qual consulta o usa e o que volta na resposta?
