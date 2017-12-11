<?php

namespace Brouzie\Components\Indexer\Async\Message;

use Enqueue\Util\JSON;

class IdsMessage implements \JsonSerializable
{
    private $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function jsonSerialize()
    {
        return ['ids' => $this->ids];
    }

    public static function jsonDeserialize(string $json)
    {
        $data = JSON::decode($json);

        if (!isset($data['ids'])) {
            throw new \LogicException('The message does not contain "ids" but it is required.');
        }

        return new static($data['ids']);
    }
}
