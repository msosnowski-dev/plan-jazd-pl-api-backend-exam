# Zadanie rekrutacyjne - Backend Developer PHP

## Opis zadania

Zadanie polegało na zbudowaniu backendu dla aplikacji do zarządzania szkołą jazdy PlanJazd.pl. Aplikacja umożliwia:
- Przeglądanie listy zaplanowanych jazd
- Dodawanie nowych jazd
- Anulowanie istniejących jazd

## Wymagania techniczne

- **Zalecane środowisko:**
  - XAMPP (zawiera Apache, MySQL i PHP)
  - PHP 7.4+
  - MySQL 5.7+

- **Alternatywne środowiska:**
  - Serwer WWW (Apache/Nginx) z obsługą PHP
  - Baza danych MySQL/MariaDB

## Struktura projektu

- `index.php` - Frontend aplikacji (dostarczony przez zespół rekrutacyjny)
- `api.php` - Główny plik API obsługujący żądania HTTP
- `config.php` - Konfiguracja połączenia z bazą danych
- `schema.sql` - Skrypt tworzący strukturę bazy danych
- `README.md` - Ten plik z dokumentacją

## Instalacja

1. Sklonuj repozytorium:
   ```bash
   git clone [adres-repozytorium]
   cd plan-jazd
   ```

2. Skonfiguruj połączenie z bazą danych w pliku `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'planjazd_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. Postępuj zgodnie z instrukcją w sekcji "Testowanie z użyciem XAMPP" aby skonfigurować bazę danych i uruchomić aplikację.

## API Endpoints

### Pobieranie listy jazd
- **Metoda:** GET
- **URL:** `/api.php/drives`
- **Odpowiedź:**
  ```json
  [
    {
      "id": 1,
      "date": "2024-10-26",
      "time": "09:00:00",
      "instructor": "Jan Kowalski",
      "student": "Anna Nowak"
    }
  ]
  ```

### Dodawanie nowej jazdy
- **Metoda:** POST
- **URL:** `/api.php/drives`
- **Nagłówki:** `Content-Type: application/json`
- **Ciało żądania:**
  ```json
  {
    "date": "2024-10-26",
    "time": "09:00:00",
    "instructor": "Jan Kowalski",
    "student": "Anna Nowak"
  }
  ```
- **Odpowiedź (sukces):**
  ```json
  {
    "id": 1,
    "date": "2024-10-26",
    "time": "09:00:00",
    "instructor": "Jan Kowalski",
    "student": "Anna Nowak"
  }
  ```

### Usuwanie jazdy
- **Metoda:** DELETE
- **URL:** `/api.php/drives/{id}`
- **Odpowiedź (sukces):**
  ```json
  {
    "success": true,
    "message": "Jazda została usunięta"
  }
  ```

## Walidacja danych

Backend przeprowadza następujące walidacje:
- Wymagane pola nie mogą być puste
- Data musi być w formacie YYYY-MM-DD
- Godzina musi być w formacie HH:MM:SS
- Instruktor i uczeń muszą mieć co najmniej 2 znaki
- Data i godzina nie mogą być z przeszłości

## Bezpieczeństwo

- Wykorzystano prepared statements do zabezpieczenia przed SQL Injection
- Wszystkie dane wejściowe są walidowane
- Obsługa błędów i wyjątków
- Brak wyświetlania szczegółów błędów produkcyjnych

## Testowanie z użyciem XAMPP

1. Skopiuj zawartość projektu do katalogu `htdocs` w instalacji XAMPP (zwykle `C:\xampp\htdocs\plan-jazd`)

2. Uruchom panel sterowania XAMPP i włącz moduły Apache i MySQL

3. Zaimportuj bazę danych:
   - Otwórz phpMyAdmin (http://localhost/phpmyadmin)
   - Utwórz nową bazę danych o nazwie `planjazd_db`
   - Wybierz utworzoną bazę i zaimportuj plik `schema.sql`

4. Otwórz aplikację w przeglądarce:
   ```
   http://localhost/plan-jazd
   ```

3. Przetestuj funkcjonalność:
   - Wyświetl listę jazd
   - Dodaj nową jazdę
   - Spróbuj dodać nieprawidłowe dane
   - Usuń istniejącą jazdę

## Autor

[Michał Sosnowski]
[04.11.2025]

## Licencja

To zadanie zostało wykonane w celach rekrutacyjnych.
