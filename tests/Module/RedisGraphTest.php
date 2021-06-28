<?php

declare(strict_types=1);

namespace RedislabsModulesTest\Module;

use Predis;
use Redislabs\Module\RedisGraph\Query;
use Redislabs\Module\RedisGraph\RedisGraph;
use Redislabs\Module\RedisGraph\Node;
use Redislabs\Module\RedisGraph\Edge;
use Redislabs\Module\RedisGraph\GraphConstructor;
use Predis\Client as PredisClient;

class RedisGraphTest extends \Codeception\Test\Unit
{
    protected RedisGraph $redisGraph;
    private PredisClient $redisClient;
    // phpcs:disable
    protected function _before()
    {
        $this->redisClient = new PredisClient();
        $this->redisGraph = RedisGraph::createWithPredis($this->redisClient);
    }

    protected function _after()
    {
        $this->redisClient->flushall();
    }
    // phpcs:enable
    /**
     * @test
     */
    public function shouldGetRedisGraphModuleSuccessfully(): void
    {
        $this->assertInstanceOf(RedisGraph::class, $this->redisGraph, 'RedisGraph module init.');
    }

    /**
     * @test
     */
    public function shouldFailForInvalidRedisGraphCommand(): void
    {
        $this->expectException(\Redislabs\Exceptions\InvalidCommandException::class);
        $this->redisGraph->invalidCommand('-test-');
    }


    /**
     * @test
     */
    public function shouldReturnQueryExecuteResultsSuccessfully(): void
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
        $commitQuery = $graph->getCommitQuery();
        $result = $this->redisGraph->commit($commitQuery);
        $this->assertEquals(2, $result->getLabelsAdded(), 'getLabelsAdded');
        $this->assertEquals(2, $result->getNodesCreated(), 'getNodesCreated');
        $this->assertEquals(0, $result->getNodesDeleted(), 'getNodesDeleted');
        $this->assertEquals(1, $result->getRelationshipsCreated(), 'getRelationshipsCreated');
        $this->assertEquals(0, $result->getRelationshipsDeleted(), 'getRelationshipsDeleted');
        $this->assertEquals(7, $result->getPropertiesSet(), 'getPropertiesSet');
        $this->assertGreaterThan(0.00001, $result->getExecutionTime(), 'getExecutionTime');

        $matchQueryString = 'MATCH (p:person)-[v:visited {purpose:"pleasure"}]->(c:country)
		   RETURN p.name, p.age, v.purpose, c.name';
        $matchQuery = new Query('TRAVELLERS', $matchQueryString);

        $explain = $this->redisGraph->explain($matchQuery);
        $this->assertStringContainsString('Results', $explain);
        $this->assertStringContainsString('Filter', $explain);
        $this->assertStringContainsString('Conditional Traverse', $explain);
        $this->assertStringContainsString('Node By Label Scan', $explain);

        $result = $this->redisGraph->query($matchQuery);
        ob_start();
        $result->prettyPrint();
        $content = ob_get_clean();
        echo $content;
        $this->assertStringContainsString('-----------------------------------------', $content, 'PrettyPrint');
        $this->assertStringContainsString('| p.name   | p.age | v.purpose | c.name |', $content, 'PrettyPrint');
        $this->assertStringContainsString('| John Doe | 33    | pleasure  | Japan  |', $content, 'PrettyPrint');

        $resultSet = $result->getResultSet();
        $labels = $result->getLabels();
        $this->assertEquals('p.name', $labels[0]);
        $this->assertEquals('John Doe', $resultSet[0][0]);

        $delete = $this->redisGraph->delete('TRAVELLERS');
        $this->assertStringContainsString('Graph removed', $delete);

        $deleteNonExistingGraph = $this->redisGraph->delete('TRAVELLERS');
        $this->assertStringContainsString('ERR Invalid graph operation on empty key', $deleteNonExistingGraph);
    }
}
