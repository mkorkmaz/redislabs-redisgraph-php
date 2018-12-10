<?php
declare(strict_types=1);

namespace RedislabsModulesTest\Module;

use Redislabs\Module\RedisGraph\Node;
use ArrayIterator;

class NodeTest extends \Codeception\Test\Unit
{

    /**
     * @test
     */
    public function shouldCreateNodeObjectUsingNamedConstructorsSuccessfully() : void
    {
        $label1 =  'person';
        $label2 =  'country';

        $properties1 = ['name' => 'John Doe', 'age' => 33, 'gender' => 'male', 'status' => 'single'];
        $properties2 = ['name' => 'Japan'];

        $node1 = Node::create();
        $this->assertEquals(null, $node1->getLabel(), 'Node::create returns null label');
        $this->assertEquals(10, strlen($node1->getAlias()), 'Node::create returns its random alias');
        $node1WithLabel = $node1->withLabel('DefinedLabel');
        $this->assertEquals(
            'DefinedLabel',
            $node1WithLabel->getLabel(),
            'Node::withLabel returns its definedalias'
        );

        $node2 = Node::createWithLabel($label1)->withProperties($properties1)->withAlias('CatOwner');

        $this->assertEquals($label1, $node2->getLabel(), 'Node::createWithLabel returns its label');
        $this->assertEquals($properties1, $node2->getProperties(), 'Node::createWithLabel returns its properties');
        $this->assertEquals('CatOwner', $node2->getAlias(), 'Node::createWithLabel returns its defined label');

        $node3 = Node::createWithLabelAndProperties($label2, $properties2);
        $this->assertEquals($label2, $node3->getLabel(), 'Node::createWithLabelAndProperties returns its label');
        $this->assertEquals(
            $properties2,
            $node3->getProperties(),
            'Node::createWithLabelAndProperties returns its properties'
        );
        $this->assertEquals(
            10,
            strlen($node3->getAlias()),
            'Node::createWithLabelAndProperties returns random alias'
        );

        $this->assertEquals(
            '(CatOwner:person {name: "John Doe", age: "33", gender: "male", status: "single"})',
            $node2->toString()
        );
    }
}
