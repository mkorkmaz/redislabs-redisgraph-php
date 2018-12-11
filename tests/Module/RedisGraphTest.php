<?php
declare(strict_types=1);

namespace RedislabsModulesTest\Module;

use Predis;
use Redislabs\Module\RedisGraph\Query;
use Redislabs\Module\RedisGraph\RedisGraph;
use Redislabs\Module\RedisGraph\Node;
use Redislabs\Module\RedisGraph\Edge;
use Redislabs\Module\RedisGraph\GraphConstructor;

class RedisGraphTest extends \Codeception\Test\Unit
{
    /**
     * @var RedisGraph
     */
    protected $redisGraph;
    /**
     * @var Predis\Client
     */
    private $redisClient;
    // phpcs:disable
    protected function _before()
    {
        $this->redisClient = new Predis\Client();
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
    public function shouldGetRedisGraphModuleSuccessfully() : void
    {
        $this->assertInstanceOf(RedisGraph::class, $this->redisGraph, 'RedisGraph module init.');
    }

    /**
     * @test
     * @expectedException \Redislabs\Exceptions\InvalidCommandException
     */
    public function shouldFailForInvalidRedisGraphCommand() : void
    {
        $this->redisGraph->invalidCommand('-test-');
    }


    /**
     * @test
     */
    public function shouldReturnQueryExecuteResultsSuccessfully() : void
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

        $result = $this->redisGraph->query($matchQuery);
        ob_start();
        $result->prettyPrint();
        $content = ob_get_clean();
        echo $content;
        $this->assertContains('-----------------------------------------', $content, 'PrettyPrint');
        $this->assertContains('| p.name   | p.age | v.purpose | c.name |', $content, 'PrettyPrint');
        $this->assertContains('| John Doe | 33    | pleasure  | Japan  |', $content, 'PrettyPrint');

        $resultSet = $result->getResultSet();
        $this->assertEquals('p.name', $resultSet[0][0]);
        $this->assertEquals('John Doe', $resultSet[1][0]);

        $delete = $this->redisGraph->delete('TRAVELLERS');
        $this->assertContains('Graph removed', $delete);

        $deleteNonExistingGraph = $this->redisGraph->delete('TRAVELLERS');
        $this->assertContains('Graph was not found', $deleteNonExistingGraph);
    }
}
