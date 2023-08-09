# RedisGraph-PHP: Redislabs Redis Graph PHP Client

RedisGraph-PHP provides PHP Client for Redislabs' RedisGraph Module. This library supports both widely used redis clients ([PECL Redis Extension](https://github.com/phpredis/phpredis/#readme) and [Predis](https://github.com/nrk/predis)).  

[![Build Status](https://api.travis-ci.com/mkorkmaz/redislabs-redisgraph-php.svg?branch=master)](https://travis-ci.com/mkorkmaz/redislabs-redisgraph-php) [![Coverage Status](https://coveralls.io/repos/github/mkorkmaz/redislabs-redisgraph-php/badge.svg?branch=master)](https://coveralls.io/github/mkorkmaz/redislabs-redisgraph-php?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mkorkmaz/redislabs-redisgraph-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mkorkmaz/redislabs-redisgraph-php/?branch=master) [![Latest Stable Version](https://poser.pugx.org/mkorkmaz/redislabs-redisgraph-php/v/stable)](https://packagist.org/packages/mkorkmaz/redislabs-redisgraph-php) [![Total Downloads](https://poser.pugx.org/mkorkmaz/redislabs-redisgraph-php/downloads)](https://packagist.org/packages/mkorkmaz/redislabs-redisgraph-php) [![Latest Unstable Version](https://poser.pugx.org/mkorkmaz/redislabs-redisgraph-php/v/unstable)](https://packagist.org/packages/mkorkmaz/redislabs-redisgraph-php) [![License](https://poser.pugx.org/mkorkmaz/redislabs-redisgraph-php/license)](https://packagist.org/packages/mkorkmaz/redislabs-redisgraph-php)

## Important
Read this announcement details before decide to use this library: [RedisGraph End-of-Life Announcement](https://redis.com/blog/redisgraph-eol/)


## About RedisGraph

"RedisGraph is the first queryable Property Graph database to use sparse matrices to represent the adjacency matrix in graphs and linear algebra to query the graph."

[More info about Redis Graph](https://oss.redislabs.com/redisgraph/).


## RedisGraph-PHP Interface

You can run any RedisGraph Query command using these functions. 

```php
<?php

use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;
use Redislabs\Module\RedisGraph\Result;

interface RedisGraph
{
    public function rawQuery(QueryInterface $query) : array
    public function query(QueryInterface $query) : Result
    public function delete(string $name) : string;
    public function explain(QueryInterface $query) : string;
    public function commit(QueryInterface $query) : Result
}

```

## Installation

The recommended method to install RedisGraph-PHP is with composer.

```bash
composer require mkorkmaz/redislabs-redisgraph-php
```

## Usage

You need PECL Redis Extension or Predis to use RedisGraph-PHP. 

### Creating RedisGraph Client

##### Example for PECL Redis Extension

```php
<?php
declare(strict_types=1);

use Redis;
use Redislabs\Module\RedisGraph\RedisGraph;

$redisClient = new Redis();
$redisClient->connect('127.0.0.1');
$redisGraph = RedisGraph::createWithPhpRedis($redisClient);
```

##### Example for Predis

```php
<?php
declare(strict_types=1);

use Predis;
use Redislabs\Module\RedisGraph\RedisGraph;

$redisClient = new Predis\Client();
$redisGraph = RedisGraph::createWithPredis($redisClient);
```

### Constructing a Graph.

```php
<?php


use Redislabs\Module\RedisGraph\Node;
use Redislabs\Module\RedisGraph\Edge;
use Redislabs\Module\RedisGraph\GraphConstructor;

$labelSource =  'person';
$labelDestination =  'country';

$propertiesSource = ['name' => 'John Doe', 'age' => 33, 'gender' => 'male', 'status' => 'single'];
$propertiesDestination = ['name' => 'Japan'];
$edgeProperties = ['purpose' => 'pleasure', 'duration' => 'two weeks'];

$person = Node::createWithLabel($labelSource)
	->withProperties($propertiesSource)
	->withAlias('CatOwner');
$country = Node::createWithLabelAndProperties($labelDestination, $propertiesDestination)
	->withAlias('CatCountry');

$edge = Edge::create($person, 'visited', $country)
	->withProperties($edgeProperties);

$graph = new GraphConstructor('TRAVELLERS');
$graph->addNode($person);
$graph->addNode($country);
$graph->addEdge($edge);
$commitQuery = $graph->getCommitQuery();

$result = $redisGraph->commit($commitQuery);

var_dump($result->getLabelsAdded()); // int(2)
var_dump($result->getNodesCreated()); // int(2)
var_dump($result->getLabelsAdded()); // int(2)
var_dump($result->getNodesDeleted()); // int(0)
var_dump($result->getRelationshipsCreated()); // int(1)
var_dump($result->getRelationshipsDeleted()); // int(0)
var_dump($result->getPropertiesSet()); // int(7)
var_dump($result->getExecutionTime()); // float(0.9785)


$propertiesSource = ['name' => 'Jane Doe', 'age' => 30, 'gender' => 'female', 'status' => 'single'];
$propertiesDestination = ['name' => 'Japan'];
$edgeProperties = ['purpose' => 'pleasure', 'duration' => 'one weeks'];

$person2 = Node::createWithLabel($labelSource)->withProperties($propertiesSource);
$country2 = Node::createWithLabelAndProperties($labelDestination, $propertiesDestination);
$edge2 = Edge::merge($person2, 'visited', $country2)->withProperties($edgeProperties);

$propertiesSource = ['name' => 'Kedibey', 'age' => 13, 'gender' => 'male', 'status' => 'single'];
$propertiesDestination = ['name' => 'Turkey'];
$edgeProperties = ['purpose' => 'living', 'duration' => 'whole life'];

$person3 = Node::createWithLabel($labelSource)->withProperties($propertiesSource);
$country3 = Node::createWithLabelAndProperties($labelDestination, $propertiesDestination);
$edge3 = Edge::merge($person3, 'visited', $country3)->withProperties($edgeProperties);

$graph = new GraphConstructor('TRAVELLERS');
$graph->addNode($person2);
$graph->addNode($country2);
$graph->addEdge($edge2);
$graph->addNode($person3);
$graph->addNode($country3);
$graph->addEdge($edge3);

$commitQuery = $graph->getCommitQueryWithMerge();
$this->redisGraph->commit($commitQuery);

```

### Querying a Graph.

```php
use Redislabs\Module\RedisGraph\Query;

$matchQueryString = 'MATCH (p:person)-[v:visited {purpose:"pleasure"}]->(c:country)
	RETURN p.name, p.age, v.purpose, v.duration, c.name';
$matchQuery = new Query('TRAVELLERS', $matchQueryString);

$result = $redisGraph->query($matchQuery);
$labels = $result->getLabels();
$resultSet = $result->getResultSet();

var_dump($labels); // Dumps column labels
var_dump($resultSet[0]); // Dumps first result
...

$result->prettyPrint();

/* Prints

------------------------------------------------------
| p.name   | p.age | v.purpose | v.duration | c.name | 
------------------------------------------------------
| John Doe | 33    | pleasure  | two weeks  | Japan  | 
| Jane Doe | 30    | pleasure  | one weeks  | Japan  | 
------------------------------------------------------

*/

```

### Cypher Query Builder

This library does not provide Query Builder, but you can use **mper/cypher-querybuilder**


#### 1. Install the library.

```bash
composer require mper/cypher-querybuilder
```

#### 2. Then build your query

```php
<?php

use MP\Cypher\QueryBuilder;

$queryBuilder = new QueryBuilder();

$queryBuilder->addMatch()
    ->addNode('p', 'person')
    ->relation('v', 'visited', ['purpose' => 'pleasure'])->right()
    ->node('c', 'country');

$matchQueryString = $queryBuilder->getQuery('p.name', 'p.age', 'v.purpose', 'c.name');

echo $matchQueryString; 

// Prints "MATCH (p:person)-[v:visited {purpose:'pleasure'}]->(c:country) RETURN p.name,p.age,v.purpose,c.name"

```


## Test and Development

You can use Docker Image provided by Redislabs.

```bash
docker run -p 6379:6379 --name redis-redisgraph redislabs/redisgraph:latest
```
