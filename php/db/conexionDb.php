<?php
    class conexionDb {

        private $host = 'sql7.freesqldatabase.com:3306';
        private $db = 'sql7783258';
        private $user = 'sql7783258';
        private $password = 'zA7i4H5Vhb';

        
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
