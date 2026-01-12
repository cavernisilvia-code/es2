<?php

// app.php
// Entry point dell'applicazione CLI: è lo script che esegui con `php app.php ...`.
// Gestisce il caricamento della configurazione, il parsing degli argomenti e l'esecuzione dei comandi.
// Abilita il controllo stretto dei tipi per ridurre ambiguità e bug.
declare(strict_types=1);

// Importa le funzioni helper (load_config, parse_args, log_event, ...).
// __DIR__ è la cartella in cui si trova questo file, quindi i percorsi sono relativi al progetto.
require __DIR__ . '/lib.php';


// Usiamo try/catch per intercettare eccezioni (errori) e gestirle in modo controllato.
try {
    // 1) Caricamento configurazione
    $cfg = load_config(__DIR__ . '/config.php');
    // 2) Parsing degli argomenti della riga di comando
    [$cmd, $opts] = parse_args($argv, (bool)$cfg['strict']);


    // 3) Comando di aiuto: stampa una mini guida e termina con exit code 0 (successo).
    if ($cmd === 'help' || $cmd === '--help' || $cmd === '-h') {
        echo $cfg['app_name'] . " v" . $cfg['version'] . "\n";
        echo "Commands:\n";
        echo "  audit:ping                 healthcheck\n";
        echo "  audit:log --user=U --action=A   write an audit log event\n";
        exit(0);
    }


    // 4) Comando di healthcheck: utile per capire se lo script parte correttamente.
    if ($cmd === 'audit:ping') {
        echo "pong\n";
        exit(0);
    }


    // 5) Comando principale: scrive un evento di audit con user e action.
    if ($cmd === 'audit:log') {
        $user = $opts['user'] ?? null;
        $action = $opts['action'] ?? null;

        if (!$user || !$action) {
            throw new InvalidArgumentException("Missing --user or --action");
        }

        log_event($cfg, 'info', 'audit.event', ['user' => $user, 'action' => $action]);
        echo "ok\n";
        exit(0);
    }


    // Se arriviamo qui, il comando non è supportato: generiamo un'eccezione.
    throw new InvalidArgumentException("Unknown command: {$cmd}");

// Qualsiasi eccezione (Throwable) viene catturata qui.
// In caso di errore:
// - logghiamo l'evento (se la config è stata caricata)
// - stampiamo un messaggio su STDERR
// - usciamo con exit code 1 (errore)
} catch (Throwable $e) {
    // in errore loggo e stampo messaggio
    if (isset($cfg) && is_array($cfg)) {
        log_event($cfg, 'error', 'app.error', ['msg' => $e->getMessage()]);
    }
    // STDERR è lo "standard error": separato dallo standard output (STDOUT).
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
