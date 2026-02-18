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
require_once 'src/TerrariZ/TerrariaProtocol/WorldInfoPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/SpawnPlayerPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerActivePacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/WorldTilePacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/EssentialTilesPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/StatusPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerHPPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerManaPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/ManaEffectPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerBuffPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/PlayerSpawnPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/SendSectionPacket.php';
//require_once 'src/TerrariZ/TerrariaProtocol/SectionCompletePacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/LoadNetModulePacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/CompleteConnectionAndSpawnPacket.php';
require_once 'src/TerrariZ/TerrariaProtocol/FinishedConnectingToServerPacket.php';

// Utils:
require_once 'src/TerrariZ/Utils/Logger.php';
require_once 'src/TerrariZ/Utils/Color.php';
require_once 'src/TerrariZ/Utils/CrashHandler.php';


//Player:
require_once 'src/TerrariZ/Player/Player.php';

//World:
require_once 'src/TerrariZ/World/World.php';
require_once 'src/TerrariZ/World/Generator.php';
require_once 'src/TerrariZ/World/WorldFile.php';
require_once 'src/TerrariZ/World/Tile.php';

// Terminal:
require_once 'src/TerrariZ/Terminal/Terminal.php';

use TerrariZ\Server;

$server = new Server();
$server->start();
