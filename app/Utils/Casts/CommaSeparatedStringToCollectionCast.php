<?php

namespace App\Utils\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Illuminate\Support\Collection;

class CommaSeparatedStringToCollectionCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        return collect(!empty($value) ? explode(',', $value) : [null]);
    }
}