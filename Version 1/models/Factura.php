<?php
require_once __DIR__ . '/../config/database.php';

class Factura {

    public static function create($pedido_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO facturas (pedido_id, fecha) VALUES (?, NOW())");
        $stmt->execute([$pedido_id]);
        return $db->lastInsertId();
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM facturas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM facturas")->fetchAll(PDO::FETCH_ASSOC);
    }
}

