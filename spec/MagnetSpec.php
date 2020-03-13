<?php

namespace spec\Amneale\Torrent;

use Amneale\Torrent\Torrent;
use PhpSpec\ObjectBehavior;

class MagnetSpec extends ObjectBehavior
{
    private const HASH = '0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33';
    private const NAME = 'Foo bar baz torrent file';
    private const TRACKERS = [
        'http://foo.bar.tracker:80',
        'udp://foo.bar.tracker:1234',
    ];

    private const RFC3986_URI = 'magnet:?xt=urn:btih:0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33' .
        '&dn=Foo%20bar%20baz%20torrent%20file' .
        '&tr=http://foo.bar.tracker:80' .
        '&tr=udp://foo.bar.tracker:1234';

    private const RFC1738_URI = 'magnet:?xt=urn:btih:0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33' .
        '&dn=Foo+bar+baz+torrent+file' .
        '&tr=http%3A%2F%2Ffoo.bar.tracker%3A80' .
        '&tr=udp%3A%2F%2Ffoo.bar.tracker%3A1234';

    public function it_can_be_built_from_an_RFC3986_uri(): void
    {
        $this->beConstructedThrough('fromUri', [self::RFC3986_URI]);

        $this->infoHash->shouldBe(self::HASH);
        $this->name->shouldBe(self::NAME);
        $this->trackers->shouldBe(self::TRACKERS);
    }

    public function it_can_be_built_from_an_RFC1738_uri(): void
    {
        $this->beConstructedThrough('fromUri', [self::RFC1738_URI]);

        $this->infoHash->shouldBe(self::HASH);
        $this->name->shouldBe(self::NAME);
        $this->trackers->shouldBe(self::TRACKERS);
    }

    public function it_can_be_built_from_a_torrent(): void
    {
        $torrent = new Torrent(self::HASH, self::NAME, self::TRACKERS);

        $this->beConstructedThrough('fromTorrent', [$torrent]);

        $this->infoHash->shouldBe(self::HASH);
        $this->name->shouldBe(self::NAME);
        $this->trackers->shouldBe(self::TRACKERS);
    }

    public function it_can_be_represented_as_a_string(): void
    {
        $this->beConstructedWith(self::HASH, self::NAME, self::TRACKERS);

        $this->__toString()->shouldReturn(self::RFC3986_URI);
    }
}
