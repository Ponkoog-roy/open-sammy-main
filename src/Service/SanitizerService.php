<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Abstraction\AbstractEntity;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class SanitizerService
{
    public const STRICT = 1;
    public const LIBERAL = 2;

    public function __construct(
        private readonly HtmlSanitizerInterface $strictSanitizer,
        private readonly HtmlSanitizerInterface $liberalSanitizer
    ) {
    }

    public function sanitizeValue(?string $value, int $sanitizeType = self::STRICT): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitizer = $this->strictSanitizer;

        // Sanitizer is removing < symbol We will allow it only if it is followed by a space.
        $value = str_replace('< ', '&lt; ', $value);

        if ($sanitizeType === self::LIBERAL) {
            $sanitizer = $this->liberalSanitizer;
        }

        return $sanitizer->sanitize($value);
    }

    public function sanitizeEntityValue(?string $value, string $propertyName, AbstractEntity $entity): ?string
    {
        if ($value === null) {
            return null;
        }

        if (in_array($propertyName, $entity->getLessPurifiedFields(), true)) {
            return $this->sanitizeValue($value, self::LIBERAL);
        }

        return $this->sanitizeValue($value, self::STRICT);
    }
}
