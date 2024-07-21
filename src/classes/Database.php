<?php
class Database {
    private $host = "mysql_db";
    private $username = "root";
    private $password = "toor";
    private $dbname = "login_db";
    public $mysqli;
    private $pdo;

    public function __construct() {
        $this->mysqli = new mysqli($this->host, $this->username, $this->password);

        if ($this->mysqli->connect_errno) {
            die("Connection error: " . $this->mysqli->connect_error);
        }

        $sql = 'CREATE DATABASE IF NOT EXISTS ' . $this->dbname;
        if (mysqli_query($this->mysqli, $sql)) {
            mysqli_select_db($this->mysqli, $this->dbname);
            $this->createTables();
        }

        //triggere si proceduri cu PDO
        $dsn = "mysql:host=$this->host;dbname=$this->dbname";
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $this->createTriggers();
            $this->createProcedures();
        } catch (PDOException $e) {
            die("PDO connection error: " . $e->getMessage());
        }
    }

    private function createTables() {
        $userTable = 'CREATE TABLE IF NOT EXISTS Users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            pass_hash VARCHAR(255) NOT NULL
        )';

        $imgTable = 'CREATE TABLE IF NOT EXISTS Img (
            id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            name VARCHAR(250) NOT NULL,
            mime VARCHAR(255) NOT NULL,
            data LONGBLOB NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )';

        $imgArchiveTable = 'CREATE TABLE IF NOT EXISTS ImgArchive (
            id INT(11) NOT NULL,
            name VARCHAR(250) NOT NULL,
            mime VARCHAR(255) NOT NULL,
            data LONGBLOB NOT NULL,
            deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )';

        $userLogTable = 'CREATE TABLE IF NOT EXISTS UserLog (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            action VARCHAR(255) NOT NULL,
            log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id)
        )';

        mysqli_query($this->mysqli, $userTable);
        mysqli_query($this->mysqli, $imgTable);
        mysqli_query($this->mysqli, $imgArchiveTable);
        mysqli_query($this->mysqli, $userLogTable);
    }

    private function createTriggers() {
        $triggers = [
            'CREATE TRIGGER before_img_insert
             BEFORE INSERT ON Img
             FOR EACH ROW
             BEGIN
                 SET NEW.created_at = NOW();
             END',

            'CREATE TRIGGER after_user_insert
             AFTER INSERT ON Users
             FOR EACH ROW
             BEGIN
                 INSERT INTO UserLog (user_id, action, log_time) VALUES (NEW.id, "User Registered", NOW());
             END',

            'CREATE TRIGGER before_img_delete
             BEFORE DELETE ON Img
             FOR EACH ROW
             BEGIN
                 INSERT INTO ImgArchive (id, name, mime, data, deleted_at) VALUES (OLD.id, OLD.name, OLD.mime, OLD.data, NOW());
             END'
        ];

        foreach ($triggers as $trigger) {
            try {
                $this->pdo->exec($trigger);
            } catch (PDOException $e) {
                // Handle exceptions or log errors if necessary
            }
        }
    }

    private function createProcedures() {
        $procedures = [
            'CREATE PROCEDURE InsertImage(IN imageName VARCHAR(255), IN imageData LONGBLOB, IN imageMime VARCHAR(255))
             BEGIN
                 INSERT INTO Img (name, data, mime) VALUES (imageName, imageData, imageMime);
             END',

            'CREATE PROCEDURE DeleteImage(IN imageId INT)
             BEGIN
                 DELETE FROM Img WHERE id = imageId;
             END',

            'CREATE PROCEDURE UpdateImage(IN imageId INT, IN imageName VARCHAR(255), IN imageData LONGBLOB, IN imageMime VARCHAR(255))
             BEGIN
                 UPDATE Img SET name = imageName, data = imageData, mime = imageMime WHERE id = imageId;
             END'
        ];

        foreach ($procedures as $procedure) {
            try {
                $this->pdo->exec($procedure);
            } catch (PDOException $e) {
                
            }
        }
    }
}
