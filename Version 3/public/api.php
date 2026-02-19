<?php
require_once __DIR__ . '/../controllers/WorkflowController.php';
require_once __DIR__ . '/../controllers/ExecuteController.php';

header("Content-Type: application/json");

$action = $_GET['action'] ?? null;

switch ($action) {

    case "save":
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(WorkflowController::save($data));
        break;

    case "addEdge":
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(WorkflowController::addEdge($data));
        break;

    case "run":
        $id = $_GET['id'];
        echo json_encode(ExecuteController::run($id));
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}

