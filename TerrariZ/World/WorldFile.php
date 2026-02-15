<?php
namespace TerrariZ\World;

class WorldFile
{
    public static function save(World $world, string $path): void
    {
        $fp = fopen($path, 'wb');

        fwrite($fp, pack('V', $world->width));
        fwrite($fp, pack('V', $world->height));
        fwrite($fp, pack('V', $world->spawnX));
        fwrite($fp, pack('V', $world->spawnY));
        fwrite($fp, pack('V', $world->worldSurface));
        fwrite($fp, pack('V', $world->rockLayer));

        fwrite($fp, pack('V', $world->worldId));
        fwrite($fp, $world->uuid);

        self::writeString($fp, $world->name);

        // tiles
        for ($x=0;$x<$world->width;$x++) {
            for ($y=0;$y<$world->height;$y++) {

                $tile = $world->tiles[$x][$y] ?? null;

                $active = $tile?->active ?? false;
                $type   = $tile?->type ?? 0;

                fwrite($fp, pack('C', $active ? 1 : 0));
                if ($active) {
                    fwrite($fp, pack('v', $type));
                }
            }
        }

        fclose($fp);
    }

    public static function load(string $path): World
    {
        $fp = fopen($path, 'rb');

        $width  = unpack('V', fread($fp,4))[1];
        $height = unpack('V', fread($fp,4))[1];
        $spawnX = unpack('V', fread($fp,4))[1];
        $spawnY = unpack('V', fread($fp,4))[1];
        $surface = unpack('V', fread($fp,4))[1];
        $rock = unpack('V', fread($fp,4))[1];

        $worldId = unpack('V', fread($fp,4))[1];
        $uuid = fread($fp,16);

        $name = self::readString($fp);

        $world = new World($width,$height,$name);
        $world->spawnX = $spawnX;
        $world->spawnY = $spawnY;
        $world->worldSurface = $surface;
        $world->rockLayer = $rock;
        $world->worldId = $worldId;
        $world->uuid = $uuid;

        for ($x=0;$x<$width;$x++) {
            for ($y=0;$y<$height;$y++) {

                $active = ord(fread($fp,1));

                if ($active) {
                    $type = unpack('v', fread($fp,2))[1];
                    $tile = $world->getTile($x,$y);
                    $tile->active = true;
                    $tile->type = $type;
                }
            }
        }

        fclose($fp);
        return $world;
    }

    private static function writeString($fp, string $str): void
    {
        fwrite($fp, pack('C', strlen($str)));
        fwrite($fp, $str);
    }

    private static function readString($fp): string
    {
        $len = ord(fread($fp,1));
        return fread($fp,$len);
    }
}
