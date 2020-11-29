<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph\Interfaces;

interface QueryInterface
{
    public function getQueryString(): string;
    public function getName(): string;
}
