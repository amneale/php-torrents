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

    /**
     * @param string $name
     * @param string|null $infoHash
     * @param array $trackers
     * @param int|null $size
     * @param \DateTimeInterface|null $creationDate
     */
    public function __construct(
        string $infoHash,
        string $name = null,
        array $trackers = [],
        ?int $size = null,
        ?\DateTimeInterface $creationDate = null
    ) {
        $this->name = $name;
        $this->infoHash = $infoHash;
        $this->trackers = $trackers;
        $this->size = $size;
        $this->creationDate = $creationDate;
    }

    /**
     * @return string
     */
    public function toMagnetUri(): string
    {
        return "magnet:?xt=urn:btih:{$this->infoHash}";
    }
}
