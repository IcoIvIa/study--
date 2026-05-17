<?php


class Database {
    private $pdo;

    public function __construct(){
        require_once '../config/database.php';
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
        );
    }

    public function query($sql, $params = []){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    }

