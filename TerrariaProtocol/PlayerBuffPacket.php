<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;
use TerrariZ\Player\Player;

class PlayerBuffPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        try {
            $playerId = $pkt->readByte();

            $buffCount = $pkt->readByte();
$buffs = [];

for ($i = 0; $i < $buffCount; $i++) {
    $buffs[$i] = $pkt->readUInt16();
}
            // Authoritative player = socket owner
            $player = $server->getPlayerBySocket($clientSocket);

            if (!$player) {
                Logger::log("debug", "PlayerBuff from unknown socket (pid=$playerId)");
                return;
            }

            // Update full buff state
            $player->setBuffs($buffs);

            Logger::log(
                "debug",
                "Player {$player->getId()} ({$player->getUsername()}) buffs updated"
            );

            // Broadcast to others
            foreach ($server->getPlayers() as $other) {
                if ($other !== $player) {
                    self::send($other, $player, $server);
                }
            }

        } catch (\Exception $e) {
            Logger::log("error", "Failed to parse PlayerBuffPacket: " . $e->getMessage());
        }
    }

    public static function send(Player $target, Player $source, Server $server): void
    {
        $payload = chr($source->getId());

        $buffs = $source->getBuffs(); // expect array[22]

        for ($i = 0; $i < 22; $i++) {
            $buffId = $buffs[$i] ?? 0;
            $payload .= Packet::writeUInt16($buffId);
        }

        Packet::writePacket(
            $server->getClientStream($target),
            50,
            $payload,
            $server
        );
    }
}
