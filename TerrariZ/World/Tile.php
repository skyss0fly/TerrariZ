<?php
namespace TerrariZ\World;

class Tile
{
    public bool $active = false;
    public int $type = 0;

    public bool $wall = false;
    public int $wallType = 0;

    public int $liquid = 0;   // 0–255
    public int $liquidType = 0; // 0 water,1 lava,2 honey

    public int $frameX = 0;
    public int $frameY = 0;
}
