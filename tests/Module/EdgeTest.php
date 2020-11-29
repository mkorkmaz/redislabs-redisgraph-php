<?php

declare(strict_types=1);

namespace RedislabsModulesTest\Module;

use Redislabs\Module\RedisGraph\Edge;
use Redislabs\Module\RedisGraph\Node;
use ArrayIterator;

class EdgeTest extends \Codeception\Test\Unit
{
    /**
     * @test
     */
    public function shouldCreateEdgeObjectUsingNamedConstructorsSuccessfully(): void
    {
        $labelSource =  'person';
        $labelDestination =  'country';

        $propertiesSource = new ArrayIterator(
            ['name' => 'John Doe', 'age' => 33, 'gender' => 'male', 'status' => 'single']
        );
        $propertiesDestination = ['name' => 'Japan'];
        $edgeProperties = ['purpose' => 'pleasure', 'duration' => 'two weeks'];

        $source = Node::createWithLabel($labelSource)->withProperties($propertiesSource)->withAlias('CatOwner');
        $destination = Node::createWithLabelAndProperties($labelDestination, $propertiesDestination)
            ->withAlias('CatCountry');

        $edge = Edge::create($source, 'visited', $destination)->withProperties($edgeProperties);

        $this->assertEquals(
            '(CatOwner:person)-[:visited {purpose: "pleasure", duration: "two weeks"}]->(CatCountry:country)',
            $edge->toString()
        );
    }
}
