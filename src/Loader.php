<?php

declare(strict_types=1);

namespace Amneale\Torrent;

use Amneale\Torrent\Engine\Decoder;
use Amneale\Torrent\Engine\Encoder;
use Amneale\Torrent\Exception\InvalidMagnetUriException;

class Loader
{
    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var Decoder
     */
    private $decoder;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Encoder $encoder
     * @param Decoder $decoder
     * @param Provider $provider
     */
    public function __construct(Encoder $encoder, Decoder $decoder, Provider $provider)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->provider = $provider;
    }

    /**
     * @param string $hash
     *
     * @return Torrent
     */
    public function fromInfoHash(string $hash): Torrent
    {
        return $this->fromFile(
            $this->provider->getDownloadUrl($hash)
        );
    }

    /**
     * @param string $uri
     *
     * @return Torrent
     */
    public function fromMagnetUri(string $uri): Torrent
    {
        $magnet = Magnet::fromUri($uri);

        return $this->fromInfoHash($magnet->infoHash);
    }

    /**
     * @param string $path
     *
     * @return Torrent
     */
    public function fromFile(string $path): Torrent
    {
        $data = $this->decoder->decode(
            file_get_contents($path)
        );

        return new Torrent(
            $this->getHash($data['info']),
            $data['info']['name'],
            $this->getTrackers($data),
            $this->getSize($data),
            $this->getCreationDate($data)
        );
    }

    /**
     * @param array $info
     *
     * @return string
     */
    private function getHash(array $info): string
    {
        $infoString = $this->encoder->encode($info);

        return sha1($infoString);
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function getTrackers($data): array
    {
        if (isset($data['announce-list'])) {
            $trackers = [];
            array_walk_recursive(
                $data['announce-list'],
                static function (string $uri) use (&$trackers): void {
                    $trackers[] = $uri;
                }
            );

            return array_unique($trackers);
        }

        if (isset($data['announce'])) {
            return [$data['announce']];
        }

        return [];
    }

    /**
     * @param $data
     *
     * @return array|null
     */
    private function getSize($data): ?int
    {
        if (isset($data['files'])) {
            return array_sum(
                array_column($data['files'], 'length')
            );
        }

        return $data['length'] ?? null;
    }

    /**
     * @param array $data
     *
     * @return \DateTime|null
     */
    private function getCreationDate(array $data): ?\DateTime
    {
        if (!empty($data['creation date'])) {
            return \DateTime::createFromFormat('U', (string) $data['creation date']);
        }

        return null;
    }
}
