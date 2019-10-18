<?php

declare(strict_types=1);

namespace Amneale\Torrent\Engine;

use SandFox\Bencode\Engine\Encoder as BaseEncoder;

class Encoder
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function encode(array $data): string
    {
        return (new BaseEncoder($data))->encode();
    }
}
