<?php
/**
 * -------------------------------------------------------------------
 * Plik konfiguracji bazy danych
 * -------------------------------------------------------------------
 * 
 * Plik konfiguracyjny zawierający ustawienia połączenia z bazą danych
 * MySQL dla aplikacji
 */

// Zabezpieczenie przed bezpośrednim dostępem do pliku
if (!defined('API_ACCESS')) {
    http_response_code(403);
    die('Bezpośredni dostęp do tego pliku jest zabroniony.');
}

// Dane połączenia z bazą danych MySQL
define('DB_HOST', 'localhost');           // Adres serwera bazy danych
define('DB_NAME', 'planjazd_db');         // Nazwa bazy danych
define('DB_USER', 'root');                // Nazwa użytkownika
define('DB_PASS', '');                    // Hasło
define('DB_CHARSET', 'utf8mb4');          // KOdowanie

// Opcje PDO dla bezpiecznego połączenia
$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Rzucanie wyjątków przy błędach
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Domyślny tryb pobierania danych jako tablica asocjacyjna
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Wyłączenie emulacji prepared statements
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET // Ustawienie kodowania
];

/**
 * -------------------------------------------------------------------
 * Dodatkowe konfiguracje
 * -------------------------------------------------------------------
 */

// Tryb debugowania (ustaw na false w środowisku produkcyjnym)
define('DEBUG_MODE', true);

// Strefa czasowa dla aplikacji
date_default_timezone_set('Europe/Warsaw');

// Ustawienia raportowania błędów (dla developmentu)
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

/**
 * -------------------------------------------------------------------
 * Funkcja połączenia z bazą danych
 * -------------------------------------------------------------------
 */

/**
 * Tworzy i zwraca obiekt PDO z połączenia z bazą danych MySQL.
 * 
 * @return PDO                 Obiekt połączenia PDO
 * @throws PDOException        W przypadku błędu połączenia z bazą danych
 */
function getDbConnection() {
    static $pdo = null;
    
    // Jeśli połączenie już istnieje, zwróć je (wzorzec Singleton)
    if ($pdo !== null) {
        return $pdo;
    }
    
    global $pdo_options;
    
    try {
        // Tworzenie DSN (Data Source Name) dla MySQL
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        
        // Utworzenie nowego połączenia PDO
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
        
        // Logowanie udanego połączenia (tylko w trybie development)
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("Pomyślnie nawiązano połączenie z bazą danych: " . DB_NAME);
        }
        
        return $pdo;
        
    } catch (PDOException $e) {
        
        // W środowisku produkcyjnym nie ujawniamy szczegółów błędu
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            // Logowanie błędu połączenia (bez ujawniania wrażliwych danych)
            error_log("Błąd połączenia z bazą danych: " . $e->getMessage());
            throw new PDOException("Błąd połączenia z bazą danych: " . $e->getMessage());
        } else {
            throw new PDOException("Nie można nawiązać połączenia z bazą danych. Spróbuj ponownie później.");
        }
    }
}