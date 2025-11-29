<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class SyncUUIDPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        // Skip the '$' prefix (Terraria UUID packets start with '$')
        $pkt->readByte();

        // Read exactly 36 bytes (UUID ASCII characters)
        $uuid = '';
        for ($i = 0; $i < 36; $i++) {
            $uuid .= chr($pkt->readByte());
        }

        Logger::log("debug", "UUID received: $uuid");

        // Identify player by their socket
        $player = $server->getPlayerBySocket($clientSocket);
        if ($player) {
            $player->setUUID($uuid); // <-- FIXED
            Logger::log(
                "debug",
                "UUID assigned to player {$player->getId()} ({$player->getUsername()})"
            );
        } else {
            Logger::log("debug", "UUID received for unknown socket {$clientSocket}");
        }
    }
}
