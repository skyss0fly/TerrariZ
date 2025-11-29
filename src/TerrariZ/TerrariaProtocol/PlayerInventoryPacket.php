<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class PlayerInventoryPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        // Identify player by socket
        $player = $server->getPlayerBySocket($clientSocket);
        if (!$player) {
            Logger::log("debug", "Packet 16 received for unknown socket {$clientSocket}");
            return;
        }

        Logger::log("debug", "Parsing inventory for player {$player->getUsername()}");

        $inventory = [];

        // Safely loop through remaining bytes
        while (true) {
            try {
                // Each inventory slot: 2 bytes ID, 2 bytes stack, 1 byte prefix
                if (strlen($data['data']) - $pkt->getPos() < 5) {
                    break; // no more complete items
                }

                $itemId = $pkt->readInt16();
                $stack = $pkt->readInt16();
                $prefix = $pkt->readByte();

                $inventory[] = [
                    'itemId' => $itemId,
                    'stack' => $stack,
                    'prefix' => $prefix,
                ];

                Logger::log(
                    "debug",
                    "Item " . (count($inventory)-1) . " => ID: $itemId, Stack: $stack, Prefix: $prefix"
                );
            } catch (\Exception $e) {
                Logger::log("error", "Error parsing inventory: ".$e->getMessage());
                break;
            }
        }

        // $player->setInventory($inventory);

        Logger::log("debug", "Finished parsing inventory for player {$player->getUsername()}");
    }
}
