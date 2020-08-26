<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AttributeMetadataTest extends TestCase
{
    public function testInterface()
    {
        $attributeMetadata = new AttributeMetadata('name');
        $this->assertInstanceOf('Symfony\Component\Serializer\Mapping\AttributeMetadataInterface', $attributeMetadata);
    }

    public function testGetName()
    {
        $attributeMetadata = new AttributeMetadata('name');
        $this->assertEquals('name', $attributeMetadata->getName());
    }

    public function testGroups()
    {
        $attributeMetadata = new AttributeMetadata('group');
        $attributeMetadata->addGroup('a');
        $attributeMetadata->addGroup('a');
        $attributeMetadata->addGroup('b');

        $this->assertEquals(['a', 'b'], $attributeMetadata->getGroups());
    }

    public function testMaxDepth()
    {
        $attributeMetadata = new AttributeMetadata('name');
        $attributeMetadata->setMaxDepth(69);

        $this->assertEquals(69, $attributeMetadata->getMaxDepth());
    }

    public function testSerializedName()
    {
        $attributeMetadata = new AttributeMetadata('name');
        $attributeMetadata->setSerializedName('serialized_name');

        $this->assertEquals('serialized_name', $attributeMetadata->getSerializedName());
    }

    public function testAddSerializedName()
    {
        $attributeMetadata = new AttributeMetadata('name');
        $attributeMetadata->addSerializedName('serialized_name', ['group1', 'group2']);

        $this->assertEquals('serialized_name', $attributeMetadata->getSerializedNameForGroups(['group1']));
        $this->assertEquals('serialized_name', $attributeMetadata->getSerializedNameForGroups(['group2']));
        $this->assertEquals('serialized_name', $attributeMetadata->getSerializedNameForGroups(['group1', 'group2']));
        $this->assertNull($attributeMetadata->getSerializedNameForGroups());

        $attributeMetadata->addSerializedName('serialized_name_group_3', ['group3']);
        $this->assertEquals('serialized_name_group_3', $attributeMetadata->getSerializedNameForGroups(['group3']));

        $attributeMetadata->addSerializedName('serialized_name_no_group');
        $this->assertEquals('serialized_name_no_group', $attributeMetadata->getSerializedNameForGroups());
    }

    public function testIgnore()
    {
        $attributeMetadata = new AttributeMetadata('ignored');
        $this->assertFalse($attributeMetadata->isIgnored());
        $attributeMetadata->setIgnore(true);
        $this->assertTrue($attributeMetadata->isIgnored());
    }

    public function testMerge()
    {
        $attributeMetadata1 = new AttributeMetadata('a1');
        $attributeMetadata1->addGroup('a');
        $attributeMetadata1->addGroup('b');

        $attributeMetadata2 = new AttributeMetadata('a2');
        $attributeMetadata2->addGroup('a');
        $attributeMetadata2->addGroup('c');
        $attributeMetadata2->setMaxDepth(2);
        $attributeMetadata2->setSerializedName('a3');

        $attributeMetadata2->setIgnore(true);

        $attributeMetadata1->merge($attributeMetadata2);

        $this->assertEquals(['a', 'b', 'c'], $attributeMetadata1->getGroups());
        $this->assertEquals(2, $attributeMetadata1->getMaxDepth());
        $this->assertEquals('a3', $attributeMetadata1->getSerializedName());
        $this->assertTrue($attributeMetadata1->isIgnored());
    }

    public function testSerialize()
    {
        $attributeMetadata = new AttributeMetadata('attribute');
        $attributeMetadata->addGroup('a');
        $attributeMetadata->addGroup('b');
        $attributeMetadata->setMaxDepth(3);
        $attributeMetadata->setSerializedName('serialized_name');

        $serialized = serialize($attributeMetadata);
        $this->assertEquals($attributeMetadata, unserialize($serialized));
    }
}
