CREATE DATABASE IF NOT EXISTS proyecto_grado CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE proyecto_grado;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuario de prueba -> email: test@correo.com / pass: 12345678
-- El hash de abajo corresponde a "12345678" con password_hash()
INSERT INTO
    users (
        first_name,
        last_name,
        email,
        password
    )
VALUES (
        'Usuario',
        'De Prueba',
        'test@correo.com',
        '$2y$10$ovulUopGJq.BLDYb.u4ZE.PTQejnZRNOVncDbTaMjIKIGFrWN1FEC'
    )
ON DUPLICATE KEY UPDATE
    email = email;

CREATE TABLE IF NOT EXISTS subjets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    teacher_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

INSERT INTO
    subjets (name, user_id, teacher_name)
VALUES (
        'Matemáticas',
        1,
        'Profesor A'
    ),
    ('Física', 1, 'Profesor B'),
    ('Química', 1, 'Profesor C'),
    ('Biología', 1, 'Profesor D'),
    ('Historia', 1, 'Profesor E');

CREATE TABLE IF NOT EXISTS terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

INSERT INTO
    terms (name, user_id)
VALUES ('Primer Semestre', 1),
    ('Segundo Semestre', 1),
    ('Tercer Semestre', 1);

CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subjet_id INT NOT NULL,
    term_id INT NOT NULL,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    value BIGINT NOT NULL,
    percentage DECIMAL(5, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subjet_id) REFERENCES subjets (id) ON DELETE CASCADE,
    FOREIGN KEY (term_id) REFERENCES terms (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

INSERT INTO
    grades (
        subjet_id,
        term_id,
        user_id,
        name,
        value,
        percentage
    )
VALUES (
        1,
        1,
        1,
        'Examen 1',
        85,
        20.00
    ),
    (
        1,
        1,
        1,
        'Examen 2',
        90,
        30.00
    ),
    (
        1,
        1,
        1,
        'Proyecto',
        95,
        50.00
    ),
    (
        1,
        1,
        1,
        'Examen 1',
        80,
        25.00
    ),
    (
        1,
        1,
        1,
        'Examen 2',
        85,
        25.00
    ),
    (
        1,
        1,
        1,
        'Proyecto',
        90,
        50.00
    ),
    (
        1,
        1,
        1,
        'Examen 1',
        75,
        30.00
    ),
    (
        1,
        1,
        1,
        'Examen 2',
        80,
        30.00
    ),
    (
        1,
        1,
        1,
        'Proyecto',
        85,
        40.00
    );

CREATE TABLE IF NOT EXISTS goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    target_date DATE,
    status ENUM(
        'pending',
        'in_progress',
        'completed'
    ) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjets (id) ON DELETE CASCADE
);

INSERT INTO
    goals (
        user_id,
        subject_id,
        name,
        description,
        target_date,
        status
    )
VALUES (
        1,
        1,
        'Mejorar en Matemáticas',
        'Quiero mejorar mis habilidades en matemáticas para el próximo examen.',
        '2024-12-31',
        'in_progress'
    ),
    (
        1,
        2,
        'Prepararme para Física',
        'Necesito prepararme para el examen de física y entender mejor los conceptos.',
        '2024-11-30',
        'pending'
    ),
    (
        1,
        3,
        'Proyecto de Química',
        'Quiero completar mi proyecto de química antes de la fecha límite y obtener una buena calificación.',
        '2024-10-15',
        'in_progress'
    ),
    (
        1,
        4,
        'Estudiar Biología',
        'Quiero estudiar biología para el próximo examen y mejorar mi comprensión de los temas.',
        '2024-09-30',
        'pending'
    );