<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;
use TerrariZ\TerrariaProtocol\StatusPacket;
use TerrariZ\TerrariaProtocol\SendSectionPacket;


class EssentialTilesPacket implements PacketInterface
{
    public const PACKET_ID = 8;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        try {
            $spawnX = $pkt->readInt32();
            $spawnY = $pkt->readInt32();

            if ($server->isDebugEnabled) {
                Logger::log(
                    "debug",
                    "Client requested essential tiles at spawn ($spawnX, $spawnY)"
                );
            }
$player = $server->getPlayerBySocket($clientSocket);
if (!$player) return;

if ($player->hasReceivedWorld()) {
    return; // ignore repeated requests
}

$player->setReceivedWorld(true);
            $world = $server->getWorld();
            $width  = $world->getWidth();
            $height = $world->getHeight();

            // ---- STATUS ----
            $sectionsX = intdiv($width, 200);
            $sectionsY = intdiv($height, 150);
            $statusMax = $sectionsX * $sectionsY;

            StatusPacket::send(
                $clientSocket,
                $statusMax,
                "Receiving world data",
                $server
            );

            // ---- SPAWN TILE AREA (vanilla ~400×300) ----
            SendSectionPacket::send(
                $clientSocket,
                $world,
                $spawnX - 200,
                $spawnY - 150,
                400,
                300,
                $server
            );

            // ---- STREAM FULL WORLD SECTIONS ----
            for ($sx = 0; $sx < $width; $sx += 200) {
                for ($sy = 0; $sy < $height; $sy += 150) {
                    SendSectionPacket::send(
                        $clientSocket,
                        $world,
                        $sx,
                        $sy,
                        200,
                        150,
                        $server
                    );
                }
            }

           CompleteConnectionAndSpawnPacket::send($clientSocket, $player, $server);

        } catch (\Throwable $e) {
            Logger::log("error", "Failed parsing Packet 8: " . $e->getMessage());
        }
    }
}
