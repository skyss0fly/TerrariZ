<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Player\Player;

class CompleteConnectionAndSpawnPacket
{
    public const PACKET_ID = 49;

    public static function send($client, Player $player, Server $server): void
    {
        $payload = chr($player->getId());

        Packet::writePacket(
            $client,
            self::PACKET_ID,
            $payload,
            $server
        );
    }
}
