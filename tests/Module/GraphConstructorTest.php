<?php

declare(strict_types=1);

namespace RedislabsModulesTest\Module;

use Redislabs\Module\RedisGraph\Node;
use Redislabs\Module\RedisGraph\Edge;
use Redislabs\Module\RedisGraph\GraphConstructor;

class GraphConstructorTest extends \Codeception\Test\Unit
{
    /**
     * @test
     */
    public function shouldCreateQueryObjectSuccessfully(): void
    {
        $labelSource =  'person';
        $labelDestination =  'country';

        $propertiesSource = ['name' => 'John Doe', 'age' => 33, 'gender' => 'male', 'status' => 'single'];
        $propertiesDestination = ['name' => 'Japan'];
        $edgeProperties = ['purpose' => 'pleasure', 'duration' => 'two weeks'];

        $person = Node::createWithLabel($labelSource)->withProperties($propertiesSource)->withAlias('CatOwner');
        $country = Node::createWithLabelAndProperties($labelDestination, $propertiesDestination)
            ->withAlias('CatCountry');

        $edge = Edge::create($person, 'visited', $country)->withProperties($edgeProperties);

        $graph = new GraphConstructor('TRAVELLERS');
        $graph->addNode($person);
        $graph->addNode($country);
        $graph->addEdge($edge);
        $query = $graph->getCommitQuery();
        $this->assertEquals(
            'CREATE (CatOwner:person {name: "John Doe", age: 33, gender: "male", status: "single"}), ' .
            '(CatCountry:country {name: "Japan"}), ' .
            '(CatOwner:person)-[:visited {purpose: "pleasure", duration: "two weeks"}]->(CatCountry:country)',
            $query->getQueryString()
        );
        $this->assertEquals('TRAVELLERS', $query->getName());
    }

    /**
     * @test
     */
    public function shouldCreateQueryObjectWithMergeSuccessfully(): void
    {
        $labelSource =  'person';
        $labelDestination =  'country';

        $propertiesSource = ['name' => 'John Doe', 'age' => 33, 'gender' => 'male', 'status' => 'single'];
        $propertiesDestination = ['name' => 'Japan'];
        $edgeProperties = ['purpose' => 'pleasure', 'duration' => 'two weeks'];

        $person = Node::createWithLabel($labelSource)->withProperties($propertiesSource)->withAlias('CatOwner');
        $country = Node::createWithLabelAndProperties($labelDestination, $propertiesDestination)
            ->withAlias('CatCountry');

        $edge = Edge::merge($person, 'visited', $country)->withProperties($edgeProperties);

        $graph = new GraphConstructor('TRAVELLERS');
        $graph->addNode($person);
        $graph->addNode($country);
        $graph->addEdge($edge);
        $query = $graph->getCommitQueryWithMerge();
        $this->assertEquals(
            'MERGE (CatOwner:person {name: "John Doe", age: 33, gender: "male", status: "single"}) ' .
            'MERGE (CatCountry:country {name: "Japan"}) ' .
            'MERGE (CatOwner)-[:visited {purpose: "pleasure", duration: "two weeks"}]->(CatCountry)',
            $query->getQueryString()
        );
        $this->assertEquals('TRAVELLERS', $query->getName());
    }
}
