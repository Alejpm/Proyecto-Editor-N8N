<?php
class GraphEngine {

    public static function topologicalSort($nodes, $edges) {
        $graph = [];
        $inDegree = [];

        foreach ($nodes as $node) {
            $graph[$node['node_id']] = [];
            $inDegree[$node['node_id']] = 0;
        }

        foreach ($edges as $edge) {
            $graph[$edge['from_node']][] = $edge['to_node'];
            $inDegree[$edge['to_node']]++;
        }

        $queue = [];
        foreach ($inDegree as $node => $degree) {
            if ($degree == 0) {
                $queue[] = $node;
            }
        }

        $sorted = [];
        while (!empty($queue)) {
            $current = array_shift($queue);
            $sorted[] = $current;

            foreach ($graph[$current] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] == 0) {
                    $queue[] = $neighbor;
                }
            }
        }

        return $sorted;
    }
}

