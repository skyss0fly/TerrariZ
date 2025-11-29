<?php

require_once 'src/TerrariZ/Server.php';
require_once 'src/TerrariZ/TerrariaProtocol/PacketHandler.php';
require_once 'src/TerrariZ/TerrariaProtocol/PacketInterface.php';
require_once 'src/TerrariZ/TerrariaProtocol/Packet.php';
// Packets:
require_once 'src/TerrariZ/TerrariaProtocol/PlayerLoginRequestPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PasswordVerificationPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerCreationPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/SyncUUIDPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerInventorySlotPacket.php';

// Utils:
require_once 'src/TerrariZ/Utils/Logger.php';
require_once 'src/TerrariZ/Utils/Color.php';

//Player:
require_once 'src/TerrariZ/Player/Player.php';



// Terminal:
require_once 'src/TerrariZ/Terminal/Terminal.php';

use TerrariZ\Server;

$server = new Server();
$server->start();
