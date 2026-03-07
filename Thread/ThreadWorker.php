<?php
namespace TerrariZ\Thread;

class ThreadWorker {

    private int $id;
    private array $queue = [];
    private bool $running = true;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function submit(Thread $thread): void {
        $this->queue[] = $thread;
    }

    public function tick(): void {
        if (!$this->running) return;

        if (empty($this->queue)) return;

        /** @var Thread $job */
        $job = array_shift($this->queue);

        $job->run();
    }

    public function stop(): void {
        $this->running = false;
    }

    public function getLoad(): int {
        return count($this->queue);
    }
}
