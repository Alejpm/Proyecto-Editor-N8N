<?php
require_once __DIR__ . '/../config/database.php';

class NodeExecutor {

    public static function execute($node, &$context) {
        $db = Database::getConnection();
        $type = $node['type'];
        $config = json_decode($node['config'], true);

        switch ($type) {

            case "crear_pedido":
                $context['pedido'] = $config;
                return ["ok" => true];

            case "verificar_cliente":
	    require_once __DIR__ . '/../models/Cliente.php';
	    $cliente = Cliente::getById($context['pedido']['cliente_id']);

	    if (!$cliente || !$cliente['activo']) {
		throw new Exception("Cliente no válido");
	    }

	    $context['cliente'] = $cliente;
	    return ["ok" => true];


            case "verificar_stock":
                $stmt = $db->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$context['pedido']['producto_id']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($producto['stock'] < $context['pedido']['cantidad']) {
                    throw new Exception("Stock insuficiente");
                }

                $context['producto'] = $producto;
                return ["ok" => true];

            case "calcular_total":
                $total = $context['producto']['precio'] * $context['pedido']['cantidad'];
                $context['total'] = $total;
                return ["total" => $total];

            case "guardar_pedido":
                $stmt = $db->prepare("INSERT INTO pedidos (cliente_id, total, fecha) VALUES (?, ?, NOW())");
                $stmt->execute([
                    $context['pedido']['cliente_id'],
                    $context['total']
                ]);
                $context['pedido_id'] = $db->lastInsertId();
                return ["pedido_id" => $context['pedido_id']];

            case "generar_factura":
                $stmt = $db->prepare("INSERT INTO facturas (pedido_id, fecha) VALUES (?, NOW())");
                $stmt->execute([$context['pedido_id']]);
                return ["factura_generada" => true];

            default:
                throw new Exception("Tipo de nodo desconocido");
        }
    }
}

