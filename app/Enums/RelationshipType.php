<?php

namespace App\Enums;

enum RelationshipType: string
{
    case SPOUSE = 'spouse';
    case CHILD = 'child';
    case PARENT = 'parent';
    case SIBLING = 'sibling';
    case OTHER = 'other';

    /**
     * Get the display name for the relationship type
     *
     * @return string The display name
     */
    public function label(): string
    {
        return match ($this) {
            self::SPOUSE => 'Spouse',
            self::CHILD => 'Child',
            self::PARENT => 'Parent',
            self::SIBLING => 'Sibling',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get the localized display name for the relationship type
     *
     * @param  string  $locale  The locale to use
     * @return string The localized display name
     */
    public function localizedLabel(string $locale = 'ms'): string
    {
        if ($locale === 'ms') {
            return match ($this) {
                self::SPOUSE => 'Pasangan',
                self::CHILD => 'Anak',
                self::PARENT => 'Ibu/Bapa',
                self::SIBLING => 'Adik-beradik',
                self::OTHER => 'Lain-lain',
            };
        }

        return $this->label();
    }

    /**
     * Get all relationship types as an array
     *
     * @param  string  $locale  The locale to use for labels
     * @return array<string, string> Array of relationship types
     */
    public static function toArray(string $locale = 'ms'): array
    {
        return array_reduce(self::cases(), function ($carry, $case) use ($locale) {
            $carry[$case->value] = $locale === 'ms'
                ? $case->localizedLabel()
                : $case->label();

            return $carry;
        }, []);
    }
}
