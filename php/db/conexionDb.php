<?php
    class conexionDb {
        private $host = 'localhost';
        private $db = 'remoteorder';
        private $user = 'root';
        private $password = '';

        /*

        private $host = '127.0.0.1:3306';
        private $db = 'u248962232_remoteorder';
        private $user = 'u248962232_remoteorder';
        private $password = 'u248962232_remoteordeR';

        */

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
