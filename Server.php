<?php
namespace TerrariZ;

use TerrariZ\TerrariaProtocol\PacketHandler;
use TerrariZ\Utils\Logger;
use TerrariZ\Utils\CrashHandler;
use TerrariZ\Player\Player;
use TerrariZ\World\World;
use TerrariZ\World\Generator;
use TerrariZ\World\WorldFile;
use TerrariZ\Thread\ThreadManager;

class Server
{
    private string $host;
    private int $port;
    private $serverSocket;

    private World $world;
    private ThreadManager $threadManager;

    public bool $isPasswordEnabled = false;
    public bool $isDebugEnabled = false;
    public string $serverPassword = '';

    /** @var resource[] */
    private array $clientSockets = [];

    /** @var Player[] keyed by socketId */
    private array $players = [];

    private bool $running = true;

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
            Logger::log("warning","Config.yml not found at $configPath");
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
        CrashHandler::register();

        $this->serverSocket = stream_socket_server(
            "tcp://{$this->host}:{$this->port}",
            $errno,
            $errstr
        );

        if (!$this->serverSocket) {
            die("Failed to start server: $errstr ($errno)\n");
        }

        stream_set_blocking($this->serverSocket, false);

        Logger::log("info", "Terraria Server Online at {$this->host}:{$this->port}");

        if ($this->isDebugEnabled) {
            Logger::log(
                "debug",
                "Debug Mode Enabled (Config.yml → DebugEnabled)"
            );
        }

        // Thread manager
        $this->threadManager = new ThreadManager(2);

        // Load or generate world
        if (file_exists("world.wld")) {
            $this->world = WorldFile::load("world.wld");
        } else {
            $this->world = new World(4200,1200,"TerrariZ World");
            Generator::generate($this->world);
            WorldFile::save($this->world,"world.wld");
        }

        // Main server loop
        while ($this->running) {
            $this->acceptClients();
            $this->tickClients();
            $this->threadManager->tick();

            // Small sleep to prevent 100% CPU spin
            usleep(1000);
        }
    }

    private function acceptClients(): void
{
    $read = [$this->serverSocket];
    $write = null;
    $except = null;

    // non-blocking poll
    if (@stream_select($read, $write, $except, 0, 0) === 0) {
        return; // no pending connections
    }

    $client = @stream_socket_accept($this->serverSocket, 0);
    if ($client === false) {
        return;
    }

    stream_set_blocking($client, false);

    $sockId = (int)$client;
    $this->clientSockets[$sockId] = $client;

    Logger::log("info", "New Client Joining (socket $sockId)");
}

    private function tickClients(): void
    {
        foreach ($this->clientSockets as $sockId => $socket) {

            if (feof($socket)) {
                $this->disconnectClient($sockId);
                continue;
            }

            $packet = \TerrariZ\TerrariaProtocol\Packet::readPacket($socket);

            if ($packet === null) {
                continue;
            }

            $packetId = $packet['id'];
            $payload  = $packet['data'];

            if ($this->isDebugEnabled) {
                Logger::log("debug","Parsed packet ID: $packetId");
            }

            PacketHandler::dispatch(
                $packetId,
                [
                    'id'   => $packetId,
                    'data' => $payload
                ],
                $socket,
                $this
            );
        }
    }

    private function disconnectClient(int $sockId): void
    {
        if (!isset($this->clientSockets[$sockId])) {
            return;
        }

        $socket = $this->clientSockets[$sockId];

        Logger::log("info", "Client disconnected (socket $sockId)");

        fclose($socket);
        unset($this->clientSockets[$sockId]);

        $this->removePlayerBySocketId($sockId);
    }

    // ---------- Player management ----------

    public function addPlayer($clientSocket, Player $player): void
    {
        $sockId = (int)$clientSocket;

        if (isset($this->players[$sockId])) {
            $existing = $this->players[$sockId];

            $player->setId($existing->getId());
            $player->setSocketId($sockId);

            $this->players[$sockId] = $player;
            return;
        }

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

    private function removePlayerBySocketId(int $sockId): void
    {
        unset($this->players[$sockId]);
    }

    // ---------- World ----------

    public function getWorld(): World
    {
        return $this->world;
    }

    public function getClientStream(Player $player)
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

    // ---------- Threads ----------

    public function getThreadManager(): ThreadManager
    {
        return $this->threadManager;
    }
}
