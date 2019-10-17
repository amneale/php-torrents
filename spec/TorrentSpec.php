<?php

declare(strict_types=1);

namespace spec\Amneale\Torrent;

use PhpSpec\ObjectBehavior;

class TorrentSpec extends ObjectBehavior
{
    private const INFO_HASH = 'info-hash';

    public function it_can_generate_a_magnet_uri(): void
    {
        $this->beConstructedWith(self::INFO_HASH);
        $this->toMagnetUri()->shouldBe('magnet:?xt=urn:btih:' . self::INFO_HASH);
    }
}
