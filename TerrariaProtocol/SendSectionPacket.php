<?php

namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;
use TerrariZ\World\World;
use TerrariZ\Utils\Logger;

class SendSectionPacket
{
    public static function send(
        $clientSocket,
        World $world,
        int $startX,
        int $startY,
        int $width,
        int $height,
        Server $server,
        bool $compressed = true
    ): void {
        try {
            // --- serialize tiles ---
            $tileData = $world->serializeTiles($startX, $startY, $width, $height);

            // --- serialize chests ---
            $chests = $world->getChestsInRegion($startX, $startY, $width, $height);
            $chestData = $world->serializeChests($chests);

            // --- serialize signs ---
            $signs = $world->getSignsInRegion($startX, $startY, $width, $height);
            $signData = $world->serializeSigns($signs);

            // --- serialize tile entities ---
            $tileEntities = $world->getTileEntitiesInRegion($startX, $startY, $width, $height);
            $tileEntityData = $world->serializeTileEntities($tileEntities);

            // optional compression (vanilla uses deflate)
            if ($compressed) {
                $tileData = gzdeflate($tileData);
                $chestData = gzdeflate($chestData);
                $signData = gzdeflate($signData);
                $tileEntityData = gzdeflate($tileEntityData);
            }

            $payload =
                Packet::writeBool($compressed) .
                Packet::writeInt32($startX) .
                Packet::writeInt32($startY) .
                Packet::writeInt16($width) .
                Packet::writeInt16($height) .
                $tileData .
                Packet::writeInt16(count($chests)) .
                $chestData .
                Packet::writeInt16(count($signs)) .
                $signData .
                Packet::writeInt16(count($tileEntities)) .
                $tileEntityData;

            Packet::writePacket(
                $clientSocket,
                10,
                $payload,
                $server
            );

        } catch (\Exception $e) {
            Logger::log("error", "SendSectionPacket failed: " . $e->getMessage());
        }
    }
}
