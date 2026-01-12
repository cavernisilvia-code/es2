<?php

// config.php
// Questo file restituisce un array associativo di configurazione.
// Viene incluso tramite `require` e il suo valore di ritorno (l'array) viene assegnato a una variabile.
// Abilita il controllo stretto dei tipi (utile per evitare errori "silenziosi").
declare(strict_types=1);

// `return [...]` in un file incluso significa: "questa è la configurazione".
return [
    'app_name' => 'AuditCLI',
    'version'  => '0.1.0',

    // logging: 'stderr' oppure 'file'
    'log_channel' => 'file',
    'log_level'   => 'debug',   // debug|info|warn|error
    'log_file'    => __DIR__ . '/audit.log',
    'log_format' => 'text', // text|json

    // parsing CLI
    'strict' => true,
];
