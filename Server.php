<?php
namespace TerrariZ;

use TerrariZ\TerrariaProtocol\PacketHandler;
use TerrariZ\Utils\Logger;
use TerrariZ\Player\Player;
use TerrariZ\World\World;
use TerrariZ\World\Generator;
use TerrariZ\World\WorldFile;

class Server
{
    private string $host;
    private int $port;
    private $serverSocket;
    private World $world;
	public bool $isPasswordEnabled = false;
	public bool $isDebugEnabled = false;
    public string $serverPassword = '';

    
	
/** @var Player[] keyed by socketId */
private array $players = [];



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
\TerrariZ\Utils\CrashHandler::register();
    $this->serverSocket = stream_socket_server("tcp://{$this->host}:{$this->port}", $errno, $errstr);

    if (!$this->serverSocket) {
        die("Failed to start server: $errstr ($errno)\n");
    }

    Logger::log("info"," Terraria Server Online at {$this->host}:{$this->port}");

    if ($this->isDebugEnabled) {
        Logger::log(
            "debug",
            "This Server is Running in Debug Mode. To Disable Debug Mode,\n Go to Config.yml and set 'DebugEnabled' to false."
        );
    }

    // Load or generate world
    if (file_exists("world.wld")) {
        $this->world = WorldFile::load("world.wld");
    } else {
        $this->world = new World(4200,1200,"TerrariZ World");
        Generator::generate($this->world);
        WorldFile::save($this->world,"world.wld");
    }

    while (true) {
        $clientSocket = @stream_socket_accept($this->serverSocket, 5);

        if ($clientSocket === false) {
            continue;
        }

        Logger::log("info", "New Client Joining");

        while (!feof($clientSocket)) {

            // 🔥 Correct Terraria packet read (handles TCP fragmentation)
            $packet = \TerrariZ\TerrariaProtocol\Packet::readPacket($clientSocket);

            if ($packet === null) {
                Logger::log("info","Client disconnected or sent nothing");
                break;
            }

            $packetId = $packet['id'];
            $payload  = $packet['data'];

            if ($this->isDebugEnabled) {
                Logger::log("debug","Parsed packet ID: $packetId");
                Logger::log("debug","Payload bytes: " . bin2hex($payload));
            }

            PacketHandler::dispatch(
                $packetId,
                [
                    'id'   => $packetId,
                    'data' => $payload
                ],
                $clientSocket,
                $this
            );
        }

        fclose($clientSocket);
    }
}
    
public function addPlayer($clientSocket, Player $player): void
{
    $sockId = (int)$clientSocket;

    // If player already exists for this socket, keep their ID
    if (isset($this->players[$sockId])) {
        $existing = $this->players[$sockId];

        $player->setId($existing->getId());
        $player->setSocketId($sockId);

        $this->players[$sockId] = $player;
        return;
    }

    // New player
    $newUid = $this->getNextServerUid();

    $player->setId($newUid);
    $player->setSocketId($sockId);

    $this->players[$sockId] = $player;

    if ($this->isDebugEnabled) {
        Logger::log(
            "debug",
            "Player #{$newUid} ({$player->getUsername()}) joined on socket {$sockId}"
        );
    }
}

public function getPlayers(): array
{
    return array_values($this->players);
}

public function getPlayerBySocket($clientSocket): ?Player
{
    return $this->players[(int)$clientSocket] ?? null;
}

public function removePlayerBySocket($clientSocket): void
{
    unset($this->players[(int)$clientSocket]);
}
public function getWorld(): World
{
    return $this->world;
}

public function getClientStream($player)
{
    return $player->getSocket();
}
private function getNextServerUid(): int
{
    if (empty($this->players)) {
        return 1;
    }

    $ids = array_map(
        fn(Player $p) => $p->getId() ?? 0,
        $this->players
    );

    return max($ids) + 1;
}
}
