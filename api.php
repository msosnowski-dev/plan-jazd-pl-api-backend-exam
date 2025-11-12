<?php
/**
 * -------------------------------------------------------------------
 * Plik przetwarzania żadań API
 * -------------------------------------------------------------------
 * 
 * Główny plik API obsługujący żądania AJAX z frontendu aplikacji
 * do zarządzania harmonogramem jazd. Implementuje operacje CRUD
 * z walidacją po stronie serwera.
 * 
 * OBSŁUGIWANE ENDPOINTY:
 * - GET  ?action=get     - Pobieranie listy wszystkich jazd
 * - POST ?action=add     - Dodawanie nowej jazdy (JSON w body)
 * - DELETE ?action=delete&id={id} - Usuwanie jazdy po ID
 */

// Definicja stałej dostępu do API (zabezpieczenie config.php)
define('API_ACCESS', true);

// Włączenie konfiguracji bazy danych
require_once 'config.php';

// Ustawienie nagłówków HTTP dla API JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');


/**
 * -------------------------------------------------------------------
 * Klasa API
 * -------------------------------------------------------------------
 */

/**
 * Klasa DriveAPI obsługuje operacje związane z harmonogramem jazd.
 *
 */
class DriveAPI {
    
    /**
     * @var PDO Połączenie z bazą danych
     */
    private $pdo;
    
    /**
     * @var array Tablica błędów walidacji
     */
    private $errors = [];
    
    /**
     * Konstruktor klasy - inicjalizuje połączenie z bazą danych.
     * 
     * @throws Exception W przypadku błędu połączenia z bazą danych
     */
    public function __construct() {
        try {
            $this->pdo = getDbConnection();
        } catch (PDOException $e) {
            $this->sendErrorResponse('Błąd połączenia z bazą danych', 500);
        }
    }
    
    /**
     * -------------------------------------------------------------------
     * Główna metoda przetwarzania żadań
     * -------------------------------------------------------------------
     */
    
