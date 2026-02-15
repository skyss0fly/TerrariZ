<?php
namespace TerrariZ\TerrariaProtocol;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;
use TerrariZ\Utils\Logger;
use TerrariZ\World\World;

class SpawnPlayerPacket 
{
    public static function send($client, World $world, Server $server): void
    {
        $payload  = pack('v', $world->spawnX);
        $payload .= pack('v', $world->spawnY);

        Packet::writePacket($client, 11, $payload, $server);
    }
}
