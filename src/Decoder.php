<?php

declare(strict_types=1);

namespace Amneale\Torrent;

use SandFox\Bencode\Engine\Decoder as BaseDecoder;

class Decoder
{
    /**
     * @param string $data
     *
     * @return array
     */
    public function decode(string $data): array
    {
        return (new BaseDecoder($data))->decode();
    }
}
