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

    public function serialize(): string
    {
        $out = "";

        $flags1 = 0;

        if ($this->active) $flags1 |= 0x02;
        if ($this->wall)   $flags1 |= 0x04;
        if ($this->liquid > 0) $flags1 |= 0x08;
        if ($this->frameX !== 0 || $this->frameY !== 0) $flags1 |= 0x10;

        $out .= chr($flags1);

        if ($this->active) {
            $out .= chr($this->type);
        }

        if ($flags1 & 0x10) {
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

        return $out;
    }
}
