<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 13/02/2017
 * Time: 15:36
 */

namespace tests\WebSchema\Utils\JsonLD;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Utils\JsonLD\Node;

class NodeTest extends AbstractTestCase
{
    public function testToArray()
    {
        $author = new Node(null, 'John Doe');
        $name = new Node(null, 'How to Tie a Reef Knot');
        $stat = new Node();

        $counter1 = new Node('InteractionCounter', [
            'interactionType'      => 'http://schema.org/ShareAction',
            'userInteractionCount' => '1203',
            'interactionService'   => new Node('Website', [
                'name' => 'Twitter',
                'url'  => 'http://www.twitter.com'
            ])
        ]);

        $counter2 = new Node('InteractionCounter', [
            'interactionType'      => 'http://schema.org/CommentAction',
            'userInteractionCount' => '78'
        ]);

        $stat->add($counter1)->add($counter2);

        $outer = (new Node('Article', [
            'author'               => $author,
            'name'                 => $name,
            'interactionStatistic' => $stat
        ]))->toArray();

        $this->assertArrayHasKey('@type', $outer);
        $this->assertArrayHasKey('interactionStatistic', $outer);
        $this->assertNotEmpty($outer['author']);
        $this->assertCount(2, $outer['interactionStatistic']);
        $this->assertInternalType('array', $outer['interactionStatistic'][0]['interactionService']);
    }
}