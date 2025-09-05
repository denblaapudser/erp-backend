<?php

namespace App\DTO\Activity;

use App\DTO\Shared\BaseFiltersDTO;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use App\Utils\Casts\CommaSeparatedStringToCollectionCast;


class ActivityFiltersDTO extends BaseFiltersDTO
{
    #[WithCast(CommaSeparatedStringToCollectionCast::class)]
    public Collection $type;

    #[WithCast(CommaSeparatedStringToCollectionCast::class)]
    public Collection $subject;

    public function __construct(
        public ?string $from = null,
        public ?string $to = null,
    ) {
        parent::__construct(
            search: null,
            perPage: 20,
        );
    }
}