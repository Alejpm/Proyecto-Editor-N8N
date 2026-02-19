<?php
require_once __DIR__ . '/../config/database.php';

class Pedido {

    public static function create($cliente_id, $total) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO pedidos (cliente_id, total, fecha) VALUES (?, ?, NOW())");
        $stmt->execute([$cliente_id, $total]);
        return $db->lastInsertId();
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM pedidos")->fetchAll(PDO::FETCH_ASSOC);
    }
}

