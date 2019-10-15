<?php

declare(strict_types=1);

namespace Amneale\Torrent;

class Torrent
{
    /**
     * @var string
     */
    private $infoHash;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $trackers;

    /**
     * @var int|null
     */
    private $size;

    /**
     * @var \DateTimeInterface|null
     */
    private $creationDate;

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
    public function getInfoHash(): string
    {
        return $this->infoHash;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getTrackers(): array
    {
        return $this->trackers;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }
}
