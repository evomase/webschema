<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 10/02/2017
 * Time: 17:45
 */

namespace tests\WebSchema\Utils;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Utils\JsonLD;
use WebSchema\Utils\JsonLD\Node;

class JsonLDTest extends AbstractTestCase
{
    public function testCreate()
    {
        $author = new Node('author', 'Text', 'John Doe');
        $name = new Node('name', 'Text', 'How to Tie a Reef Knot');
        $stat = new Node('interactionStatistic', 'InteractionCounter');

        $counter1 = new Node('interactionStatistic', 'InteractionCounter', [
            'interactionType'      => 'http://schema.org/ShareAction',
            'userInteractionCount' => '1203'
        ]);

        $counter2 = new Node('interactionStatistic', 'InteractionCounter', [
            'interactionType'      => 'http://schema.org/CommentAction',
            'userInteractionCount' => '78'
        ]);

        $stat->add($counter1)->add($counter2);

        $data = [
            $author,
            $name,
            $stat
        ];

        JsonLD::create('Article', $data);
    }
}