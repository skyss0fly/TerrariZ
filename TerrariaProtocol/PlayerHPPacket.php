<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Player\Player;

class PlayerHPPacket implements PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        $playerId = $pkt->readByte();
        $hp       = $pkt->readInt16();
        $maxHp    = $pkt->readInt16();

        $player = $server->getPlayerBySocket($clientSocket);

        $player->setCurrentHP($hp);
        $player->setMaxHP($maxHp);

        // broadcast to others
        foreach ($server->getPlayers() as $other) {
            if ($other !== $player) {
                self::send($other, $player, $server);
            }
        }
    }

    public static function send(Player $target, Player $source, Server $server): void
    {
        $payload =
            chr($source->getId()) .
            Packet::writeInt16($source->getCurrentHP()) .
            Packet::writeInt16($source->getMaxHP());

        Packet::writePacket(
            $server->getClientStream($target),
            16,
            $payload,
            $server
        );
    }
}
