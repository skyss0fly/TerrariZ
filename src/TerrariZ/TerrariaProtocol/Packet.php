<?php

namespace TerrariZ\TerrariaProtocol;


Class Packet {

public static function sendPlayerAppearance($clientSocket, array $appearanceData) {
    $payload = '';

    $payload .= chr($appearanceData['slot']);                  // Offset 0
    $payload .= chr($appearanceData['hairStyle']);             // Offset 1
    $payload .= chr($appearanceData['gender']);                // Offset 2
    $payload .= self::packColor($appearanceData['hairColor']);         // Offset 3
    $payload .= self::packColor($appearanceData['skinColor']);         // Offset 6
    $payload .= self::packColor($appearanceData['eyeColor']);          // Offset 9
    $payload .= self::packColor($appearanceData['shirtColor']);        // Offset 12
    $payload .= self::packColor($appearanceData['undershirtColor']);   // Offset 15
    $payload .= self::packColor($appearanceData['pantsColor']);        // Offset 18
    $payload .= self::packColor($appearanceData['shoeColor']);         // Offset 21
    $payload .= chr($appearanceData['difficulty']);            // Offset 24
    $payload .= self::packString($appearanceData['playerName']);       // Offset 25+

    self::writePacket($clientSocket, 4, $payload);
	
	}
	
	public static function packColor(array $color): string {
    return chr($color['r']) . chr($color['g']) . chr($color['b']);
}

public static function packString(string $str): string {
    return chr(strlen($str)) . $str;
}

public static function readPacket($client) {
    $lengthBytes = fread($client, 2);
    if (!$lengthBytes) {
        //DEBUG: echo "No data received from client!\n";
        return null;
    }

    $length = unpack('v', $lengthBytes)[1];
    $packet = fread($client, $length);

    if (!$packet) {
      //DEBUG:  echo "Packet read failed!\n";
        return null;
    }

  //DEBUG:   var_dump($packet);

    return [
        'id' => ord($packet[0]),
        'data' => substr($packet, 1)
    ];
}
     public static function writePacket($client, int $packetId, string $payload = '') {
	/*	
		if ($bytesWritten = socket_write($client, $packetId) === false) {
    echo "Socket write failed: " . socket_strerror(socket_last_error($client)) . PHP_EOL;
}
*/
      stream_set_blocking($client,true);
      $length = strlen($payload) + 3;
	  $packet = pack('v',$length) . chr($packetId) . $payload;
	  fwrite($client, $packet);
	  echo "packet";
	  var_dump($packet);
	  echo "payload";
	  var_dump($payload);
	  echo "length";
	  var_dump($length);
	  fflush($client); 
} 

}


?>