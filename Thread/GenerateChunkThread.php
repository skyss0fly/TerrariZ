<?php
namespace TerrariZ\Thread;

class GenerateChunkThread extends Thread {

    private int $chunkX;
    private int $chunkY;
    private string $result;

    public function __construct(int $id, int $x, int $y) {
        parent::__construct($id);
        $this->chunkX = $x;
        $this->chunkY = $y;
    }

    public function run(): void {
        $this->result = WorldGen::generateChunk($this->chunkX, $this->chunkY);
        $this->complete = true;
    }

    public function getResult(): string {
        return $this->result;
    }
}
