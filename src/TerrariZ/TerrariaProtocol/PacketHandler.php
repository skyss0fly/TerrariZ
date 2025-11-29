<?php

namespace TerrariZ\TerrariaProtocol;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\PlayerLoginRequestPacket;
use TerrariZ\TerrariaProtocol\PasswordVerificationPacket;
use TerrariZ\TerrariaProtocol\PlayerCreationPacket;
use TerrariZ\Utils\Logger;
use TerrariZ\TerrariaProtocol\PacketInterface;

class PacketHandler
{
    /**
     * Packet ID List — maps packet IDs to their handler classes.
	 * How this Works:
	 * The client sends a Packet ID to the server, this packet has an id.
	 * this id is mapped and the logic handled based off the id of the packet. e.g if the server recieves 1, it will run the function to call the PlayerLoginRequestPacket class to handle the logic for that packet.
	 * This Class will be regularly updated, so you are not shielded from api constraint changes!
     */
    private const PACKET_MAP = [
    1  => PlayerLoginRequestPacket::class,
    4  => PlayerCreationPacket::class,
    5 => PlayerInventorySlotPacket::class, 
    38 => PasswordVerificationPacket::class,
    42 => PlayerManaPacket::class,
    68 => SyncUUIDPacket::class,
];


    /**
     * Returns the class name that handles the given packet ID.
     */
    public static function getHandlerClassFromID(int $id): ?string
    {
        return self::PACKET_MAP[$id] ?? null;
    }
	
	public static function dispatch(int $id, array $data, $clientSocket, Server $server): void
	{
		if ($server->isDebugEnabled == true){
		echo "Raw data received:\n";
		var_dump($id);
		var_dump($data);
		var_dump($clientSocket);
		}
    $className = self::getHandlerClassFromID($id);

    if ($className && class_exists($className)) {
        $handler = new $className();
        if ($handler instanceof PacketInterface) {
            $handler->handle($data, $clientSocket, $server);
        } else {
            Logger::log("error","Handler for packet ID $id does not implement PacketInterface.");
        }
    } else {
        Logger::log("error","No handler found for packet ID $id.");
    }
}

}
