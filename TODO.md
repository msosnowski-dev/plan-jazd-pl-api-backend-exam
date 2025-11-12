<h1>Zadanie Rekrutacyjne: PHP Developer – Backend dla PlanJazd.pl</h1>
<p>Dzień dobry,<br>Dziękujemy za zainteresowanie pracą w naszym zespole. Twoim zadaniem testowym będzie &quot;ożywienie&quot; prostej aplikacji do zarządzania szkołą jazdy <strong>PlanJazd.pl</strong>.<br>Otrzymujesz od nas jeden plik startowy: <code>index.php</code>.<br>Plik ten zawiera kompletny, nowoczesny frontend (HTML, CSS i JavaScript). Interfejs jest w pełni &quot;klikalny&quot;, jednak jego logika JavaScript odwołuje się do nieistniejącego backendu.<br><strong>Twoim zadaniem jest zbudowanie tego backendu.</strong></p>
<h2>Twoje Zadanie Główne</h2>
<ol>
<li><strong>Zbuduj logikę backendu</strong> w czystym PHP, która obsłuży żądania <code>fetch()</code> wysyłane przez skrypt JS (znajdujący się w pliku <code>index.php</code>).</li>
<li><strong>Stwórz połączenie</strong> z bazą danych MySQL (obowiązkowo przy użyciu <strong>PDO</strong>).</li>
<li><strong>Zaimplementuj walidację</strong> danych po stronie serwera PHP.</li>
<li><strong>Stwórz wymagane pliki:</strong> Oczekujemy, że dostarczysz osobne pliki dla logiki API (np. <code>api.php</code>), konfiguracji bazy (np. <code>config.php</code>) oraz strukturę bazy (<code>schema.sql</code>).</li>
<li><strong>Dodaj dokumentację</strong> w kodzie PHP, wyjaśniającą działanie Twojego API.</li>
</ol>
<h2>Kluczowe Wymagania (Obowiązkowe)</h2>
<p>Rozwiązania, które nie spełnią tych zasad, nie będą oceniane.</p>
<ul>
<li><strong>Czysty PHP:</strong> Cały backend musi być napisany w czystym PHP. <strong>Prosimy o nie</strong> używanie jakichkolwiek frameworków (np. Laravel, Symfony) lub bibliotek ORM.</li>
<li><strong>Tylko MySQL + PDO:</strong> Jedynym źródłem danych musi być baza MySQL. Do komunikacji z bazą <strong>musisz</strong> użyć rozszerzenia <strong>PDO</strong>.</li>
<li><strong>Bezpieczeństwo:</strong> Zadbaj o bezpieczeństwo zapytań oraz kodu.</li>
<li><strong>Dokumentacja w Kodzie:</strong> Twój kod PHP musi zawierać czytelne komentarze (np. w stylu PHPDoc) wyjaśniające, jak działa API (jakich danych oczekuje, co zwraca, jaka jest logika).</li>
<li><strong>Walidacja Server-Side:</strong> Dane z formularza <em>muszą</em> być walidowane po stronie serwera (np. sprawdzanie pustych pól, formatu daty/godziny, czy nazwiska nie zawierają cyfr itp.).</li>
</ul>
<h2>Wymagania Funkcjonalne (Co ma obsłużyć API?)</h2>
<p>Frontend (skrypt JS w <code>index.php</code>) oczekuje od Twojego backendu trzech funkcji:</p>
<ol>
<li><strong>Pobieranie Jazd (GET):</strong> Zwrócenie listy wszystkich zaplanowanych jazd z bazy (w formacie JSON).</li>
<li><strong>Dodawanie Jazdy (POST):</strong> Odebranie danych JSON z formularza, walidacja i zapis do bazy. W razie sukcesu zwróć dodany obiekt, w razie błędu walidacji – komunikat błędu.</li>
<li><strong>Anulowanie Jazdy (DELETE):</strong> Usunięcie jazdy z bazy na podstawie jej ID (przekazanego w URL).</li>
</ol>
<h2>Oczekiwany Rezultat (Co należy przesłać?)</h2>
<p>Oczekujemy od Ciebie jednego archiwum <code>.zip/.rar</code> zawierającego:</p>
<ol>
<li>Wszystkie Twoje pliki backendu (np. <code>api.php</code>, <code>config.php</code>).</li>
<li>Plik <code>schema.sql</code> ze strukturą bazy danych (polecenie <code>CREATE TABLE</code>).</li>
<li>Plik <code>index.php</code>, który od nas otrzymałeś (nawet jeśli nie był modyfikowany).</li>
</ol>
<p>Pliki prześlij na adres email: <code>rekrutacja@planjazd.pl</code> z tytułem: <code>[ET3] Twoje Imię i Nazwisko</code></p>
<h2>Kryteria Oceny</h2>
<ul>
<li><strong>Czy rozwiązanie działa?</strong> (Poprawna integracja frontendu z backendem).</li>
<li><strong>Jakość Kodu Backendu:</strong> (Czytelność, struktura, logika, obsługa błędów).</li>
<li><strong>Bezpieczeństwo:</strong> (Poprawne użycie PDO, kompletna walidacja po stronie serwera).</li>
<li><strong>Jakość Dokumentacji:</strong> (Czytelność i kompletność komentarzy w kodzie PHP).</li>
</ul>
<p>Powodzenia!<br><em>Stopka informacyjna: Przesłany kod źródłowy ma wyłącznie charakter testowy i nie będzie wykorzystywany komercyjnie. Po zakończeniu analizy wszystkie materiały zostaną trwale usunięte z systemu. Kandydat zachowuje pełne prawa autorskie do przesłanych plików.</em></p>
