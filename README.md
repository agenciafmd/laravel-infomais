## Laravel - Infomais
[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/laravel-infomais.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/laravel-infomais)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Envia as conversões para a Infomais

## Instalação

```bash
composer require agenciafmd/laravel-infomais:dev-master
```

## Configuração

Para que a integração seja realizada, precisamos da **endpoint e o assunto (identificador da integração)**


Colocamos endpoint no nosso .env

```dotenv
INFOMAIS_INTEGRATION_URL=https://infomais.xxxxxx.com.br
```

Para que funcione, é preciso publicar o arquivo de configuração e colocar o assunto (identificador da integração) na variavel sources

```bash
php artisan vendor:publish --tag=laravel-infomais:configs
```

Por padrão, as configurações do pacote são:

```php
<?php

return [
    'integration_url' => env('INFOMAIS_INTEGRATION_URL',''),
    'sources' => [
        'contato' => 'FORM CONTATO',
    ],
];
```
No caso de ter vários formulários para integração utilizamos um array com o identificador do formulario do site como chave e o identificador da integração como valor


## Uso

Envie os campos no formato de array para o SendConversionsToInfomais.

**Campos obrigatórios**

**assunto**     -   Identificador do ponto de conversão

**nome**	-   Nome

**email**      -   E-mail

**telefone**   -    Telefone

**cpf**        -  CPF

**estado**        -  Estado

**complemento**        -  Complemento




**Retorno do endpoint**

sucesso - Retorna 200. Indica se houve sucesso.

erro  -  campo erro false ou true. Indica se houve erro.


```php

RESPONSE: 200 - {"Erro":false,"MsgErro":"Dados Recebido"}

```

Para que o processo funcione pelos **jobs**, é preciso passar os valores conforme mostrado abaixo.

```php
use Agenciafmd\Infomais\Jobs\SendConversionsToInfomais;

        $sources = config('laravel-infomais.sources');

        $data = [
            "nome" => $data['name'],
            "email" => $data['email'],
            "cpf" => $data['cpf'],
            "telefone" => $data['phone'],
            "estado" => $data['state'],
            "assunto" => $sources['contato'],
            "complemento" => '**Nome:** ' . $data["name"] .
                            ' **E-mail:** ' . $data["email"] .
                            ' **CPF:** ' . $data["cpf"] .
                            ' **Telefone:** ' . $data["phone"] .
                            ' **Assunto:** ' . $sources['contato'] .
                            ' **Cidade:** ' . $data["city"] . ' - ' . $data["state"],
        ];

        SendConversionsToInfomais::dispatch($data)
                ->delay(5)
                ->onQueue('low');
```

Note que no nosso exemplo, enviamos o job para a fila **low**.

Certifique-se de estar rodando no seu queue:work esteja semelhante ao abaixo.

```shell
php artisan queue:work --tries=3 --delay=5 --timeout=60 --queue=high,default,low
```
