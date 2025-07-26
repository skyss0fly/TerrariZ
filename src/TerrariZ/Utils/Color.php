<?php
namespace TerrariZ\Utils;

class Color {
    public int $R;
    public int $G;
    public int $B;

    public function __construct(int $R, int $G, int $B) {
        $this->R = $R;
        $this->G = $G;
        $this->B = $B;
    }
}
