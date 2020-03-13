<?php

namespace spec\Amneale\Torrent;

use PhpSpec\ObjectBehavior;

class MagnetSpec extends ObjectBehavior
{
    private const URI = 'magnet:?xt=urn:btih:0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33' .
        '&dn=Foo+bar+baz+torrent+file' .
        '&tr=http%3A%2F%2Ffoo.bar.tracker%3A80' .
        '&tr=udp%3A%2F%2Ffoo.bar.tracker%3A1234';
    private const HASH = '0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33';
    private const NAME = 'Foo bar baz torrent file';
    private const TRACKERS = [
        'http://foo.bar.tracker:80',
        'udp://foo.bar.tracker:1234',
    ];

    public function it_can_be_built_from_a_uri(): void
    {
        $this->beConstructedThrough('fromUri', [self::URI]);

        $this->infoHash->shouldBe(self::HASH);
        $this->name->shouldBe(self::NAME);
        $this->trackers->shouldBe(self::TRACKERS);
    }
}
