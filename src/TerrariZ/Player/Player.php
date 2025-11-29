<?php
namespace TerrariZ\Player;
use TerrariZ\Utils\Color;
class Player
{
	/** @var int|null */
	private ?int   $id       = null;
    private ?int   $socketId = null;


	public int    $uid;
    public string $username;
    public int    $skinVariant;
    public int    $hair;
    public int    $hairDye;
    public bool   $hideVisuals;
    public bool   $hideVisuals2;
    public bool   $hideMisc;
    public color    $hairColor;
    public color    $skinColor;
    public color    $eyeColor;
    public color    $shirtColor;
    public color    $undershirtColor;
    public color    $pantsColor;
    public color    $shoeColor;
    public int    $difficultyFlag;
    public int    $flags2;
    public int    $flags3;
	private string $uuid = '';
    private int $currentMana = 0;
private int $maxMana = 20;
public function __construct(

    int $uid,
    string $username,
    int $skinVariant,
    int $hair,
    int $hairDye,
    int $hideVisuals,
    int $hideVisuals2,
    int $hideMisc,
    Color $hairColor,
    Color $skinColor,
    Color $eyeColor,
    Color $shirtColor,
    Color $undershirtColor,
    Color $pantsColor,
    Color $shoeColor,
    int $difficultyFlag,
    int $flags2,
    int $flags3
) {
	$this->uid = $uid;
	$this->username = $username;
	$this->skinVariant = $skinVariant;
	$this->hair = $hair;
	$this->hairDye = $hairDye;
	$this->hideVisuals = $hideVisuals;
	$this->hideVisuals2 = $hideVisuals2;
	$this->hideMisc = $hideMisc;
	$this->hairColor = $hairColor;
	$this->skinColor = $skinColor;
	$this->eyeColor = $eyeColor;
	$this->shirtColor = $shirtColor;
	$this->undershirtColor = $undershirtColor;
	$this->pantsColor = $pantsColor;
	$this->shoeColor = $shoeColor;
	$this->difficultyFlag = $difficultyFlag;
	$this->flags2 = $flags2;
	$this->flags3 = $flags3;
	
}
public function setUUID(string $uuid): void {
    $this->uuid = $uuid;
}

public function getUUID(): string {
    return $this->uuid;
}
public static function fromPacketData(array $data): Player {
    return new Player(
        $data['uid'],
        $data['username'],
        $data['skinVariant'],
        $data['hair'],
        $data['hairDye'],
        $data['hideVisuals'],
        $data['hideVisuals2'],
        $data['hideMisc'],
        new Color($data['hairColorR'], $data['hairColorG'], $data['hairColorB']),
        new Color($data['skinColorR'], $data['skinColorG'], $data['skinColorB']),
        new Color($data['eyeColorR'], $data['eyeColorG'], $data['eyeColorB']),
        new Color($data['shirtColorR'], $data['shirtColorG'], $data['shirtColorB']),
        new Color($data['undershirtColorR'], $data['undershirtColorG'], $data['undershirtColorB']),
        new Color($data['pantsColorR'], $data['pantsColorG'], $data['pantsColorB']),
        new Color($data['shoeColorR'], $data['shoeColorG'], $data['shoeColorB']),
        $data['difficultyFlag'],
        $data['flags2'],
        $data['flags3']
    );
}

public function setCurrentMana(int $mana): void { $this->currentMana = $mana; }
public function setMaxMana(int $mana): void { $this->maxMana = $mana; }

public function getUID(): int {
    return $this->uid;
}
public function getUsername(): string
    {
        return $this->username;
    }
 public function setSocketId(int $socketId): void
    {
        $this->socketId = $socketId;
    }
	 public function getSocketId(): ?int
    {
        return $this->socketId;
    }


 public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}
