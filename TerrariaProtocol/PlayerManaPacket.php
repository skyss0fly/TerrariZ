<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;
use TerrariZ\Player\Player;

class PlayerManaPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        try {
            // Packet fields
            $playerId    = $pkt->readByte();
            $currentMana = $pkt->readInt16();
            $maxMana     = $pkt->readInt16();

            // Optional trailing data (protocol padding / future fields)
            $remaining = strlen($data['data']) - $pkt->getPos();
            $extraData = $remaining > 0 ? substr($data['data'], $pkt->getPos()) : null;

            // Authoritative player = socket owner
            $player = $server->getPlayerBySocket($clientSocket);

            if (!$player) {
                Logger::log("debug", "Mana packet from unknown socket (pid=$playerId)");
                return;
            }

            // Update state
            $player->setCurrentMana($currentMana);
            $player->setMaxMana($maxMana);

            Logger::log(
                "debug",
                "Player {$player->getId()} ({$player->getUsername()}) mana updated: $currentMana / $maxMana"
            );

            // Broadcast to other players
            foreach ($server->getPlayers() as $other) {
                if ($other !== $player) {
                    self::send($other, $player, $server);
                }
            }

        } catch (\Exception $e) {
            Logger::log("error", "Failed to parse PlayerManaPacket: " . $e->getMessage());
        }
    }

    public static function send(Player $target, Player $source, Server $server): void
    {
        $payload =
            chr($source->getId()) .
            Packet::writeInt16($source->getCurrentMana()) .
            Packet::writeInt16($source->getMaxMana());

        Packet::writePacket(
            $server->getClientStream($target),
            42, // PlayerMana packet id
            $payload,
            $server
        );
    }
}
