-- Create the database
CREATE DATABASE IF NOT EXISTS livestock_db;
USE livestock_db;

-- Animals table
CREATE TABLE IF NOT EXISTS animals (
    tagId VARCHAR(50) PRIMARY KEY,
    name VARCHAR(50),
    animalType VARCHAR(20),
    sex VARCHAR(10),
    breed VARCHAR(50),
    birthdate DATE,
    isPregnant BOOLEAN DEFAULT FALSE,
    isSick BOOLEAN DEFAULT FALSE,
    ownerContact VARCHAR(20)
);

-- HealthRecords table
CREATE TABLE IF NOT EXISTS healthRecords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tagId VARCHAR(50),
    type VARCHAR(50),
    startDate DATE,
    endDate DATE,
    nextEventDate DATE,
    notes TEXT,
    vetName VARCHAR(50),
    vetContact VARCHAR(20),
    FOREIGN KEY (tagId) REFERENCES animals(tagId)
);