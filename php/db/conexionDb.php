<?php
    class conexionDb {

        private $host = 'sql303.infinityfree.com:3306';
        private $db = 'if0_39165783_remote_order';
        private $user = 'if0_39165783';
        private $password = 'nQkvT8UZu9C93cp';

        
        private $conn;

        public function __construct() {
            $this->connect();
        }

        private function connect() {
            try {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }

        public function getConnection() {
            return $this->conn;
        }

        public function closeConnection() {
            $this->conn = null;
        }

    }
?>
