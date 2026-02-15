<?php
namespace TerrariZ\TerrariaProtocol;

use TerrariZ\Server;

interface PacketInterface
{
    public function handle(array $data, $clientSocket, Server $server): void;
}
