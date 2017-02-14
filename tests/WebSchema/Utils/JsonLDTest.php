<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 10/02/2017
 * Time: 17:45
 */

namespace WebSchema\Tests\Utils;

use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\JsonLD;
use WebSchema\Utils\JsonLD\Node;

class JsonLDTest extends AbstractTestCase
{
    public function testCreate()
    {
        $author = new Node(null, 'John Doe');
        $name = new Node(null, 'How to Tie a Reef Knot');
        $stat = new Node();

        $counter1 = new Node('InteractionCounter', [
            'interactionType'      => 'http://schema.org/ShareAction',
            'userInteractionCount' => '1203'
        ]);

        $counter2 = new Node('InteractionCounter', [
            'interactionType'      => 'http://schema.org/CommentAction',
            'userInteractionCount' => '78'
        ]);

        $stat->add($counter1)->add($counter2);

        $data = [
            'author'               => $author,
            'name'                 => $name,
            'interactionStatistic' => $stat
        ];

        $this->assertEquals(JsonLD::create(new Node('Article', $data)), json_encode([
            '@context'             => 'http://schema.org/',
            '@type'                => 'Article',
            'author'               => $author->getData(),
            'name'                 => $name->getData(),
            'interactionStatistic' => [
                [
                    '@type' => $counter1->getType(),
                ] + $counter1->getData(),

                [
                    '@type' => $counter2->getType(),
                ] + $counter2->getData()
            ]
        ]));
    }
}