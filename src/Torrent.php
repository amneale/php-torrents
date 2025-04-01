<?php

declare(strict_types=1);

namespace Amneale\Torrent;

final class Torrent
{
    public string $infoHash;
    public ?string $name;
    public array $trackers;
    public ?int $size;
    public ?\DateTimeInterface $creationDate;

    public function __construct(
        string $infoHash,
        ?string $name = null,
        array $trackers = [],
        ?int $size = null,
        ?\DateTimeInterface $creationDate = null,
    ) {
        $this->name = $name;
        $this->infoHash = $infoHash;
        $this->trackers = $trackers;
        $this->size = $size;
        $this->creationDate = $creationDate;
    }

    public function toMagnet(): Magnet
    {
        return new Magnet($this->infoHash, $this->name, $this->trackers);
    }
}
