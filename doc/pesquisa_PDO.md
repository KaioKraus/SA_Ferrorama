Claro — abaixo está **o código Markdown puro**, para você copiar e colar diretamente em um arquivo `.md`. Não alterei nem excluí o conteúdo.

````
# O que é PDO

O PDO (PHP Data Object) é uma extensão da linguagem PHP para acesso a banco de dados. Totalmente orientado a objetos, ele possui diversos recursos importantes, além de suporte a diversos mecanismos de banco de dados.

# Para que ele é utilizado no PHP

Puxar dados do banco para o programa de forma simples e direta de forma fácil e “universal”. Além disso ele oferece suporte nativo para preteção contra ataques do tipo SQL injection.

# Principais caracteristicas

Simplifica as operações do banco de dados, como:

- Fechamento das conexões do banco de dados
- tratamento de erros
- executar consultas
- criando conexão com o banco de dados

ele também é compatível com a maioria das API’s usadas nos dias de hoje, o'que faz com que o PDO seja um método muito usado e conhecido atualmente. Permite também o controle manual ou automático de transações (commit e rollback) para garantir a integridade dos dados. É escrito em linguagem C e compilado diretamente no PHP, o que garante alta velocidade de execução.

# Como funciona uma conexão utilizando PDO

Para se conectar usando o PDO. Nós vamos criar uma nova instância de classe e especificar o driver que vamos usar, no caso o mysql, o nome do banco de dados, nome de usuário e senha.

```php
<?php $conn = new PDO('mysql:host=localhost;dbname=meuBancoDeDados', $username, $password); ?>
````

 também é muito simples se conectar usando o PDO, mas como em toda conexão, é preciso tratar os erros, para se caso aconteça algum erro na conexão, mostrar isso ao usuário. então usamos os seguintes comandos:

```
<?php
try {
    $conn = new PDO('mysql:host=localhost;dbname=meuBancoDeDados', $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>
```

 # Diferenças entre PDO e MySQLi

 A principal diferença é que o PDO é uma extensão genérica que suporta mais de 12 bancos de dados diferentes e usa o estilo orientado a objetos, enquanto o MySQLi funciona exclusivamente com MySQL e permite programar tanto em formato orientado a objetos quanto procedural. Ambas as ferramentas são seguras contra SQL Injection quando usadas com prepared statements, mas o PDO oferece uma portabilidade de código muito maior caso você precise trocar de banco de dados no futuro, enquanto o MySQLi pode apresentar uma leve vantagem de desempenho por ser otimizado especificamente para o ecossistema MySQL.

 # Vantagens e desvantagens de utilizar PDO

 O PDO destaca-se principalmente por sua portabilidade e por sua segurança nativa, blindando a aplicação contra ataques de SQL Injection por meio de prepared statements. Por outro lado, suas principais desvantagens são a exigência de conhecimento em orientação a objetos (o que afasta desenvolvedores iniciantes acostumados com o modelo procedural) e o fato de não traduzir a sintaxe SQL, o que significa que consultas com funções específicas de um banco de dados ainda precisarão ser reescritas caso o sistema gerenciador seja trocado.

 # O que são Prepared Statements e por que são importantes

 Prepared Statements são modelos de código SQL pré-compilados pelo banco de dados que separam a estrutura da consulta dos dados reais. Eles são fundamentais porque eliminam o risco de SQL Injection (bloqueando ataques maliciosos) e aumentam o desempenho do sistema, já que o banco não precisa reanalisar a mesma query a cada execução.

 # Em quais situações o PDO pode ser uma boa escolha

 O PDO é uma excelente escolha quando você está desenvolvendo aplicações em PHP que interagem com bancos de dados relacionais e precisa de segurança, flexibilidade e código limpo.

 # Fontes

 https://www.treinaweb.com.br/blog/o-que-e-pdo-no-php

 https://blog.grancursosonline.com.br/php-pdo-vs-mysqli/

 https://www.php.net/manual/pt\_BR/book.pdo.php

 https://pt.stackoverflow.com/questions/99620/qual-a-diferen%C3%A7a-entre-o-statement-e-o-preparedstatement

 https://www.guj.com.br/t/o-que-e-preparedstatement-e-para-que-serve/86774/

 https://www.devmedia.com.br/php-pdo-como-se-conectar-ao-banco-de-dados/37211

 https://www.php.net/manual/pt\_BR/book.pdo.php

 https://www.treinaweb.com.br/blog/o-que-e-pdo-no-php

 https://www.ibm.com/docs/pt-br/db2/11.5.x?topic=php-application-development-pdo

 https://www.geeksforgeeks.org/php/what-is-pdo-in-php/

```

```