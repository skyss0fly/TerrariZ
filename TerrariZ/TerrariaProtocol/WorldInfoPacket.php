<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\World\World;
use TerrariZ\Server;

class WorldInfoPacket implements PacketInterface
{
    public const PACKET_ID = 7;

    /**
     * Server → Client send
     */
    public static function send($client, World $world, Server $server): void
    {
        $payload = '';

        // world time
        $payload .= pack('V', $world->time);

        // flags
        $flags = 0;
        if ($world->isDay)    $flags |= 1;
        if ($world->bloodMoon) $flags |= 2;
        if ($world->eclipse)   $flags |= 4;

        $payload .= pack('C', $flags);
        $payload .= pack('C', $world->moonPhase);

        // dimensions
        $payload .= pack('v', $world->width);
        $payload .= pack('v', $world->height);

        // spawn
        $payload .= pack('v', $world->spawnX);
        $payload .= pack('v', $world->spawnY);

        // layers
        $payload .= pack('v', $world->worldSurface);
        $payload .= pack('v', $world->rockLayer);

        // id + name
        $payload .= pack('V', $world->worldId);
        $payload .= chr(strlen($world->name)) . $world->name;

        // uuid (16 bytes expected)
        $payload .= $world->uuid;

        // generator version (Terraria uses UInt64 LE)
        $payload .= pack('P', $world->generatorVersion);

        // backgrounds
        $payload .= pack('C', $world->moonType);
        $payload .= pack('C', $world->treeBG);
        $payload .= pack('C', $world->corruptBG);
        $payload .= pack('C', $world->jungleBG);
        $payload .= pack('C', $world->snowBG);

        Packet::writePacket($client, self::PACKET_ID, $payload, $server);
    }

    /**
     * Client → Server handler
     * (packet 7 is never sent by client)
     */
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $world = $server->getWorld();

       $this->send($clientSocket, $world, $server);
    }
}
