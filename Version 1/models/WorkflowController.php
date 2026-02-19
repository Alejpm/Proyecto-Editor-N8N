<?php
require_once __DIR__ . '/../models/Workflow.php';

class WorkflowController {

    public static function save($data) {
        return Workflow::saveWorkflow($data);
    }

    public static function list() {
        return Workflow::getAll();
    }
}

