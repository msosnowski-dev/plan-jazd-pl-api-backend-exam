<?php
/**
 * -----------------------------------------------------------------
 * PLIK STARTOWY ZADANIA REKRUTACYJNEGO
 * -----------------------------------------------------------------
 *
 * Witaj Kandydacie,
 *
 * Otrzymujesz ten plik `index.php` jako kompletny, działający
 * frontend aplikacji "PlanJazd.pl" (HTML, CSS i JavaScript).
 *
 * TWOJE ZADANIE:
 * 1. Nie modyfikuj logiki JavaScript w tym pliku (chyba że
 * wymaga tego Twoje API, np. zmiana URL).
 * 2. Zbuduj backend w czystym PHP, który obsłuży żądania
 * AJAX (fetch) wysyłane przez skrypt JS na dole tego pliku.
 * 3. Skrypt JS oczekuje, że API będzie odpowiadać na żądania:
 * - GET (dla ?action=get): Pobranie listy jazd.
 * - POST (dla ?action=add): Dodanie nowej jazdy.
 * - DELETE (dla ?action=delete): Usunięcie jazdy.
 * 4. Stwórz pliki `api.php`, `config.php` (lub podobne)
 * oraz `schema.sql` dla bazy danych MySQL.
 * 5. Zaimplementuj logikę walidacji po stronie serwera PHP
 * zgodnie z pełnym opisem zadania (readme).
 *
 * Powodzenia!
 */

