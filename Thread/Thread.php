<?php
namespace TerrariZ\Thread;

abstract class Thread {

    protected int $id;
    protected bool $complete = false;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function getId(): int {
        return $this->id;
    }

    public function isComplete(): bool {
        return $this->complete;
    }

    abstract public function run(): void;

    abstract public function getResult(): mixed;
}
