<?php

declare(strict_types=1);

namespace Amneale\Torrent;

use Amneale\Torrent\Engine\Decoder;
use Amneale\Torrent\Engine\Encoder;
use Amneale\Torrent\Exception\InvalidMagnetUriException;

class Factory
{
    private const MAGNET_REGEX = '/^magnet:\?xt=urn:btih:[0-9a-fA-F]{40}(?:&.*)?/';

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
        if (!preg_match(self::MAGNET_REGEX, $uri)) {
            throw new InvalidMagnetUriException('Invalid Magnet URI: ' . $uri);
        }

        $query = parse_url($uri, PHP_URL_QUERY);
        parse_str($query, $parameters);
        $hash = substr($parameters['xt'], 9);

        return $this->fromInfoHash($hash);
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

        $torrent = new Torrent(
            $this->getHash($data['info']),
            $data['info']['name'],
            $this->getTrackers($data),
            $this->getSize($data),
            $this->getCreationDate($data)
        );

        return $torrent;
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
        $trackers = [];

        if (isset($data['announce-list'])) {
            array_walk_recursive(
                $data['announce-list'],
                static function (string $uri) use (&$trackers): void {
                    $trackers[] = $uri;
                }
            );

            return $trackers;
        }

        if (isset($data['announce'])) {
            return [$data['announce']];
        }

        return $trackers;
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

        if (isset($data['length'])) {
            return $data['length'];
        }

        return null;
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
