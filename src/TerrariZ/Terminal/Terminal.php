<?php
namespace TerrariZ\Terminal;

class Terminal
{
    private array $commands = [];

    public function tick(): void
    {
        $read = [STDIN];
        $write = $except = null;

        if (stream_select($read, $write, $except, 0, 0)) {
            $input = trim(fgets(STDIN));
            if ($input !== '') {
                $this->commands[] = $input;
            }
        }
    }

    public function getCommands(): array
    {
        $cmds = $this->commands;
        $this->commands = []; // Clear after reading
        return $cmds;
    }
}
