# dotenv-adapter

Dynamically decide which .env files to load.

## Install

```bash
composer require punktde/dotenv-adapter ^1.0.0
composer config extra.helhum/dotenv-connector.env-file /usr/local/etc/app.env
composer config extra.helhum/dotenv-connector.adapter 'PunktDe\DotenvAdapter\DotenvAdapter'
```

## Usage

```bash
# echo FOO=foo > foo.env && echo FOO=bar > bar.env
# ENV_FILE=/tmp/foo.env:/tmp/bar.env php print_foo.php
bar
# FOO=baz ENV_FILE=/tmp/foo.env:/tmp/bar.env php print_foo.php
baz
```
