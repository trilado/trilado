<?php

require_once 'crud/config/bootstrap.php';
require_once 'crud/command/AbstractCommand.php';
require_once 'crud/command/SqlCommand.php';
require_once 'crud/command/ControllerCommand.php';
require_once 'crud/command/ViewCommand.php';

if (count($argv) < 3) {
    echo "Use: php trilado [operation] [model].\n";
    exit(1);
}

if ($argv[1] == 'crud') {
    (new ControllerCommand)->run($argv);
    (new ViewCommand)->run($argv);
    (new SqlCommand)->run($argv);
} else if ($argv[1] == 'controller') {
    (new ControllerCommand)->run($argv);
} else if ($argv[1] == 'view') {
    (new ViewCommand)->run($argv);
} else if ($argv[1] == 'database') {
    (new SqlCommand)->run($argv);
} else {
    echo "\033[31m";
    echo "Use: php trilado [operation] [model] [--option].\n";
    echo "Operacoes disponiveis:\n";
    echo "\t crud \t\t Cria o CRUD de um model\n";
    echo "\t controller \t Cria o controller de um model\n";
    echo "\t view \t\t Cria as views de um model\n";
    echo "\t database \t Cria a tabela do banco de dados de um model\n";
    echo "\n";

    echo "Opcoes disponiveis:\n";
    echo "\t --force \t Sobrescreve o arquivo/tabela caso exista\n";
    echo "\033[0m";
    exit(1);
}
