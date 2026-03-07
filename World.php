<?php
namespace TerrariZ\World;

use TerrariZ\TerrariaProtocol\Packet;

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
public int $moonType = 0;
public int $treeBG = 0;
public int $corruptBG = 0;
public int $jungleBG = 0;
public int $snowBG = 0;

    public int $worldId;
    public string $name;
    public string $uuid;
    public int $generatorVersion = 1;

    /** @var Tile[][] */
    public array $tiles = [];

    /** Cached section packets */
    private array $sectionCache = [];

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

        // 🔥 Preallocate full tile grid (major speedup)
        for ($x = 0; $x < $width; $x++) {
            $col = [];
            for ($y = 0; $y < $height; $y++) {
                $col[$y] = new Tile();
            }
            $this->tiles[$x] = $col;
        }
    }

    public function getTile(int $x, int $y): Tile
    {
        return $this->tiles[$x][$y];
    }

    /* ================================
       RLE TILE SERIALIZATION
       ================================ */

    public function serializeTiles(int $startX, int $startY, int $w, int $h): string
    {
        $out = '';

        for ($x = $startX; $x < $startX + $w; $x++) {

            $y = $startY;

            while ($y < $startY + $h) {

                $tile = $this->tiles[$x][$y];
                $run  = 1;

                while (
                    $y + $run < $startY + $h &&
                    $this->tilesEqual($tile, $this->tiles[$x][$y + $run])
                ) {
                    $run++;
                }

                $out .= $tile->serializeWithRun($run);

                $y += $run;
            }
        }

        return $out;
    }

    private function tilesEqual(Tile $a, Tile $b): bool
    {
        return
            $a->active === $b->active &&
            $a->type === $b->type &&
            $a->wall === $b->wall &&
            $a->wallType === $b->wallType &&
            $a->liquid === $b->liquid &&
            $a->liquidType === $b->liquidType &&
            $a->frameX === $b->frameX &&
            $a->frameY === $b->frameY;
    }

    /* ================================
       SECTION PACKET BUILD
       ================================ */

    public function buildSectionPacket(
        int $startX,
        int $startY,
        int $width,
        int $height
    ): string {

        $key = "$startX:$startY:$width:$height";

        if (isset($this->sectionCache[$key])) {
            return $this->sectionCache[$key];
        }

        $tileData = $this->serializeTiles($startX, $startY, $width, $height);

        $payload =
            Packet::writeBool(false) .
            Packet::writeInt32($startX) .
            Packet::writeInt32($startY) .
            Packet::writeInt16($width) .
            Packet::writeInt16($height) .
            $tileData .
            Packet::writeInt16(0) .
            Packet::writeInt16(0) .
            Packet::writeInt16(0);

        $packet = Packet::buildPacket(10, $payload);

        $this->sectionCache[$key] = $packet;

        return $packet;
    }
}
