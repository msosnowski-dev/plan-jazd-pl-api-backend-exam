-- -------------------------------------------------------------------
-- SCHEMAT BAZY DANYCH DLA APLIKACJI PLANJAZD.PL
-- -------------------------------------------------------------------

-- -------------------------------------------------------------------
-- TABELA: drives (jazdy)
-- -------------------------------------------------------------------
-- Przechowuje informacje o wszystkich zaplanowanych jazdach
-- instruktorskich w szkole jazdy.

CREATE TABLE IF NOT EXISTS drives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time TIME NOT NULL,
    instructor VARCHAR(100) NOT NULL,
    student VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Indeks na kombinacji daty i godziny dla szybkiego sortowania
-- i wyszukiwania jazd w określonym czasie
CREATE INDEX idx_date_time ON drives (date, time);

-- Indeks na instruktorze dla szybkiego wyszukiwania jazd danego instruktora
CREATE INDEX idx_instructor ON drives (instructor);

-- Indeks na kursancie dla szybkiego wyszukiwania jazd danego kursanta
CREATE INDEX idx_student ON drives (student);


-- PRzykładowe rekordy

/*
INSERT INTO drives (date, time, instructor, student) VALUES
('2024-10-26', '09:00:00', 'Jan Kowalski', 'Anna Nowak'),
('2024-10-26', '10:30:00', 'Maria Wiśniewska', 'Piotr Zieliński'),
('2024-10-27', '14:00:00', 'Jan Kowalski', 'Katarzyna Lewandowska'),
('2024-10-28', '11:15:00', 'Tomasz Dąbrowski', 'Michał Wójcik'),
('2024-10-29', '16:45:00', 'Maria Wiśniewska', 'Agnieszka Kaczmarek');
*/
