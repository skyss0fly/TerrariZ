<?php
namespace TerrariZ\World;

class World
{
    public int $width;
    public int $height;

    public int $spawnX;
    public int $spawnY;

    public int $worldSurface;
    public int $rockLayer;

    public int $time = 0;
    public bool $isDay = true;
    public bool $bloodMoon = false;
    public bool $eclipse = false;
    public int $moonPhase = 0;

    public int $worldId;
    public string $name;
    public string $uuid; // 16-byte binary
    public int $generatorVersion = 1;

    public int $moonType = 0;
    public int $treeBG = 0;
    public int $corruptBG = 0;
    public int $jungleBG = 0;
    public int $snowBG = 0;

    /** @var array<int,array<int,Tile>> */
    public array $tiles = [];

    public function __construct(int $width, int $height, string $name = "TerrariZ World")
    {
        $this->width = $width;
        $this->height = $height;
        $this->name = $name;

        $this->spawnX = intdiv($width, 2);
        $this->spawnY = intdiv($height, 2);

        $this->worldSurface = intdiv($height, 2);
        $this->rockLayer = $this->worldSurface + 200;

        $this->worldId = random_int(1, PHP_INT_MAX);
        $this->uuid = random_bytes(16);
    }

    public function getTile(int $x, int $y): Tile
    {
        return $this->tiles[$x][$y] ??= new Tile();
    }
}
