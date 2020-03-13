<?php

declare(strict_types=1);

namespace Amneale\Torrent;

interface Provider
{
    public function getDownloadUrl(string $hash): string;
}
