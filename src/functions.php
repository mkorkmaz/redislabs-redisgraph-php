<?php
declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use function random_int;

function quotedString(string $str) : string
{
    return '"' . $str . '"';
}

/*
 * We have to be sure that random string is statistically unique.
*/

function randomString(
    ?int $length = 10,
    ?string $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) : string {
    $str = '';
    $keysize = strlen($keyspace)-1;
    for ($i = 0; $i < $length; ++$i) {
        $randomKeyPosition = random_int(0, $keysize);
        $str .= $keyspace[$randomKeyPosition];
    }
    return $str;
}
