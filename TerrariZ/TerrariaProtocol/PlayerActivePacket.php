<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;

class PlayerActivePacket
{
    public static function send($client, Server $server): void
    {
        $player = $server->getPlayerBySocket($client);
        if (!$player) return;

        $payload  = chr($player->getId()); // player id
        $payload .= chr(1);                // active = true

        Packet::writePacket($client, 8, $payload, $server);
    }
}
