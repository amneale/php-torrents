<?php

declare(strict_types=1);

namespace spec\Amneale\Torrent;

use Amneale\Torrent\Engine\Decoder;
use Amneale\Torrent\Engine\Encoder;
use Amneale\Torrent\Exception\InvalidMagnetUriException;
use Amneale\Torrent\Provider;
use Amneale\Torrent\Torrent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LoaderSpec extends ObjectBehavior
{
    private const TORRENT_INFO = 'encoded-torrent-info';
    private const TORRENT_DATA = [
        'info' => [
            'name' => 'my-torrent',
        ],
    ];

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $hash;

    public function let(Encoder $encoder, Decoder $decoder, Provider $provider): void
    {
        $this->hash = sha1(self::TORRENT_INFO);
        $this->file = tempnam(sys_get_temp_dir(), 'loader');

        $provider->getDownloadUrl($this->hash)->willReturn($this->file);

        $encoder->encode(Argument::any())->willReturn(self::TORRENT_INFO);
        $decoder->decode(Argument::any())->willReturn(self::TORRENT_DATA);

        $this->beConstructedWith($encoder, $decoder, $provider);
    }

    public function it_loads_a_torrent_from_an_info_hash(): void
    {
        $torrent = $this->fromInfoHash($this->hash);
        $torrent->shouldBeAnInstanceOf(Torrent::class);
        $torrent->infoHash->shouldBe($this->hash);
    }

    public function it_loads_a_torrent_from_a_magnet_uri(): void
    {
        $torrent = $this->fromMagnetUri('magnet:?xt=urn:btih:' . $this->hash);
        $torrent->shouldBeAnInstanceOf(Torrent::class);
        $torrent->infoHash->shouldBe($this->hash);
    }

    public function it_excepts_when_passed_an_invalid_magnet_uri(): void
    {
        $this->shouldThrow(InvalidMagnetUriException::class)->during('fromMagnetUri', ['foo:bar:baz']);
    }

    public function it_loads_a_torrent_from_a_file(): void
    {
        $torrent = $this->fromFile($this->file);
        $torrent->shouldBeAnInstanceOf(Torrent::class);
        $torrent->name->shouldBe(self::TORRENT_DATA['info']['name']);
        $torrent->infoHash->shouldBe($this->hash);
    }

    public function it_can_can_read_trackers_from_nested_announce_list(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(
                self::TORRENT_DATA,
                [
                    'announce-list' => [
                        'foo.bar/tracker',
                        'nested' => ['foo.bar/tracker2'],
                        'duplicate' => ['foo.bar/tracker'],
                    ],
                ]
            )
        );

        $torrent = $this->fromFile($this->file);
        $torrent->trackers->shouldBe(['foo.bar/tracker', 'foo.bar/tracker2']);
    }

    public function it_can_read_a_single_tracker_from_torrent_data(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(self::TORRENT_DATA, ['announce' => 'foo.bar/tracker'])
        );

        $torrent = $this->fromFile($this->file);
        $torrent->trackers->shouldBe(['foo.bar/tracker']);
    }

    public function it_can_sum_file_sizes(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(
                self::TORRENT_DATA,
                [
                    'files' => [
                        ['length' => 1],
                        ['length' => 2],
                        ['length' => 3],
                        ['length' => 4],
                    ],
                ]
            )
        );

        $torrent = $this->fromFile($this->file);
        $torrent->size->shouldBe(10);
    }

    public function it_can_read_length_from_torrent_data(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(self::TORRENT_DATA, ['length' => 12345])
        );

        $torrent = $this->fromFile($this->file);
        $torrent->size->shouldBe(12345);
    }

    public function it_can_read_creation_date_from_torrent_data(Decoder $decoder): void
    {
        $time = time();
        $decoder->decode(Argument::any())->willReturn(
            array_merge(self::TORRENT_DATA, ['creation date' => $time])
        );

        $torrent = $this->fromFile($this->file);
        $torrent = $this->fromFile($this->file);
        $torrent->creationDate->shouldBeLike(new \DateTime("@{$time}"));
    }
}
