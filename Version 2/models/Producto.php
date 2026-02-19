<?php
require_once __DIR__ . '/../config/database.php';

class Producto {

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($nombre, $precio, $stock) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $precio, $stock]);
        return $db->lastInsertId();
    }

    public static function updateStock($id, $nuevoStock) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        return $stmt->execute([$nuevoStock, $id]);
    }

    public static function getAll() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);
    }
}

