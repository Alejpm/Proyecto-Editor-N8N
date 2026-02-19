<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Factura.php';

class NodeExecutor {

    /* ==========================================
       DEFINICIÓN PROFESIONAL DE NODOS
    ========================================== */

    private static $definitions = [

        "crear_pedido" => [
            "requires" => [],
            "provides" => ["pedido"]
        ],

        "verificar_cliente" => [
            "requires" => ["pedido"],
            "provides" => ["cliente"]
        ],

        "verificar_stock" => [
            "requires" => ["pedido"],
            "provides" => ["producto"]
        ],

        "calcular_total" => [
            "requires" => ["pedido", "producto"],
            "provides" => ["total"]
        ],

        "guardar_pedido" => [
            "requires" => ["pedido", "total"],
            "provides" => ["pedido_id"]
        ],

        "generar_factura" => [
            "requires" => ["pedido_id"],
            "provides" => ["factura_id"]
        ]
    ];

    /* ==========================================
       EJECUCIÓN PRINCIPAL
    ========================================== */

    public static function execute($node, &$context) {

        $type = $node['type'];
        $config = json_decode($node['config'], true);

        if (!$config) {
            $config = [];
        }

        if (!isset(self::$definitions[$type])) {
            throw new Exception("Tipo de nodo desconocido: " . $type);
        }

        $definition = self::$definitions[$type];

        // 🔒 VALIDACIÓN DE DEPENDENCIAS
        foreach ($definition["requires"] as $req) {
            if (!isset($context[$req])) {
                throw new Exception(
                    "El nodo '{$type}' requiere '{$req}' pero no existe en el contexto"
                );
            }
        }

        // 🚀 EJECUCIÓN REAL
        switch ($type) {

            case "crear_pedido":

                if (!isset($config['cliente_id']) ||
                    !isset($config['producto_id']) ||
                    !isset($config['cantidad'])) {
                    throw new Exception("Config incompleta en crear_pedido");
                }

                $context['pedido'] = $config;
                return ["pedido_creado" => true];


            case "verificar_cliente":

                $cliente = Cliente::getById($context['pedido']['cliente_id']);

                if (!$cliente || !$cliente['activo']) {
                    throw new Exception("Cliente no válido");
                }

                $context['cliente'] = $cliente;
                return ["cliente_validado" => true];


            case "verificar_stock":

                $producto = Producto::getById($context['pedido']['producto_id']);

                if (!$producto) {
                    throw new Exception("Producto no encontrado");
                }

                if ($producto['stock'] < $context['pedido']['cantidad']) {
                    throw new Exception("Stock insuficiente");
                }

                $context['producto'] = $producto;
                return ["stock_ok" => true];


            case "calcular_total":

                $total = $context['producto']['precio'] *
                         $context['pedido']['cantidad'];

                $context['total'] = $total;
                return ["total" => $total];


            case "guardar_pedido":

                $pedidoId = Pedido::create(
                    $context['pedido']['cliente_id'],
                    $context['total']
                );

                $context['pedido_id'] = $pedidoId;
                return ["pedido_id" => $pedidoId];


            case "generar_factura":

                $facturaId = Factura::create($context['pedido_id']);

                $context['factura_id'] = $facturaId;
                return ["factura_id" => $facturaId];
        }
    }
}


