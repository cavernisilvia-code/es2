# PHP CLI Example Application

This repository contains a **minimal PHP command-line application** designed for educational purposes.
It demonstrates how to structure a small PHP project, separate configuration and logic, and run scripts from the terminal.

The project intentionally avoids frameworks, databases, and frontend technologies to keep the focus on **core PHP concepts**.

---

## Requirements

- PHP 7.4 or higher (tested with PHP 8.x)
- Command-line access (Windows, Linux, macOS)

Verify PHP installation:

```bash
php -v
```

---

## Project Structure

```text
.
├── app.php        # Application entry point
├── config.php     # Configuration values
├── lib.php        # Helper functions
└── README.md
```

---

## How to Run

Clone the repository and move into the project directory:

```bash
git clone <repository-url>
cd <repository-directory>
```

Run the application using the PHP CLI:

```bash
php app.php
```

---

## Example Output

A typical execution produces output similar to:

```text
Avvio applicazione...
Configurazione caricata correttamente.
Risultato dell'elaborazione: 42
Fine esecuzione.
```

(The exact output depends on the values defined in `config.php`.)

---

## Configuration

The file `config.php` contains configuration constants used by the application.

Example:

```php
define('BASE_VALUE', 10);
```

Changing configuration values will affect the final output when running `app.php`.

---

## Educational Purpose

This repository is intended to be used for:

- learning basic PHP syntax and execution flow;
- understanding file inclusion (`require_once`);
- practicing small, controlled code changes;
- collaborating via Git (branches, merges, conflict resolution).

It is well suited for beginner courses and workshops.

---

## License

This project is provided for educational use.  
No warranty is expressed or implied.
