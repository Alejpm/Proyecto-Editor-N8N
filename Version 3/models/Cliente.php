<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($nombre, $activo = true, $deuda = 0) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO clientes (nombre, activo, deuda) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $activo, $deuda]);
        return $db->lastInsertId();
    }

    public static function updateDeuda($id, $deuda) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE clientes SET deuda = ? WHERE id = ?");
        return $stmt->execute([$deuda, $id]);
    }

    public static function getAll() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
    }
}

