<?php
namespace TerrariZ\World;

class Tile
{
    public bool $active = false;
    public int $type = 0;

    public bool $wall = false;
    public int $wallType = 0;

    public int $liquid = 0;
    public int $liquidType = 0;

    public int $frameX = 0;
    public int $frameY = 0;

    /**
     * Serialize ONE tile (Terraria format)
     */
    public function serializeWithRun(int $run): string
{
    $flags = 0;

    if ($this->active) $flags |= 0x02;
    if ($this->wall)   $flags |= 0x04;
    if ($this->liquid > 0) $flags |= 0x08;
    if ($this->frameX !== 0 || $this->frameY !== 0) $flags |= 0x10;

    $out = "";

    if ($run > 1) {
        if ($run < 256) {
            $flags |= 0x40; // 1-byte RLE
        } else {
            $flags |= 0x80; // 2-byte RLE
        }
    }

    $out .= chr($flags);

    if ($this->active) {
        $out .= chr($this->type);
    }

    if ($flags & 0x10) {
        $out .= pack("v", $this->frameX);
        $out .= pack("v", $this->frameY);
    }

    if ($this->wall) {
        $out .= chr($this->wallType);
    }

    if ($this->liquid > 0) {
        $out .= chr($this->liquid);
        $out .= chr($this->liquidType);
    }

    if ($run > 1) {
        if ($run < 256) {
            $out .= chr($run - 1);
        } else {
            $out .= pack("v", $run - 1);
        }
    }

    return $out;
}
}
