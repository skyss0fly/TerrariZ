<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;

class WorldTilePacket
{
    public static function sendInitial($client, Server $server): void
    {
        $world = $server->getWorld();

        $sectionW = 200;
        $sectionH = 150;

        for ($sx = 0; $sx < $world->width; $sx += $sectionW) {
            for ($sy = 0; $sy < $world->height; $sy += $sectionH) {

                $payload  = pack('v', $sx);
                $payload .= pack('v', $sy);
                $payload .= pack('v', $sectionW);
                $payload .= pack('v', $sectionH);

                $tileCount = $sectionW * $sectionH;

                // RLE: all air tiles
                while ($tileCount > 0) {
                    $run = min(255, $tileCount);

                    $payload .= chr(0);   // flags = air
                    $payload .= chr($run); // RLE count

                    $tileCount -= $run;
                }

                Packet::writePacket($client, 49, $payload, $server);
            }
        }

        Packet::writePacket($client, 52, '', $server);
    }
}
