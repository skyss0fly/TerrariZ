<?php

require_once 'src/TerrariZ/Server.php';
require_once 'src/TerrariZ/TerrariaProtocol/PacketHandler.php';
require_once 'src/TerrariZ/TerrariaProtocol/PacketInterface.php';
require_once 'src/TerrariZ/TerrariaProtocol/Packet.php';
// Packets:
require_once 'src/TerrariZ/TerrariaProtocol/PlayerLoginRequestPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PasswordVerificationPacket.php';

// Utils:
require_once 'src/TerrariZ/Utils/Logger.php';

// Terminal:
require_once 'src/TerrariZ/Terminal/Terminal.php';

use TerrariZ\Server;

$server = new Server();
$server->start();