    /**
     * Główna metoda obsługująca żądania API.
     * 
     * Analizuje parametr 'action' i kieruje żądanie do odpowiedniej metody.
     * Obsługuje GET (pobieranie), POST (dodawanie) i DELETE (usuwanie).
     * 
     * @return void
     */
    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($action) {
                case 'get':
                    if ($method !== 'GET') {
                        $this->sendErrorResponse('Metoda GET wymagana dla akcji get', 405);
                    }
                    $this->getDrives();
                    break;
                    
                case 'add':
                    if ($method !== 'POST') {
                        $this->sendErrorResponse('Metoda POST wymagana dla akcji add', 405);
                    }
                    $this->addDrive();
                    break;
                    
                case 'delete':
                    if ($method !== 'DELETE') {
                        $this->sendErrorResponse('Metoda DELETE wymagana dla akcji delete', 405);
                    }
                    $this->deleteDrive();
                    break;
                    
                default:
                    $this->sendErrorResponse('Nieznana akcja API. Dostępne: get, add, delete', 400);
            }
        } catch (Exception $e) {
            error_log("Błąd API: " . $e->getMessage());
            $this->sendErrorResponse('Wystąpił nieoczekiwany błąd serwera', 500);
        }
    }
    
    /**
     * -------------------------------------------------------------------
     * Metody obsługi żądań
     * -------------------------------------------------------------------
     */
    
    /**
     * Zwraca listę wszystkich jazd.
     * 
     * Sortuje jazdy według daty i godziny (najwcześniejsze pierwsze) lubpuste array 
     * @return void Wysyła odpowiedź JSON
     */
    private function getDrives() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, date, time, instructor, student, created_at 
                FROM drives 
                ORDER BY date ASC, time ASC
            ");
            
            $stmt->execute();
            $drives = $stmt->fetchAll();
            
            // Dla GET zwracamy bezpośrednio tablicę
            echo json_encode($drives, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit();
            
        } catch (PDOException $e) {
            error_log("Błąd pobierania jazd: " . $e->getMessage());
            $this->sendErrorResponse('Nie udało się pobrać listy jazd', 500);
        }
    }
    
    /**
     * Dodaje nową jazdę do bazy danych.
     * 
     * Dane JSON przechodza walidację, a następnie są zapisywane.
     * 
     * @return void Wysyła odpowiedź JSON z danymi dodanej jazdy lub błędami
     */
    private function addDrive() {
        // Pobranie i dekodowanie danych JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendErrorResponse('Nieprawidłowy format JSON', 400);
        }
        
        // Walidacja danych wejściowych
        if (!$this->validateDriveData($data)) {
            $this->sendErrorResponse(
                'Błędy walidacji: ' . implode(', ', $this->errors), 
                422
            );
        }
        
        try {
            // Sprawdzenie czy nie ma konfliktu czasowego
            if ($this->hasTimeConflict($data['date'], $data['time'], $data['instructor'])) {
                $this->sendErrorResponse(
                    'Instruktor ma już zaplanowaną jazdę w tym terminie', 
                    409
                );
            }
            
            // Wstawienie nowej jazdy do bazy
            $stmt = $this->pdo->prepare("
                INSERT INTO drives (date, time, instructor, student) 
                VALUES (:date, :time, :instructor, :student)
            ");
            
            $stmt->execute([
                ':date' => $data['date'],
                ':time' => $data['time'],
                ':instructor' => trim($data['instructor']),
                ':student' => trim($data['student'])
            ]);
            
            // Pobranie ID nowo dodanej jazdy
            $newId = $this->pdo->lastInsertId();
            
            // Pobranie pełnych danych dodanej jazdy
            $stmt = $this->pdo->prepare("
                SELECT id, date, time, instructor, student, created_at 
                FROM drives 
                WHERE id = :id
            ");
            $stmt->execute([':id' => $newId]);
            $newDrive = $stmt->fetch();
            
            if (!$newDrive) {
                throw new Exception("Nie udało się pobrać danych dodanej jazdy");
            }
            
            $this->sendSuccessResponse(
                $newDrive,
                'Jazda została pomyślnie dodana'
            );
            
        } catch (PDOException $e) {
            error_log("Błąd dodawania jazdy: " . $e->getMessage());
            $this->sendErrorResponse('Nie udało się dodać jazdy do bazy danych', 500);
        }
    }
    
    /**
     * Usuwa jazdę z bazy danych na podstawie ID.
     * 
     * Sprawdza czy jazda o podanym ID istnieje, a następnie ją usuwa.
     * 
     * @return void Wysyła odpowiedź JSON o powodzeniu lub błędzie
     */
    private function deleteDrive() {
        $id = $_GET['id'] ?? '';
        
        // Walidacja ID
        if (!$this->validateId($id)) {
            $this->sendErrorResponse('Nieprawidłowe ID jazdy', 400);
        }
        
        try {
            // Sprawdzenie czy jazda istnieje
            $stmt = $this->pdo->prepare("SELECT id FROM drives WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if (!$stmt->fetch()) {
                $this->sendErrorResponse('Jazda o podanym ID nie istnieje', 404);
            }
            
            // Usunięcie jazdy
            $stmt = $this->pdo->prepare("DELETE FROM drives WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $this->sendSuccessResponse(
                ['deleted_id' => (int)$id],
                'Jazda została pomyślnie usunięta'
            );
            
        } catch (PDOException $e) {
            error_log("Błąd usuwania jazdy: " . $e->getMessage());
            $this->sendErrorResponse('Nie udało się usunąć jazdy', 500);
        }
    }
    
    /**
     * -------------------------------------------------------------------
     * Metody walidacji danych z formularza
     * -------------------------------------------------------------------
     */
    
    /**
     * Przeprowadza pełną walidację danych jazdy.
     * 
     * Sprawdza wszystkie wymagane pola, formaty danych, logiczne ograniczenia
     * i bezpieczeństwo danych wejściowych.
     * 
     * @param array $data Dane jazdy do walidacji
     * @return bool True jeśli dane są prawidłowe, false w przeciwnym razie
     */
    private function validateDriveData($data) {
        $this->errors = [];
        
        // Sprawdzenie wymaganych pól
        $requiredFields = ['date', 'time', 'instructor', 'student'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[] = "Pole '{$field}' jest wymagane";
            }
        }
        
        // Jeśli brak wymaganych pól, nie kontynuuj dalszej walidacji
        if (!empty($this->errors)) {
            return false;
        }
        
        // Walidacja daty
        if (!$this->validateDate($data['date'])) {
            $this->errors[] = 'Nieprawidłowy format daty (wymagany: YYYY-MM-DD)';
        } elseif (!$this->isDateInFuture($data['date'])) {
            $this->errors[] = 'Data jazdy nie może być z przeszłości';
        }
        
        // Walidacja czasu
        if (!$this->validateTime($data['time'])) {
            $this->errors[] = 'Nieprawidłowy format czasu (wymagany: HH:MM)';
        } elseif (!$this->isValidBusinessHour($data['time'])) {
            $this->errors[] = 'Jazdy można planować tylko w godzinach 8:00-18:00';
        }
        
        // Walidacja instruktora
        if (!$this->validatePersonName($data['instructor'])) {
            $this->errors[] = 'Imię i nazwisko instruktora może zawierać tylko litery, spacje i polskie znaki';
        }
        
        // Walidacja kursanta
        if (!$this->validatePersonName($data['student'])) {
            $this->errors[] = 'Imię i nazwisko kursanta może zawierać tylko litery, spacje i polskie znaki';
        }
        
        // Sprawdzenie długości pól
        if (mb_strlen(trim($data['instructor'])) > 100) {
            $this->errors[] = 'Imię i nazwisko instruktora nie może być dłuższe niż 100 znaków';
        }
        
        if (mb_strlen(trim($data['student'])) > 100) {
            $this->errors[] = 'Imię i nazwisko kursanta nie może być dłuższe niż 100 znaków';
        }
        
        return empty($this->errors);
    }
    
    /**
     * Waliduje format daty (YYYY-MM-DD).
     * 
     * @param string $date Data do walidacji
     * @return bool True jeśli format jest prawidłowy
     */
    private function validateDate($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }
    
    /**
     * Sprawdza czy data nie jest z przeszłości.
     * 
     * @param string $date Data do sprawdzenia
     * @return bool True jeśli data jest dzisiejsza lub przyszła
     */
    private function isDateInFuture($date) {
        $inputDate = new DateTime($date);
        $today = new DateTime('today');
        return $inputDate >= $today;
    }
    
    /**
     * Waliduje format czasu (HH:MM lub HH:MM:SS).
     * 
     * @param string $time Czas do walidacji
     * @return bool True jeśli format jest prawidłowy
     */
    private function validateTime($time) {
        // Akceptuje format HH:MM lub HH:MM:SS
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time);
    }
    
    /**
     * Sprawdza czy czas mieści się w godzinach pracy (8:00-18:00).
     * 
     * @param string $time Czas do sprawdzenia
     * @return bool True jeśli czas jest w godzinach pracy
     */
    private function isValidBusinessHour($time) {
        $timeObj = DateTime::createFromFormat('H:i', substr($time, 0, 5));
        $startTime = DateTime::createFromFormat('H:i', '08:00');
        $endTime = DateTime::createFromFormat('H:i', '18:00');
        
        return $timeObj >= $startTime && $timeObj <= $endTime;
    }
    
    /**
     * Waliduje imię i nazwisko (tylko litery, spacje i polskie znaki).
     * 
     * @param string $name Imię i nazwisko do walidacji
     * @return bool True jeśli nazwa jest prawidłowa
     */
    private function validatePersonName($name) {
        $trimmedName = trim($name);
        
        // Sprawdzenie czy nie jest puste po trim
        if (empty($trimmedName)) {
            return false;
        }
        
        // Regex dla polskich liter, spacji i myślników
        return preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s\-]+$/', $trimmedName);
    }
    
    /**
     * Waliduje ID (musi być liczbą całkowitą dodatnią).
     * 
     * @param mixed $id ID do walidacji
     * @return bool True jeśli ID jest prawidłowe
     */
    private function validateId($id) {
        return is_numeric($id) && (int)$id > 0;
    }
    
    /**
     * -------------------------------------------------------------------
     * Metody pomocnicze
     * -------------------------------------------------------------------
     */
    
    /**
     * Sprawdza czy istnieje konflikt czasowy dla instruktora.
     * 
     * Jeden instruktor nie może mieć dwóch jazd w tym samym czasie.
     * 
     * @param string $date Data jazdy
     * @param string $time Czas jazdy
     * @param string $instructor Imię i nazwisko instruktora
     * @return bool True jeśli istnieje konflikt
     */
    private function hasTimeConflict($date, $time, $instructor) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM drives 
                WHERE date = :date 
                AND time = :time 
                AND instructor = :instructor
            ");
            
            $stmt->execute([
                ':date' => $date,
                ':time' => $time,
                ':instructor' => trim($instructor)
            ]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Błąd sprawdzania konfliktu czasowego: " . $e->getMessage());
            return false; // Kiedy nie ma konfliktu, pozwalamy na dodanie
        }
    }
    
    /**
     * -------------------------------------------------------------------
     * Metody odpowiedzi HTTP
     * -------------------------------------------------------------------
     */
    
    /**
     * Wysyła odpowiedź sukcesu w formacie JSON.
     * 
     * @param mixed $data Dane do zwrócenia
     * @param string $message Opcjonalny komunikat
     * @param int $httpCode Kod odpowiedzi HTTP (domyślnie 200)
     * @return void
     */
    private function sendSuccessResponse($data, $message = 'Operacja zakończona sukcesem', $httpCode = 200) {
        http_response_code($httpCode);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Wysyła odpowiedź błędu w formacie JSON.
     * 
     * @param string $message Komunikat błędu
     * @param int $httpCode Kod odpowiedzi HTTP
     * @return void
     */
    private function sendErrorResponse($message, $httpCode = 400) {
        http_response_code($httpCode);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}


/**
 * -------------------------------------------------------------------
 * Uruchomienie API
 * -------------------------------------------------------------------
 */

// Uruchomienie API
try {
    $api = new DriveAPI();
    $api->handleRequest();
} catch (Exception $e) {
    error_log("Krytyczny błąd API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Wystąpił krytyczny błąd serwera',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}
