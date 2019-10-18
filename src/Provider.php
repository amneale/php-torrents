<?php

declare(strict_types=1);

namespace Amneale\Torrent;

interface Provider
{
    /**
     * @param string $hash
     *
     * @return string
     */
    public function getDownloadUrl(string $hash): string;
}
