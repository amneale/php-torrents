<?php

declare(strict_types=1);

namespace Amneale\Torrent\Engine;

use SandFox\Bencode\Engine\Decoder as BaseDecoder;

class Decoder
{
    public function decode(string $data): array
    {
        return (new BaseDecoder($data))->decode();
    }
}
