CREATE DATABASE appointment;
USE appointment;
CREATE TABLE users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    number VARCHAR(15) NOT NULL,
    appointment_for VARCHAR(50) NOT NULL,
    appointment_description VARCHAR(200) NOT NULL,
    date NOT NULL,
    time(6) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
