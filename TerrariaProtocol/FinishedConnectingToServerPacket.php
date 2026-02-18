<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;

class FinishedConnectingToServerPacket
{
    public const PACKET_ID = 129;

    public static function send($client, Server $server): void
    {
        Packet::writePacket(
            $client,
            self::PACKET_ID,
            "",
            $server
        );
    }
}
