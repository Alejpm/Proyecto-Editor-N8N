<?php
require_once __DIR__ . '/../core/GraphEngine.php';
require_once __DIR__ . '/../core/NodeExecutor.php';
require_once __DIR__ . '/../models/Workflow.php';

class ExecuteController {

    public static function run($workflow_id) {

        $workflow = Workflow::getFullWorkflow($workflow_id);

        $nodes = $workflow['nodes'];
        $edges = $workflow['edges'];

        $executionOrder = GraphEngine::topologicalSort($nodes, $edges);

        $context = [];
        $results = [];

        foreach ($executionOrder as $nodeId) {
            $node = array_filter($nodes, fn($n) => $n['node_id'] == $nodeId);
            $node = array_values($node)[0];

            try {
                $results[$nodeId] = NodeExecutor::execute($node, $context);
            } catch (Exception $e) {
                $results[$nodeId] = ["error" => $e->getMessage()];
                break;
            }
        }

        return $results;
    }
}

