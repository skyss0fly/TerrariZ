<?php
namespace TerrariZ\World;

class Generator
{
    public static function generate(World $world): void
    {
        $width  = $world->width;
        $height = $world->height;

        $surfaceBase = $world->worldSurface;
        $rockLayer   = $world->rockLayer;

        for ($x = 0; $x < $width; $x++) {

            // simple terrain variation
            $surface = $surfaceBase + (int)(sin($x / 40) * 8);

            // SKY (air) — already default

            // DIRT layer
            for ($y = $surface; $y < $surface + 20 && $y < $height; $y++) {
                $t = $world->tiles[$x][$y];
                $t->active = true;
                $t->type = 0; // dirt
            }

            // STONE layer
            for ($y = $surface + 20; $y < $height; $y++) {
                $t = $world->tiles[$x][$y];
                $t->active = true;
                $t->type = 1; // stone
            }
        }
    }
}
