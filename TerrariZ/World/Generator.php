<?php
namespace TerrariZ\World;

class Generator
{
    public static function generate(World $world): void
    {
        $surface = $world->worldSurface;

        for ($x = 0; $x < $world->width; $x++) {

            $noise = rand(-3, 3);
            $ground = $surface + $noise;

            for ($y = $ground; $y < $world->height; $y++) {

                $tile = $world->getTile($x, $y);
                $tile->active = true;

                if ($y == $ground) {
                    $tile->type = 2; // grass
                } elseif ($y < $ground + 40) {
                    $tile->type = 0; // dirt
                } else {
                    $tile->type = 1; // stone
                }
            }
        }

        $world->spawnY = $surface - 5;
    }
}
