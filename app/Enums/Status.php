<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel, HasColor
{
    case Draft = 'draft';
    case Publish = 'publish';

    public function getLabel(): ?string
    {
        return match ($this){
            self::Draft => '草稿',
            self::Publish => '发布',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this){
            self::Draft => 'primary',
            self::Publish => 'info',
        };
    }
}
