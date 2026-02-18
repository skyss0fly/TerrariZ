<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;
use TerrariZ\Utils\Logger;


class StatusPacket
{
    public static function send(
        $client,
        int $statusMax,
        string $text,
        Server $server,
        int $flags = 1
    ): void {
        $payload  = pack('V', $statusMax);
        $payload .= Packet::writeNetworkText($text);
        $payload .= pack('C', $flags);

        Packet::writePacket($client, 9, $payload, $server);
    }
}