/**
 * Poniższy kod HTML/CSS/JS to w pełni działający frontend.
 * Twoja logika PHP (backend) musi zostać zbudowana osobno
 * i obsługiwać żądania wysyłane przez ten frontend.
 */
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanJazd.pl - Panel Zarządzania</title>
    
    <!-- Ładowanie Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ikony Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Czcionka Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark-primary: #0d1117; 
            --bg-dark-secondary: #161b22; 
            --border-color: #30363d; 
            --text-primary: #c9d1d9; 
            --text-secondary: #8b949e; 
            --accent-blue: #58a6ff; 
            --accent-green: #34d399; 
            --accent-red: #f87171;
            --demo-bar-color: #2c3e50; 
            --demo-text-color: #e6e6e6; 
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark-primary);
            color: var(--text-primary);
        }
        
        /* Pasek demo z students.php */
        .demo-bar {
            background-color: var(--demo-bar-color); 
            color: var(--demo-text-color);
            padding: 0.5rem 1rem;
            font-size: 0.875rem; 
            font-weight: 600; 
            z-index: 100; 
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
            position: sticky;
            top: 0;
            width: 100%;
        }

        /* Układ z students.php */
        .sidebar {
            background-color: var(--bg-dark-secondary); 
            border-right: 1px solid var(--border-color);
        }
        .sidebar-link {
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background-color: rgba(88, 166, 255, 0.1); 
            color: var(--accent-blue);
            font-weight: 600;
        }

        /* Styl dla paska przewijania (dla estetyki) */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }

        /* Style dla powiadomienia "Toast" (dark mode) */
        #toast {
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            background-color: #1f2937; /* Ciemne tło */
            border: 1px solid var(--border-color);
        }
        #toast.bg-green-500 { background-color: var(--accent-green) !important; color: #000; }
        #toast.bg-red-500 { background-color: var(--accent-red) !important; color: #000; }

        /* Style dla Modułu (dark mode) */
        #confirmationModal {
            transition: opacity 0.2s ease-in-out;
        }
        #modalPanel {
            background-color: var(--bg-dark-secondary);
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease-in-out;
        }
        #confirmationModal.hidden { opacity: 0; pointer-events: none; }
        #confirmationModal.hidden #modalPanel { transform: scale(0.95); }

        /* Style dla formularza (dark mode) */
        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
        }
        .form-input {
            background-color: #1f2937; /* Ciemniejsze tło inputu */
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 100%;
            padding-left: 2.5rem; /* 40px */
            padding-right: 1rem;
            padding-top: 0.75rem; /* 12px */
            padding-bottom: 0.75rem;
            border-radius: 0.5rem; /* rounded-lg */
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(88, 166, 255, 0.3);
        }
        /* Poprawka dla ikony w inpucie */
        .input-icon {
            padding-top: 2rem; /* Dopasowanie do labelki */
        }
        /* Poprawka dla selektorów daty/godziny w dark mode */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }
        
        /* Lista jazd (dark mode) */
        .drive-item {
            background-color: var(--bg-dark-secondary);
            border: 1px solid var(--border-color);
        }
        .drive-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            background-color: #1f2937;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Pasek Demo (z students.php) -->
    <div class="demo-bar flex justify-center items-center gap-4 flex-wrap">
        <span class="font-bold uppercase tracking-wider text-yellow-300">
            <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-1"></i> ZADANIE REKRUTACYJNE
        </span>
        <span class="hidden sm:inline-block">Frontend oczekuje na podłączenie API.</span>
    </div>

    <!-- Kontener na powiadomienia (Toast) -->
    <div id="toast" class="fixed top-20 right-5 z-50 p-4 rounded-md shadow-lg text-white max-w-sm transform translate-x-[120%] opacity-0">
        <span id="toast-message"></span>
    </div>

    <!-- Główny kontener aplikacji (z students.php) -->
    <div class="min-h-screen flex flex-col md:flex-row" style="height: calc(100vh - 40px);">
        
        <!-- Boczny panel (z students.php) -->
        <aside class="sidebar w-full md:w-64 shadow-md md:h-full sticky top-0 flex-shrink-0">
            <div class="p-6 border-b border-border-color">
                <h1 class="text-2xl font-bold text-accent-blue flex items-center gap-2">
                    <i data-lucide="calendar-clock" class="w-7 h-7"></i>
                    PlanJazd.pl
                </h1>
            </div>
            <nav class="p-4">
                <a href="#" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg font-semibold">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Harmonogram
                </a>
                <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium mt-2">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Instruktorzy
                </a>
                <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium mt-2">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                    Kursanci
                </a>
            </nav>
        </aside>

        <!-- Główna treść (z index.php, wstawiona w układ students.php) -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            <h2 class="text-3xl font-bold text-primary mb-8">Zarządzaj harmonogramem jazd</h2>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Kolumna z formularzem (przestylizowana) -->
                <div class="lg:col-span-1">
                    <div class="bg-dark-secondary p-6 rounded-xl shadow-lg border border-border-color">
                        <h3 class="text-xl font-bold mb-6 text-primary">Dodaj nową jazdę</h3>
                        <form id="addDriveForm" class="space-y-5">
                            
                            <div class="relative">
                                <label for="date" class="block text-sm font-semibold text-secondary mb-1">Data</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none input-icon">
                                    <i data-lucide="calendar" class="w-5 h-5 text-gray-500"></i>
                                </div>
                                <input type="date" id="date" name="date" required class="form-input">
                            </div>

                            <div class="relative">
                                <label for="time" class="block text-sm font-semibold text-secondary mb-1">Godzina</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none input-icon">
                                    <i data-lucide="clock" class="w-5 h-5 text-gray-500"></i>
                                </div>
                                <input type="time" id="time" name="time" required class="form-input">
                            </div>

                            <div class="relative">
                                <label for="instructor" class="block text-sm font-semibold text-secondary mb-1">Instruktor</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none input-icon">
                                    <i data-lucide="user-cog" class="w-5 h-5 text-gray-500"></i>
                                </div>
                                <input type="text" id="instructor" name="instructor" placeholder="np. Jan Kowalski" required class="form-input">
                            </div>

                            <div class="relative">
                                <label for="student" class="block text-sm font-semibold text-secondary mb-1">Kursant</label>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none input-icon">
                                    <i data-lucide="user" class="w-5 h-5 text-gray-500"></i>
                                </div>
                                <input type="text" id="student" name="student" placeholder="np. Anna Nowak" required class="form-input">
                            </div>
                            
                            <button type="submit" class="w-full bg-accent-blue text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all transform hover:-translate-y-0.5 !mt-8">
                                Dodaj do planu
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Kolumna z listą jazd (przestylizowana) -->
                <div class="lg:col-span-2">
                    <div class="bg-dark-secondary p-6 rounded-xl shadow-lg border border-border-color min-h-[400px]">
                        <h3 class="text-xl font-bold mb-6 text-primary">Zaplanowane jazdy</h3>
                        
                        <!-- Wskaźnik ładowania -->
                        <div id="loader" class="flex justify-center items-center py-10">
                            <i data-lucide="loader" class="animate-spin h-8 w-8 text-accent-blue"></i>
                            <span class="ml-3 text-secondary font-medium">Ładowanie danych...</span>
                        </div>

                        <!-- Kontener na listę jazd -->
                        <ul id="drivesList" class="space-y-4">
                            <!-- Elementy listy będą dodawane dynamicznie przez JS -->
                        </ul>

                        <!-- Komunikat, gdy lista jest pusta -->
                        <p id="emptyState" class="text-center text-secondary py-10 hidden">
                            Brak zaplanowanych jazd. Dodaj pierwszą, korzystając z formularza.
                        </p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Moduł Potwierdzenia (Popup) (przestylizowany) -->
    <div id="confirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70">
        <div id="modalPanel" class="p-6 rounded-lg shadow-xl max-w-sm w-full mx-4">
            <h3 class="text-lg font-bold text-primary">Potwierdzenie</h3>
            <p class="text-secondary mt-2 mb-6">Czy na pewno chcesz anulować tę jazdę? Tej operacji nie można cofnąć.</p>
            <div class="flex justify-end gap-3">
                <button id="modalCancelBtn" class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                    Nie, zostaw
                </button>
                <button id="modalConfirmBtn" data-id="" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                    Tak, anuluj
                </button>
            </div>
        </div>
    </div>

    <!-- Logika JavaScript po stronie klienta (Frontend) -->
    <script>
        // Inicjalizacja ikon Lucide
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', () => {

            // === SELEKTORY DOM ===
            const driveForm = document.getElementById('addDriveForm');
            const drivesList = document.getElementById('drivesList');
            const loader = document.getElementById('loader');
            const emptyState = document.getElementById('emptyState');
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const modal = document.getElementById('confirmationModal');
            const modalCancelBtn = document.getElementById('modalCancelBtn');
            const modalConfirmBtn = document.getElementById('modalConfirmBtn');

            // === KONFIGURACJA API ===
            // Kandydat będzie musiał stworzyć ten plik i jego logikę
            const API_BASE_URL = 'api.php'; 

            /**
             * Wyświetla powiadomienie (toast)
             */
            function showToast(message, type = 'success') {
                toastMessage.textContent = message;
                toast.classList.remove('bg-green-500', 'bg-red-500');
                toast.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
                toast.classList.remove('opacity-0', 'translate-x-[120%]');
                toast.classList.add('opacity-100', 'translate-x-0');
                setTimeout(() => {
                    toast.classList.remove('opacity-100', 'translate-x-0');
                    toast.classList.add('opacity-0', 'translate-x-[120%]');
                }, 3000);
            }

            /**
             * Przełącza widoczność wskaźnika ładowania
             */
            function setLoading(isLoading) {
                loader.classList.toggle('hidden', !isLoading);
                drivesList.classList.toggle('hidden', isLoading);
                emptyState.classList.add('hidden'); 
            }

            /**
             * Renderuje pojedynczy element listy jazd
             */
            function renderDriveItem(drive) {
                const li = document.createElement('li');
                li.className = 'drive-item flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200';
                li.dataset.id = drive.id; 

                // Formatowanie daty i czasu (upewnijmy się, że czas jest obsługiwany poprawnie)
                const displayDate = new Date(drive.date + 'T00:00:00').toLocaleDateString('pl-PL', { year: 'numeric', month: 'long', day: 'numeric' });
                const displayTime = drive.time ? drive.time.substring(0, 5) : 'Brak';

                li.innerHTML = `
                    <div class="flex-1 mb-3 sm:mb-0 pr-4">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-accent-blue text-lg">${displayTime}</span>
                            <span class="text-primary font-semibold">${displayDate}</span>
                        </div>
                        <div class="text-sm text-secondary mt-2 space-y-1">
                            <p><span class="font-medium text-gray-500 w-20 inline-block">Instruktor:</span> ${drive.instructor}</p>
                            <p><span class="font-medium text-gray-500 w-20 inline-block">Kursant:</span> ${drive.student}</p>
                        </div>
                    </div>
                    <button data-id="${drive.id}" class="cancel-btn text-sm font-medium text-red-400 hover:text-red-300 bg-red-900/50 hover:bg-red-900/80 px-3 py-2 rounded-md transition-colors w-full sm:w-auto flex-shrink-0">
                        Anuluj jazdę
                    </button>
                `;
                
                li.querySelector('.cancel-btn').addEventListener('click', handleCancelClick);
                drivesList.appendChild(li);
            }

            /**
             * Pobiera i wyświetla listę jazd z serwera
             */
            async function fetchDrives() {
                setLoading(true);
                drivesList.innerHTML = ''; 

                try {
                    // Żądanie GET do pobrania listy jazd
                    const response = await fetch(`${API_BASE_URL}?action=get`);
                    if (!response.ok) throw new Error(`Błąd serwera: ${response.statusText}. Sprawdź, czy plik API działa.`);
                    const drives = await response.json();

                    if (drives.length === 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                        // Sortowanie po stronie klienta, aby mieć pewność
                        drives.sort((a, b) => (a.date + a.time).localeCompare(b.date + b.time));
                        drives.forEach(renderDriveItem);
                    }
                } catch (error) {
                    console.error('Błąd podczas pobierania jazd:', error);
                    showToast(error.message, 'error');
                    emptyState.classList.remove('hidden');
                } finally {
                    setLoading(false);
                }
            }

            /**
             * Obsługuje wysłanie formularza dodawania nowej jazdy
             */
            async function handleAddDrive(e) {
                e.preventDefault(); 
                const formData = new FormData(driveForm);
                const driveData = Object.fromEntries(formData.entries());

                try {
                    // Żądanie POST do dodania nowej jazdy
                    const response = await fetch(`${API_BASE_URL}?action=add`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(driveData),
                    });

                    const result = await response.json();
                    if (!response.ok || result.status !== 'success') {
                        // Oczekujemy, że API zwróci błąd walidacji w 'result.message'
                        throw new Error(result.message || 'Wystąpił nieznany błąd serwera.');
                    }

                    renderDriveItem(result.data); // Oczekujemy, że API zwróci dodany obiekt
                    driveForm.reset(); 
                    emptyState.classList.add('hidden'); 
                    showToast('Pomyślnie dodano nową jazdę!', 'success');

                } catch (error) {
                    console.error('Błąd podczas dodawania jazdy:', error);
                    showToast(error.message, 'error');
                }
            }

            /**
             * Wykonuje logikę usunięcia po potwierdzeniu w module
             */
            async function performDeletion(driveId) {
                const driveElement = document.querySelector(`li[data-id="${driveId}"]`);
                if (!driveElement) return;

                try {
                    // Żądanie DELETE do usunięcia jazdy
                    const response = await fetch(`${API_BASE_URL}?action=delete&id=${driveId}`, {
                        method: 'DELETE',
                    });

                    const result = await response.json();
                    if (!response.ok || result.status !== 'success') {
                        throw new Error(result.message || 'Nie udało się usunąć jazdy.');
                    }

                    driveElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    driveElement.style.opacity = '0';
                    driveElement.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        driveElement.remove();
                        if (drivesList.children.length === 0) {
                            emptyState.classList.remove('hidden');
                        }
                    }, 300);

                    showToast('Jazda została anulowana.', 'success');

                } catch (error) {
                    console.error('Błąd podczas anulowania jazdy:', error);
                    showToast(error.message, 'error');
                }
            }

            /**
             * Obsługuje kliknięcie przycisku "Anuluj" - tylko pokazuje modal
             */
            function handleCancelClick(e) {
                const driveId = e.currentTarget.dataset.id;
                modalConfirmBtn.dataset.id = driveId;
                modal.classList.remove('hidden');
            }

            // === INICJALIZACJA ===
            
            driveForm.addEventListener('submit', handleAddDrive);

            modalCancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modalConfirmBtn.dataset.id = '';
            });

            // Zamykanie modala po kliknięciu tła
            modal.addEventListener('click', (e) => {
                if (e.target === modal) { 
                    modal.classList.add('hidden');
                    modalConfirmBtn.dataset.id = '';
                }
            });

            // Potwierdzenie usunięcia
            modalConfirmBtn.addEventListener('click', () => {
                const driveId = modalConfirmBtn.dataset.id;
                if (driveId) {
                    performDeletion(driveId);

                    modal.classList.add('hidden'); 
                    modalConfirmBtn.dataset.id = ''; 
                }
            });

            // Pobranie danych przy starcie
            fetchDrives();
        });
    </script>

</body>
</html>

