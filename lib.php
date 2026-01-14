<?php
declare(strict_types=1);

// lib.php
// Raccolta di funzioni "di supporto" (helper) usate dall'app CLI.
// In un progetto reale queste funzioni starebbero spesso in classi o namespace,
// ma per scopi didattici restano semplici funzioni globali.

/** 
 * Carica la configurazione da un file PHP.
 *
 * @param string $path Percorso del file (es. __DIR__ . '/config.php')
 * @return array       Array associativo di configurazione
 *
 * Nota: il file di configurazione deve "ritornare" un array (vedi config.php).
 * Se il file non esiste o non ritorna un array, viene sollevata un'eccezione.
 */
function load_config(string $path): array {
    // Verifica che il file esista sul filesystem
    if (!is_file($path)) {
        throw new RuntimeException("Config not found: {$path}");
    }
    // `require` include il file ed esegue il suo codice.
    // Qui config.php fa `return [...]`, quindi $cfg riceve quell'array.
    $cfg = require $path;
    // Verifica che il file incluso abbia restituito un array
    if (!is_array($cfg)) {
        throw new RuntimeException("Config invalid: {$path}");
    }
    return $cfg;
}

/**
 * Effettua un parsing molto semplice degli argomenti CLI ($argv).
 *
 * @param array $argv  Argomenti passati a PHP (include il nome dello script in posizione 0)
 * @param bool  $strict Se true, argomenti sconosciuti causano un'eccezione
 * @return array       Una coppia: [comando, opzioni]
 *
 * Convenzione: il comando è in $argv[1], le opzioni sono in formato --chiave=valore.
 */
function parse_args(array $argv, bool $strict): array {
    // Sintassi:
    // php app.php audit:ping --user=alice --action=login
    // Se non viene passato alcun comando, mostriamo l'help
    $cmd = $argv[1] ?? 'help';
    $opts = [
        'user'   => null,
        'action' => null,
        'level' => 'info',
    ];

    // Scorriamo tutti gli argomenti a partire da indice 2 (dopo script e comando)
    foreach ($argv as $i => $a) {
        if ($i < 2) continue; // salta $argv[0] (script) e $argv[1] (comando)
        // Riconosciamo le opzioni in base al prefisso
        if (str_starts_with($a, '--user=')) {
            $opts['user'] = substr($a, 7);
        } elseif (str_starts_with($a, '--level=')) {
            $opts['level'] = substr($a, 9);
        } else {
            // Argomento non riconosciuto: in modalità strict blocchiamo l'esecuzione
            if ($strict) {
                throw new InvalidArgumentException("Unknown argument: {$a}");
            }
        }
    }

    return [$cmd, $opts];
}

/**
 * Scrive un evento di log con un formato minimale.
 *
 * @param array  $cfg      Configurazione (canale, livello minimo, file, ...)
 * @param string $level    Livello del log: debug|info|warn|error
 * @param string $message  Messaggio/evento (es. 'audit.event')
 * @param array  $context  Dati aggiuntivi (serializzati in JSON)
 *
 * Il log viene filtrato in base a $cfg['log_level'].
 * Il canale di output può essere STDERR oppure un file.
 */
function log_event(array $cfg, string $level, string $message, array $context = []): void {
    // Mappa di priorità: numeri più alti = log più "importante"
    $levels = ['debug' => 10, 'info' => 20, 'warn' => 30, 'error' => 40];
    $min = $levels[$cfg['log_level']] ?? 20;
    $cur = $levels[$level] ?? 20;

    // Se il livello corrente è sotto la soglia minima, non logghiamo
    if ($cur < $min) return;

    // Timestamp in formato ISO 8601 (es. 2026-01-10T09:00:00+01:00)
    $ts = date('c');
    // Serializziamo il contesto in JSON (stringa) per stamparlo a fine riga
    $ctx = $context ? json_encode($context, JSON_UNESCAPED_SLASHES) : '';
    $line = "[{$ts}] {$level} {$message} {$cfg["log_format"]} {$ctx}\n";

    // Scegliamo dove scrivere il log: file oppure STDERR
    if (($cfg['log_channel'] ?? 'stderr') === 'file') {
        file_put_contents($cfg['log_file'], $line, FILE_APPEND);
    } else {
            // Argomento non riconosciuto: in modalità strict blocchiamo l'esecuzione
        fwrite(STDERR, $line);
    }
}
