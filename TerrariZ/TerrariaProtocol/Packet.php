<?php
namespace TerrariZ\TerrariaProtocol;
use TerrariZ\Server;
use TerrariZ\Utils\Logger;
class Packet
{
    /** Raw packet bytes */
    private string $data;

    /** Read cursor */
    private int $pos = 0;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->pos  = 0;
    }
public function getPos(): int
{
    return $this->pos;
}
public static function writeString(string $str): string
{
    return chr(strlen($str)) . $str;
}

public static function writeInt16(int $v): string
{
    return pack('v', $v);
}

public static function writeInt32(int $v): string
{
    return pack('V', $v);
}

public static function readPacket($client): ?array
{
    $lengthBytes = fread($client, 2);
    if ($lengthBytes === false || strlen($lengthBytes) < 2) {
        return null;
    }

    $length = unpack('v', $lengthBytes)[1];

    // length includes the 2 bytes we already read
    $remaining = $length - 2;

    $packet = '';
    while (strlen($packet) < $remaining) {
        $chunk = fread($client, $remaining - strlen($packet));
        if ($chunk === false || $chunk === '') {
            return null;
        }
        $packet .= $chunk;
    }

    $id = ord($packet[0]);
    $data = substr($packet, 1);

    return [
        'id'   => $id,
        'data' => $data,
    ];
}

public static function writePacket(
    $client,
    int $packetId,
    string $payload,
    Server $server
): void {
    stream_set_blocking($client, true);

    $length = strlen($payload) + 3;
    $packet = pack('v', $length) . chr($packetId) . $payload;

    fwrite($client, $packet);
    fflush($client);

    if ($server->isDebugEnabled) {
        Logger::log(
            "debug",
            "Outbound packet ID: {$packetId}\n" .
            "Outbound length: {$length}\n" .
            "Outbound payload: " . bin2hex($payload)
        );
    }
}

    public function readByte(): int
    {
        $length = strlen($this->data);
        if ($this->pos >= $length) {
            throw new \Exception(
                "Packet too short: tried to read offset {$this->pos} of {$length}"
            );
        }
        return ord($this->data[$this->pos++]);
    }

    public function readInt16(): int
    {
        if (strlen($this->data) < $this->pos + 2) {
            throw new \Exception("Packet too short for 16-bit at {$this->pos}");
        }
        $chunk = substr($this->data, $this->pos, 2);
        $this->pos += 2;
        return unpack('v', $chunk)[1];
    }

    public function readInt32(): int
    {
        if (strlen($this->data) < $this->pos + 4) {
            throw new \Exception("Packet too short for 32-bit at {$this->pos}");
        }
        $chunk = substr($this->data, $this->pos, 4);
        $this->pos += 4;
        return unpack('V', $chunk)[1];
    }

    public function readString(): string
    {
        $length    = $this->readByte();
        $remaining = strlen($this->data) - $this->pos;
        if ($remaining < $length) {
            throw new \Exception(
                "Incomplete string: need {$length}, have {$remaining}"
            );
        }
        $str = substr($this->data, $this->pos, $length);
        $this->pos += $length;
        return $str;
    }
    public function newreadInt16(): int
{
    if (strlen($this->data) < $this->pos + 2) {
        throw new \Exception("Packet too short for 16-bit at {$this->pos}");
    }
    $chunk = substr($this->data, $this->pos, 2);
    $this->pos += 2;
    // 'v' is little-endian unsigned short
    return unpack('v', $chunk)[1];
}

}

