<?php

namespace App\Service;

use App\Entity\Node;
use SplPriorityQueue;

readonly class AStarService
{
    public function find_path(
        Node     $start,
        Node     $goal,
        array    $allNodes
    ): ?array
    {
        $openSet = new SplPriorityQueue();
        $openSet->insert($start, 0);

        $cameFrom = [];
        $gScore = [$start->getId() => 0];
        $fScore = [$start->getId() => $this->heuristic($start, $goal)];

        while (!$openSet->isEmpty()) {
            $current = $openSet->extract();

            if ($current === $goal) {
                return $this->reconstructPath($cameFrom, $current);
            }

            /* @var Node $node */
            foreach ($allNodes as $node) {
                $neighbor = $node->getNodes()->contains($current);

                if (!$neighbor) {
                    continue;
                }

                $tentativeGScore = $gScore[$current->getId()] + 1;

                if (!isset($gScore[$node->getId()]) || $tentativeGScore < $gScore[$node->getId()]) {
                    $cameFrom[$node->getId()] = $current;
                    $gScore[$node->getId()] = $tentativeGScore;
                    $fScore[$node->getId()] = $tentativeGScore / 10 + $this->heuristic($node, $goal);

                    if (!$this->inOpenSet($openSet, $node)) {
                        $openSet->insert($node, -$fScore[$node->getId()]);
                    }
                }
            }
        }

        return [];
    }

    function heuristic(Node $a, Node $b): float
    {
        return sqrt(pow($a->getPoint()->getX() - $b->getPoint()->getX(), 2) + pow($a->getPoint()->getX() - $b->getPoint()->getY(), 2));
    }

    function reconstructPath(array $cameFrom, Node $current): array
    {
        $totalPath = [$current->getPoint()];
        while (isset($cameFrom[$current->getId()])) {
            $current = $cameFrom[$current->getId()];
            $totalPath[] = $current->getPoint();
        }
        return array_reverse($totalPath);
    }

    function inOpenSet(SplPriorityQueue $openSet, Node $node): bool
    {
        foreach (clone $openSet as $item) {
            if ($item === $node) {
                return true;
            }
        }
        return false;
    }
}