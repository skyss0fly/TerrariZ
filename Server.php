<?php
namespace TerrariZ;

use TerrariZ\TerrariaProtocol\PacketHandler;
use TerrariZ\Utils\Logger;
class Server
{
    private string $host;
    private int $port;
    private $serverSocket;
	public bool $isPasswordEnabled = false;
	public bool $isDebugEnabled = false;
    public string $serverPassword = '';


    public function __construct(string $host = '0.0.0.0', int $port = 7777)
    {
        $this->host = $host;
        $this->port = $port;

        $configPath = __DIR__ . '/Resources/Config.yml';
        if (file_exists($configPath)) {
            $config = $this->parseSimpleYaml($configPath);
			$this->isDebugEnabled = isset($config['DebugEnabled']) && strtolower($config['DebugEnabled']) === 'true';
            $this->isPasswordEnabled = isset($config['IsPasswordEnabled']) && strtolower($config['IsPasswordEnabled']) === 'true';
            $this->serverPassword = $config['Password'] ?? '';
        } else {
            Logger::log("warning","Config.yml not found at $configPath\n");
        }
    }

    private function parseSimpleYaml(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $config = [];

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $config[trim($key)] = trim($value, " \t\n\r\"'");
            }
        }

        return $config;
    }

    public function start(): void
    {
        $this->serverSocket = stream_socket_server("tcp://{$this->host}:{$this->port}", $errno, $errstr);

        if (!$this->serverSocket) {
            die("Failed to start server: $errstr ($errno)\n");
        }

        Logger::log("info"," Terraria Server Online at {$this->host}:{$this->port}\n");

        while (true) {
            $clientSocket = @stream_socket_accept($this->serverSocket, 5);

            if ($clientSocket === false) {
                continue; // No connection, loop again
            }

            echo  "Client connected\n";
			while (!feof($clientSocket)) {
    $chunk = fread($clientSocket, 1024);

    if ($chunk === false || $chunk === '') {
        Logger::log("info","Client disconnected or sent nothing\n");
        break;
    }
	if ($this->isDebugEnabled == true) {
    Logger::log("debug","Raw bytes: " . bin2hex($chunk) . "\n");
}
    // Try to read packet length and ID
    if (strlen($chunk) < 3) {
        Logger::log("info","Chunk too small to be a packet\n");
        continue;
    }

    $length = unpack('v', substr($chunk, 0, 2))[1];
    $packetId = ord($chunk[2]);

    if ($this->isDebugEnabled == true) {
	Logger::log("debug","Parsed length: $length\n");
    Logger::log("debug","Parsed packet ID: $packetId\n");
	}
    $payload = substr($chunk, 3, $length - 1);

    $packetData = [
        'id' => $packetId,
        'data' => $payload,
    ];

    PacketHandler::dispatch($packetId, $packetData, $clientSocket, $this);
}

				fclose($clientSocket); // Only close after client is done
				}
				}
				}
