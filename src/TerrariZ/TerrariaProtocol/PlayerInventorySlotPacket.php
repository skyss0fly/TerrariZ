<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class PlayerInventorySlotPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        // Identify player by socket
        $player = $server->getPlayerBySocket($clientSocket);
        if (!$player) {
            Logger::log("debug", "Packet 5 received for unknown socket {$clientSocket}");
            return;
        }

        Logger::log("debug", "Parsing inventory slot update for player {$player->getUsername()}");

        // Read according to Packet 5 structure
        $playerId = $pkt->readByte();  // 1 byte: player ID
        $slotId   = $pkt->readByte();  // 1 byte: slot index
        $stack    = $pkt->readInt16(); // 2 bytes: stack size
        $prefix   = $pkt->readByte();  // 1 byte: prefix
        $netId    = $pkt->readInt16(); // 2 bytes: net item ID

        // Store slot in inventory
       $player->setInventorySlot($slotId, $netId, $stack, $prefix);


        Logger::log(
            "debug",
            "Player {$player->getUsername()} slot {$slotId} => NetID: $netId, Stack: $stack, Prefix: $prefix"
        );
    }
}
