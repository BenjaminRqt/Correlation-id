<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Data\ValueObject;

use Symfony\Component\Uid\Uuid as SymfonyUuid;

/**
 * @see https://symfony.com/doc/current/components/uid.html
 */
class Uuid extends SymfonyUuid
{
    private static ?Uuid $generatedInstance = null;
    private static ?string $nextGeneratedId = null;

    final public function __construct(string $uuid)
    {
        parent::__construct($uuid);
    }

    public static function setNextGeneratedId(?string $nextGeneratedId): void
    {
        self::$nextGeneratedId = $nextGeneratedId;
    }

    public static function generate(): Uuid
    {
        if (self::$generatedInstance === null) {
            self::$generatedInstance = new self(self::$nextGeneratedId ?? Uuid::v6()->uid);
        }

        return self::$generatedInstance;
    }

    public function getId(): string
    {
        return $this->uid;
    }
}
