<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;

class PlayerLoginRequestPacket implements PacketInterface
{
    public const PACKET_ID = 1;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        if ($server->isPasswordEnabled) {
            Packet::writePacket($clientSocket, 37, chr(1));
        } else {
            Packet::writePacket($clientSocket, 3, chr(1));
        }
    }
}
