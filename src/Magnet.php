<?php

namespace Amneale\Torrent;

use Amneale\Torrent\Exception\InvalidMagnetUriException;
use League\Uri\Components\Query;
use League\Uri\Uri;

final class Magnet
{
    private const REGEX = '/^magnet:\?xt=urn:btih:[0-9a-fA-F]{40}(?:&.*)?/';

    public string $infoHash;
    public ?string $name;
    public array $trackers;

    public function __construct(string $infoHash, ?string $name = null, array $trackers = [])
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

    public static function fromTorrent(Torrent $torrent): self
    {
        return new self($torrent->infoHash, $torrent->name, $torrent->trackers);
    }

    public function __toString(): string
    {
        $query = Query::createFromParams(['xt' => 'urn:btih:' . $this->infoHash]);

        if (!empty($this->name)) {
            $query = $query->appendTo('dn', $this->name);
        }

        foreach ($this->trackers as $tracker) {
            $query = $query->appendTo('tr', $tracker);
        }

        return (string) Uri::createFromString('magnet:')->withQuery($query);
    }
}
