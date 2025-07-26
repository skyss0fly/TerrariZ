<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\TerrariaProtocol\PacketInterface;
use TerrariZ\Server;
use TerrariZ\TerrariaProtocol\Packet;
use TerrariZ\Player\Player;

class PlayerCreationPacket implements PacketInterface
{
    public const PACKET_ID = 4;

    public function handle(array $data, $clientSocket, Server $server): void
    {
      
    $pkt = new Packet($data['data']);

    // 1) whoAmI is ONE byte, not two:
    $uid = $pkt->readByte();                

    
        $username = $pkt->readString();
		var_dump($username);


        // 3) appearance settings
        $skinVariant   = $pkt->readByte();
        $hair          = $pkt->readByte();
        $hairDye       = $pkt->readByte();
        $hideVisuals   = $pkt->readByte();
        $hideVisuals2  = $pkt->readByte();
        $hideMisc      = $pkt->readByte();

        // 4) colors (7×RGB triplets)
        $hairColor        = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $skinColor        = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $eyeColor         = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $shirtColor       = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $undershirtColor  = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $pantsColor       = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];
        $shoeColor        = [$pkt->readByte(), $pkt->readByte(), $pkt->readByte()];

        // 5) flags
        $difficultyFlag = $pkt->readByte();
        $flags2         = $pkt->readByte();
        $flags3         = $pkt->readByte();

        // assemble into the array shape your Player::fromPacketData() expects
        $playerData = [
            'uid'               => $uid,
            'username'          => $username,
            'skinVariant'       => $skinVariant,
            'hair'              => $hair,
            'hairDye'           => $hairDye,
            'hideVisuals'       => $hideVisuals,
            'hideVisuals2'      => $hideVisuals2,
            'hideMisc'          => $hideMisc,

            'hairColorR'        => $hairColor[0],
            'hairColorG'        => $hairColor[1],
            'hairColorB'        => $hairColor[2],

            'skinColorR'        => $skinColor[0],
            'skinColorG'        => $skinColor[1],
            'skinColorB'        => $skinColor[2],

            'eyeColorR'         => $eyeColor[0],
            'eyeColorG'         => $eyeColor[1],
            'eyeColorB'         => $eyeColor[2],

            'shirtColorR'       => $shirtColor[0],
            'shirtColorG'       => $shirtColor[1],
            'shirtColorB'       => $shirtColor[2],

            'undershirtColorR'  => $undershirtColor[0],
            'undershirtColorG'  => $undershirtColor[1],
            'undershirtColorB'  => $undershirtColor[2],

            'pantsColorR'       => $pantsColor[0],
            'pantsColorG'       => $pantsColor[1],
            'pantsColorB'       => $pantsColor[2],

            'shoeColorR'        => $shoeColor[0],
            'shoeColorG'        => $shoeColor[1],
            'shoeColorB'        => $shoeColor[2],

            'difficultyFlag'    => $difficultyFlag,
            'flags2'            => $flags2,
            'flags3'            => $flags3,
        ];

        // finally, hand it off to your Player model and register on the server
        $player = Player::fromPacketData($playerData);
        $server->addPlayer($clientSocket, $player);
    }
}
