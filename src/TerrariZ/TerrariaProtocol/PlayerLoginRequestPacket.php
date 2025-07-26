<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;
use TerrariZ\Utils\Logger;

class PlayerLoginRequestPacket implements PacketInterface
{
    public const PACKET_ID = 1;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        if ($server->isPasswordEnabled) {
			if ($server->isDebugEnabled) {
				Logger::log("debug", "Sent the Password Request Packet to the Client");
			}
            Packet::writePacket($clientSocket, 37, chr(1));
        } else {
			if ($server->isDebugEnabled) {
				Logger::log("debug", "Skipped the Password Request Packet.");
			}
            Packet::writePacket($clientSocket, 3, chr(1));
        }
    }
}
