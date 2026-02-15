<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class EssentialTilesPacket implements PacketInterface
{
    public const PACKET_ID = 8;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        try {
            // Client sends spawn tile coordinates (Int32 LE)
            $spawnX = $pkt->readInt32();
            $spawnY = $pkt->readInt32();

            if ($server->isDebugEnabled) {
                Logger::log(
                    "debug",
                    "Client requested essential tiles at spawn ($spawnX, $spawnY)"
                );
            }

        } catch (\Throwable $e) {
            Logger::log("error", "Failed parsing Packet 8: " . $e->getMessage());
        }
    }
}
