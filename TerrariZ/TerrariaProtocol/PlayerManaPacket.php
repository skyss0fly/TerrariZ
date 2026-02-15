<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class PlayerManaPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        try {
            // Player UID (index assigned by server)
            $playerId = $pkt->readByte();

            // Current Mana (Int16)
            $currentMana = $pkt->readInt16();

            // Max Mana (Int16)
            $maxMana = $pkt->readInt16();

            // Optional: Read extra bytes if present (some packets have padding)
            $remaining = strlen($data['data']) - $pkt->getPos();
            $extraData = null;
            if ($remaining > 0) {
                $extraData = substr($data['data'], $pkt->getPos());
            }

            // Find player
            $player = $server->getPlayers()[$playerId] ?? null;
            if ($player) {
                $player->setCurrentMana($currentMana);
                $player->setMaxMana($maxMana);

                Logger::log(
                    "debug",
                    "Player {$player->getId()} ({$player->getUsername()}) mana updated: $currentMana / $maxMana"
                );
            } else {
                Logger::log(
                    "debug",
                    "Received mana packet for unknown player ID $playerId"
                );
            }

        } catch (\Exception $e) {
            Logger::log("error", "Failed to parse PlayerManaPacket: " . $e->getMessage());
        }
    }
}
