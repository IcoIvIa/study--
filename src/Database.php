<?php


class Database
{
    private $pdo;

    public function __construct()
    {
        try{
        require_once __DIR__ . '/../config/database.php';
        $this->pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password,
            [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            ]
            );
        } catch (PDOException $e) {
    error_log($e->getMessage());
    die('データベース接続エラーが発生しました');
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
