# PHP SIMPLES API

Um esqueleto para construir uma api oauth2 de forma rápida e simples, sem muita configuração ou necessidade de programar.

## Como começar

Faça o download ou clone o projeto, na pasta onde estão os arquivos você vai encontrar o database.sql basta importar no mysql e depois configurar as credencias no arquivo database.ini na pasta config.

Ainda na pasta config existe outro arquivo de configuração oauth2.ini para configurar o grant_type (https://bshaffer.github.io/oauth2-server-php-docs/overview/grant-types/), use client_credentials ou password.

Feito essas configurações inicias podemos começar a criar nossos primeiros resources, criei um simples cliente em php para auxiliar na criação dos models e controllers para utilizá-lo vá até a pasta do projeto e execute o seguinte comando:

```
php cli.php -r Pessoa -c varchar:45:nome,varchar:15:cidade,int:3:idade
```

Para utilizar relacionamento entre tabelas utilize um dos paramêtros:

```
-u (Um para Um)
-a (Um para Muitos)
-m (Muitos para Muitos)
```

Exemplo:

```
php cli.php -r Endereco -c varchar:45:rua,varchar:15:cidade,int:3:numero -a Pessoa
```

Isso irá criar um rota nova para consultar no caso do exemplo os endereços de uma pessoa, ficando algo parecido com isso:

```
http://localhost/minhaapi/pessoa/1/endereco -> GET (Busca todos os endereços da pessoa com id 1)
```

Esse comando irá criar o controller, o model e caso a tabela não exista no banco de dados será criada. Explicando um pouco melhor o que o comando faz no primeiro paramêtro informamos o nome do resource utilizando o -r nesse exemplo Pessoa e utilizando o -c vamos dizer quais campos teremos que nesse exemplo são NOME VARCHAR(45), CIDADE VARCHAR(15) E IDADE INT(11).

Pronto, feito isso você já pode utilizar sua api e consultar, cadastrar, editar ou deletar pessoas.
Se estiver trabalhando localhost sua url ficará mais ou menos assim:

```
http://localhost/minhaapi/pessoa -> GET (Consulta todas as pessoas)
http://localhost/minhaapi/pessoa/1 -> GET (Busca a pessoa com id 1)
http://localhost/minhaapi/pessoa/1 -> DELETE (Remove a pessoa com id 1)
http://localhost/minhaapi/pessoa/1 -> PUT (Atualiza a pessoa com id 1, o restante dos paramêtros devem ser passados pelo Content-Type:application/x-www-form-urlencoded)
http://localhost/minhaapi/pessoa -> POST (Cria uma nova pessoa, os paramêtros devem ser passados pelo Content-Type:application/x-www-form-urlencoded)
```

### Validação

Baseado nos tipos dos campos criamos uma validação que já é aplicada automaticamente.
Você pode adicionar outras validações no campo utilizando os comentários do mysql, basta colocar o tipo da validação e se for mais de uma separe por virgula.

Por exemplo um campo VARCHAR que também é um EMAIL no caso a api irá validar apenas se o conteúdo é VARCHAR, mas se você colocar nos comentários do campo a validação "email" ele passará também a checar esse tipo.

A lista de validação você encontra aqui: https://github.com/vlucas/valitron

### Rotas

As rotas da api também são criadas automaticamente, mas caso precise implementar alguma melhoria ou ter mais controle sobre isso você deve consultar a documentação da biblioteca de rotas que utilizamos, aqui: https://github.com/mrjgreen/phroute

### Active Record

Se você está acostumado a trabalhar com Codeigniter não terá dificuldades para utilizar o active record para fazer operações no banco de dados, segue a documentação: https://www.codeigniter.com/userguide3/database/query_builder.html 