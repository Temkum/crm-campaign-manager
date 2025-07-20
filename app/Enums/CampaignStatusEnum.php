<?php

namespace App\Enums;

enum CampaignStatusEnum: string
{
    case ACTIVE = 'active';
    case DISABLED = 'disabled';
    case PAUSED = 'paused';
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    
    /**
     * Convert the enum to a string
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
    

    /**
     * Get all values as an array
     *
     * @return array<string, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Get all cases as an array for select options
     *
     * @return array<string, string>
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = ucfirst(strtolower($case->value));
        }
        return $options;
    }
}