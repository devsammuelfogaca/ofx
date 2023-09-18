# Ofx Library Test

[![Source Code](http://img.shields.io/badge/source-fogacasammuel/ofx-blue.svg?style=flat-square)](https://github.com/fogacasammuel/ofx)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/fogacasammuel/ofx.svg?style=flat-square)](https://packagist.org/packages/fogacasammuel/ofx)
[![Latest Version](https://img.shields.io/github/release/fogacasammuel/ofx.svg?style=flat-square)](https://github.com/fogacasammuel/ofx/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build](https://img.shields.io/scrutinizer/build/g/fogacasammuel/ofx.svg?style=flat-square)](https://scrutinizer-ci.com/g/fogacasammuel/ofx)
[![Quality Score](https://img.shields.io/scrutinizer/g/fogacasammuel/ofx.svg?style=flat-square)](https://scrutinizer-ci.com/g/fogacasammuel/ofx)
[![Total Downloads](https://img.shields.io/packagist/dt/fogacasammuel/ofx.svg?style=flat-square)](https://packagist.org/packages/cfogacasammuel/ofx)

###### Ofx is a component responsible for reading OFX files, generally used in bank account statements.

Ofx é um componente responsável por fazer a leitura de arquivos OFX, geralmente usados em extratos de contas bancárias.

Você pode saber mais **[clicando aqui](https://github.com/fogacasammuel/ofx)**.

### Highlights

- Simple installation (Instalação simples)
- Composer ready and PSR-2 compliant (Pronto para o composer e compatível com PSR-2)

## Installation

ofx is available via Composer:

```bash
"fogacasammuel/ofx": "^1.0"
```

or run

```bash
composer require fogacasammuel/ofx
```

## Documentation

###### For details on how to use, see a sample folder in the component directory. In it you will have an example of use for each class. It works like this:

Para mais detalhes sobre como usar, veja uma pasta de exemplo no diretório do componente. Nela terá um exemplo de uso para cada classe. Ele funciona assim:

#### User endpoint:

```php
<?php

require __DIR__ . "../../vendor/autoload.php";

use FogacaSammuel\Ofx\Ofx;

// Initialize the Class
$ofx = new Ofx(PATH_FILE_OFX);

//Get invoices from file OFX
$invoices = $ofx->invoices();
var_dump($invoices);

//Get data account from file OFX
$account = $ofx->account();
var_dump($account);

//Get balance from account
$balance = $ofx->balance();
var_dump($balance);
```

## Contributing

Please see [CONTRIBUTING](https://github.com/fogacasammuel/ofx/blob/master/CONTRIBUTING.md) for details.

## Support

###### Security: If you discover any security related issues, please email sammuel.fogaca@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para sammuel.fogaca@gmail.com em vez de usar o rastreador de problemas.

Thank you

## Credits
- [Samuel Fogaça](https://github.com/fogacasammuel) (Developer)
- [Anderson Arruda](https://github.com/andmarruda/ofx-php)(Code Base)
- [All Contributors](https://github.com/fogacasammuel/ofx/contributors) (This Rock)

## License

The MIT License (MIT). Please see [License File](https://github.com/fogacasammuel/ofx/blob/master/LICENSE) for more information.