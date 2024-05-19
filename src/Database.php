<?php
namespace App;

use PDO;
use PDOException;

class Database {
    private $hostname = 'localhost';
    private $port = 3306;
    private $dbname = 'b-cart';
    private $username = 'root';
    private $password = '';
    private $dsn = '';

    private $pdo = null;

    public function __construct() {
        $this->pdo = null;
        $this->dsn = "mysql:host={$this->hostname};port={$this->port};dbname={$this->dbname}";
    }

    public function connect() {
        if (!$this->pdo) {
            try {
                $this->pdo = new PDO($this->dsn, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    public function close() {
        $this->pdo = null;
    }
}
