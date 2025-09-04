<?php 

namespace App\DTO\Inventory;

class ActivityFiltersDTO extends BaseFiltersDTO
{
    public function __construct(
        public ?string $type,
        public ?string $from,
        public ?string $to,
        public ?string $subject
    ) {
        parent::__construct(
            search: null,
            perPage: 20,
        );

        $this->type = !empty($this->type) ? collect(explode(',', $this->type)) : [null];
        $this->subject = !empty($this->subject) ? collect(explode(',', $this->subject)) : [null];
        $this->from ??= null;
        $this->to ??= null;
    }
}