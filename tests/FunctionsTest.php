<?php
declare(strict_types=1);

namespace RedislabsModulesTest;

use function Redislabs\Module\RedisGraph\quotedString;
use function Redislabs\Module\RedisGraph\randomString;

class FunctionsTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * It's meaningless to try to unit test randomness since there is no expected value to use in assertEquals.
     * But we can generate plenty number of random strings and make sure there is no collision.
     * In our case, probability of generating same string is 1/(52^10).
     */
    public function randomStringFunctionShouldReturnRandomStrings() : void
    {
        $randomStrings = [];

        for ($i=0; $i<1000; $i++) {
            $newString  = randomString();
            $this->assertArrayNotHasKey($newString, $randomStrings);
            $randomStrings[$newString] = 1;
        }
    }

    /**
     * @test
     */
    public function quotedStringFunctionsShouldQuotedString() : void
    {
        $this->assertEquals('"Kedibey"', quotedString('Kedibey'));
    }
}
