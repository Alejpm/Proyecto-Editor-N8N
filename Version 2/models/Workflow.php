<?php
require_once __DIR__ . '/../config/database.php';

class Workflow {

    /* ================================
       GUARDAR WORKFLOW COMPLETO
       ================================ */
    public static function saveWorkflow($data) {

        $db = Database::getConnection();
        $db->beginTransaction();

        try {

            // 1️⃣ Crear registro principal
            $stmt = $db->prepare("INSERT INTO workflows (nombre) VALUES (?)");
            $stmt->execute([$data['name']]);
            $workflow_id = $db->lastInsertId();

            // 2️⃣ Guardar nodos
            foreach ($data['nodes'] as $node) {

                $stmt = $db->prepare("
                    INSERT INTO workflow_nodes
                    (workflow_id, node_id, type, pos_x, pos_y, config)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $workflow_id,
                    $node['id'],
                    $node['type'],
                    $node['x'],
                    $node['y'],
                    json_encode($node['config'])
                ]);
            }

            // 3️⃣ Guardar conexiones
            foreach ($data['edges'] as $edge) {

                $stmt = $db->prepare("
                    INSERT INTO workflow_edges
                    (workflow_id, from_node, to_node)
                    VALUES (?, ?, ?)
                ");

                $stmt->execute([
                    $workflow_id,
                    $edge['from'],
                    $edge['to']
                ]);
            }

            $db->commit();

            return [
                "ok" => true,
                "workflow_id" => $workflow_id
            ];

        } catch (Exception $e) {

            $db->rollBack();

            return [
                "ok" => false,
                "error" => $e->getMessage()
            ];
        }
    }

    /* ================================
       LISTAR TODOS LOS WORKFLOWS
       ================================ */
    public static function getAll() {

        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT id, nombre, created_at
            FROM workflows
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================================
       OBTENER WORKFLOW COMPLETO
       (nodos + conexiones)
       ================================ */
    public static function getFullWorkflow($id) {

        $db = Database::getConnection();

        // Obtener nodos
        $stmt = $db->prepare("
            SELECT node_id, type, pos_x, pos_y, config
            FROM workflow_nodes
            WHERE workflow_id = ?
        ");
        $stmt->execute([$id]);
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener conexiones
        $stmt = $db->prepare("
            SELECT from_node, to_node
            FROM workflow_edges
            WHERE workflow_id = ?
        ");
        $stmt->execute([$id]);
        $edges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "nodes" => $nodes,
            "edges" => $edges
        ];
    }

    /* ================================
       ELIMINAR WORKFLOW (opcional)
       ================================ */
    public static function delete($id) {

        $db = Database::getConnection();
        $db->beginTransaction();

        try {

            $stmt = $db->prepare("DELETE FROM workflow_edges WHERE workflow_id = ?");
            $stmt->execute([$id]);

            $stmt = $db->prepare("DELETE FROM workflow_nodes WHERE workflow_id = ?");
            $stmt->execute([$id]);

            $stmt = $db->prepare("DELETE FROM workflows WHERE id = ?");
            $stmt->execute([$id]);

            $db->commit();

            return ["ok" => true];

        } catch (Exception $e) {

            $db->rollBack();

            return [
                "ok" => false,
                "error" => $e->getMessage()
            ];
        }
    }
}

