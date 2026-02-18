<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\Utils\Logger;

class LoadNetModulePacket implements PacketInterface
{
    public const PACKET_ID = 82;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        $pkt = new Packet($data['data']);

        $moduleId = $pkt->readUInt16();
        $remaining = substr($data['data'], $pkt->getPos());

        if ($server->isDebugEnabled) {
            Logger::log(
                "debug",
                "NetModule {$moduleId} len=" . strlen($remaining)
            );
        }

        // Minimal behavior: ignore
        // Later: handle modules (chat, pvp, etc.)
    }
}
