<?php

namespace Amneale\Torrent;

use SandFox\Bencode\Engine\Decoder as BaseDecoder;

class Decoder
{
    /**
     * @param string $data
     *
     * @return null
     */
    public function decode(string $data)
    {
        $decoder = new BaseDecoder($data);
        return $decoder->decode();
    }
}
