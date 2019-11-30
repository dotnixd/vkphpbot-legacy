<?php
    class DB {
        private $host;
        private $user;
        private $name;
        private $passwd;
        private $conn;

        public function __construct($dblocation, $dbuser, $dbpasswd, $dbname) {
            $this->host = $dblocation;
            $this->user = $dbuser;
            $this->name = $dbname;
            $this->passwd = $dbpasswd;
        }

        public function Connect() {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->name . ";charset=utf8";
            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $this->conn = new PDO($dsn, $this->user, $this->passwd, $opt);
            } catch(PDOException $e) {
                $api->SendMessage($e->getMessage(), 20000001);
                echo $e->getMessage();
            }
        }

        public function GetConn() {
            return $this->conn;
        }

        public function Query() {
            $args = func_get_args();
        }
    }
?>