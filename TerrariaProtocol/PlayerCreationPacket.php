<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;
use TerrariZ\TerrariaProtocol\WorldInfoPacket;
use TerrariZ\TerrariaProtocol\SpawnPlayerPacket;
use TerrariZ\Player\Player;
use TerrariZ\Utils\Logger;

class PlayerCreationPacket implements PacketInterface
{
    public const PACKET_ID = 4;

    public function handle(array $data, $clientSocket, Server $server): void
    {
$pkt = new Packet($data['data']);

$uid = $pkt->readByte();

$hairVariant = $pkt->readByte(); // unused but present
$hair        = $pkt->readByte();
$hairDye     = $pkt->readByte();
$hideVisuals = $pkt->readByte();
$hideVisuals2= $pkt->readByte();
$hideMisc    = $pkt->readByte();
$skinVariant = $pkt->readByte();

$username = $pkt->readString();

Logger::log("debug", "Parsed username: {$username}");

        // Colors
        $hairColor        = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $skinColor        = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $eyeColor         = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $shirtColor       = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $undershirtColor  = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $pantsColor       = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $shoeColor        = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];

        $difficultyFlag = $pkt->readByte();
        $flags2         = $pkt->readByte();
        $flags3         = $pkt->readByte();

        // Build packet data
        $playerData = [
            'uid' => $uid,
            'username' => $username,
            'skinVariant' => $skinVariant,
            'hair' => $hair,
            'hairDye' => $hairDye,
            'hideVisuals' => $hideVisuals,
            'hideVisuals2' => $hideVisuals2,
            'hideMisc' => $hideMisc,
            'hairColorR' => $hairColor[0], 'hairColorG' => $hairColor[1], 'hairColorB' => $hairColor[2],
            'skinColorR' => $skinColor[0], 'skinColorG' => $skinColor[1], 'skinColorB' => $skinColor[2],
            'eyeColorR' => $eyeColor[0], 'eyeColorG' => $eyeColor[1], 'eyeColorB' => $eyeColor[2],
            'shirtColorR' => $shirtColor[0], 'shirtColorG' => $shirtColor[1], 'shirtColorB' => $shirtColor[2],
            'undershirtColorR' => $undershirtColor[0], 'undershirtColorG' => $undershirtColor[1], 'undershirtColorB' => $undershirtColor[2],
            'pantsColorR' => $pantsColor[0], 'pantsColorG' => $pantsColor[1], 'pantsColorB' => $pantsColor[2],
            'shoeColorR' => $shoeColor[0], 'shoeColorG' => $shoeColor[1], 'shoeColorB' => $shoeColor[2],
            'difficultyFlag' => $difficultyFlag,
            'flags2' => $flags2,
            'flags3' => $flags3,
        ];

        // Register the player
        $player = Player::fromPacketData($playerData);
        $server->addPlayer($clientSocket, $player);

        Logger::log("info", "{$username} joined the Server!");
//WorldInfoPacket::send($clientSocket, $server->getWorld(), $server);

    }
}
