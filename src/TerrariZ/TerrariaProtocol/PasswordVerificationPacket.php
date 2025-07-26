<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;

class PasswordVerificationPacket implements PacketInterface
{
    public const PACKET_ID = 38;

    public function handle(array $data, $clientSocket, Server $server): void
    {
        $passwordLength = ord($data['data'][0]);
$password = substr($data['data'], 1, $passwordLength);

        if ($password === $server->serverPassword) {
            echo "Password accepted\n";

            $playerUid = 1; // TODO: assign dynamically
            $specialFlags = 0;
            $payload = pack('CC', $playerUid, $specialFlags);
            Packet::writePacket($clientSocket, 3, $payload);
        } else {
            echo "Password rejected\n";
            $reason = "Invalid Password" . chr(0);
			echo " ";
			echo $reason;
			echo " ";
			echo "bin2hex \n";
			echo bin2hex($reason);
			echo "Payload length: " . strlen($reason) . "\n";
            Packet::writePacket($clientSocket, 2, $reason);
        }
		
		
    }
}
