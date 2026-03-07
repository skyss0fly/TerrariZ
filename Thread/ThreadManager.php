<?php
namespace TerrariZ\Thread;

class ThreadManager {

    private array $workers = [];
    private int $nextId = 1;

    public function __construct(int $workerCount = 2) {
        for ($i = 0; $i < $workerCount; $i++) {
            $this->workers[] = new ThreadWorker($i);
        }
    }

    public function submit(Thread $thread): void {
        $worker = $this->selectLeastLoadedWorker();
        $worker->submit($thread);
    }

    private function selectLeastLoadedWorker(): ThreadWorker {
        usort($this->workers, fn($a,$b) => $a->getLoad() <=> $b->getLoad());
        return $this->workers[0];
    }

    public function tick(): void {
        foreach ($this->workers as $w) {
            $w->tick();
        }
    }

    public function createId(): int {
        return $this->nextId++;
    }
}
