<?php

namespace Amneale\Torrent;

use Amneale\Torrent\Exception\InvalidMagnetUriException;
use League\Uri\Components\Query;
use League\Uri\Uri;

class Magnet
{
    private const REGEX = '/^magnet:\?xt=urn:btih:[0-9a-fA-F]{40}(?:&.*)?/';

    public string $infoHash;
    public ?string $name;
    public array $trackers;

    private function __construct(string $infoHash, ?string $name = null, array $trackers = [])
    {
        $this->infoHash = $infoHash;
        $this->name = $name;
        $this->trackers = $trackers;
    }

    public static function fromUri(string $uri): self
    {
        if (!preg_match(self::REGEX, $uri)) {
            throw new InvalidMagnetUriException('Invalid Magnet URI: ' . $uri);
        }

        $query = Query::createFromUri(
            Uri::createFromString($uri)
        );

        $infoHash = substr($query->get('xt'), 9, 40);
        $name = $query->has('dn') ? urldecode($query->get('dn')) : null;
        $trackers = $query->has('tr') ? $query->getAll('tr') : [];

        return new self($infoHash, $name, $trackers);
    }
}
