<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class PlayerSpawnPacket implements PacketInterface
{
    public const PACKET_ID = 12;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        $playerId = $pkt->readByte();
$spawnX   = $pkt->readInt16();
$spawnY   = $pkt->readInt16();
$respawn  = $pkt->readInt32();
$context  = $pkt->readByte();

$player = $server->getPlayerBySocket($clientSocket);
if (!$player) return;

$world = $server->getWorld();

if ($spawnX === 65535 || $spawnY === 65535) {
    $spawnX = $world->spawnX;
    $spawnY = $world->spawnY;
}

$player->setPosition($spawnX, $spawnY);
$player->setDead($respawn > 0);

Logger::log(
    "debug",
    "Spawn resolved: {$player->getUsername()} at ($spawnX,$spawnY)"
);

//CompleteConnectionAndSpawnPacket::send($clientSocket, $player, $server);
FinishedConnectingToServerPacket::send($clientSocket, $server);
    }
}
