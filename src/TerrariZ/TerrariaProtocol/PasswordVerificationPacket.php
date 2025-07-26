<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;
use TerrariZ\Utils\Logger;

class PasswordVerificationPacket implements PacketInterface
{
    public const PACKET_ID = 38;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        $passwordLength = ord($data['data'][0]);
$password = substr($data['data'], 1, $passwordLength);

        if ($password === $server->serverPassword) {
            Logger::log("info","Password accepted\n");

            $playerUid = 1; // TODO: assign dynamically
            $specialFlags = 0;
            $payload = pack('CC', $playerUid, $specialFlags);
            Packet::writePacket($clientSocket, 3, $payload);
        } else {
            Logger::log("info","Password rejected\n");
            $reason = "Invalid Password" . chr(0);
			if ($server->isDebugEnabled) {
			Logger::log("debug", " ");
			Logger::log("debug", $reason);
			Logger::log("debug", " ");
			Logger::log("debug", "bin2hex \n");
			logger::log("debug", bin2hex($reason));
			logger::log("debug","Payload length: " . strlen($reason) . "\n");
			}
            Packet::writePacket($clientSocket, 2, $reason);
        }
		
		
    }
}
