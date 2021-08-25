<?php

declare(strict_types=1);

namespace PunktDe\DotenvAdapter;

/**
 * (c) 2021 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 * All rights reserved.
 */

use Helhum\DotEnvConnector\DotEnvVars;
use Symfony\Component\Dotenv\Dotenv;

class DotenvAdapter implements DotEnvVars
{
    const ENV_DIR = 'ENV_DIR';
    const ENV_FILE = 'ENV_FILE';

    public function exposeToEnvironment(string $envFile): void
    {
        $dotEnv = new Dotenv();

        // putenv() sets environment variables for the current process, therefore it can't be used thread-safe
        $dotEnv->usePutenv(!ZEND_THREAD_SAFE);

        // $ENV_DIR defaults to the directory of the env file specified in composer.json
        $envDir = (string)($_SERVER[self::ENV_DIR] ?? $_ENV[self::ENV_DIR] ?? dirname($envFile));

        // Variables will have precedence in the following order (first one wins):
        // 1. Environment variables
        // 2. Variables from files in $ENV_FILE
        // -> Multiple files can be specified using colon (":") as separator
        // -> Last one wins if a variable is set in two files
        // -> Last one wins if a variable is set twice in a file
        // 3. Variables from file in composer.json extra.helhum/dotenv-connector.env-file
        $envFiles = (string)($_SERVER[self::ENV_FILE] ?? $_ENV[self::ENV_FILE] ?? '');
        $envFiles = array_reverse(explode(PATH_SEPARATOR, $envFiles));
        $envFiles[] = $envFile;

        foreach ($envFiles as $envFile) {
            if (empty($envFile)) {
                continue;
            }

            // $ENV_FILE is relative to $ENV_DIR
            if (strpos($envFile, DIRECTORY_SEPARATOR) !== 0) {
                $envFile = $envDir . DIRECTORY_SEPARATOR . $envFile;
            }

            if (!is_readable($envFile)) {
                continue;
            }

            $dotEnv->load($envFile);

            // Reset Symfony loaded dotenv vars
            // We need to do this or Symfony will override previously set dotenv vars
            $_SERVER['SYMFONY_DOTENV_VARS'] = $_ENV['SYMFONY_DOTENV_VARS'] = '';
        }
    }
}
