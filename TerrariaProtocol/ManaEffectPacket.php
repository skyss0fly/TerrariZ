<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;
use TerrariZ\Player\Player;

class ManaEffectPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        try {
            $playerId  = $pkt->readByte();
            $manaDelta = $pkt->readInt16(); // can be positive or negative

            // Authoritative player = socket owner
            $player = $server->getPlayerBySocket($clientSocket);

            if (!$player) {
                Logger::log("debug", "ManaEffect from unknown socket (pid=$playerId)");
                return;
            }

            Logger::log(
                "debug",
                "ManaEffect: Player {$player->getId()} ({$player->getUsername()}) delta=$manaDelta"
            );

            // Broadcast to others so they see the effect
            foreach ($server->getPlayers() as $other) {
                if ($other !== $player) {
                    self::send($other, $player, $manaDelta, $server);
                }
            }

        } catch (\Exception $e) {
            Logger::log("error", "Failed to parse ManaEffectPacket: " . $e->getMessage());
        }
    }

    public static function send(Player $target, Player $source, int $manaDelta, Server $server): void
    {
        $payload =
            chr($source->getId()) .
            Packet::writeInt16($manaDelta);

        Packet::writePacket(
            $server->getClientStream($target),
            43,
            $payload,
            $server
        );
    }
}
