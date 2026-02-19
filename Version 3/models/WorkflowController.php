<?php
require_once __DIR__ . '/../models/Workflow.php';
require_once __DIR__ . '/../config/database.php';

class WorkflowController {

    public static function save($data) {
        return Workflow::saveWorkflow($data);
    }

    public static function addEdge($data) {

        $db = Database::getConnection();

        try {

            $stmt = $db->prepare("
                INSERT INTO workflow_edges (workflow_id, from_node, to_node)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $data['workflow_id'],
                $data['from'],
                $data['to']
            ]);

            return [
                "ok" => true,
                "message" => "Conexión guardada correctamente en base de datos"
            ];

        } catch (Exception $e) {

            return [
                "ok" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}

