<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshQualifier;
use DateTime;

/**
 * Tests for Entity MeshQualifier
 * @group model
 */
class MeshQualifierTest extends EntityBase
{
    /**
     * @var MeshQualifier
     */
    protected $object;

    /**
     * Instantiate a MeshQualifier object
     */
    protected function setUp(): void
    {
        $this->object = new MeshQualifier();
    }

    public function testNotBlankValidation()
    {
        $notBlank = [
            'name'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test_name');
        $this->validate(0);
    }
    /**
     * @covers \App\Entity\MeshQualifier::__construct
     */
    public function testConstructor()
    {
        $now = new DateTime();
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshQualifier::setName
     * @covers \App\Entity\MeshQualifier::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshQualifier::addDescriptor
     */
    public function testAddDescriptor()
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshQualifier::removeDescriptor
     */
    public function testRemoveDescriptor()
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshQualifier::getDescriptors
     * @covers \App\Entity\MeshQualifier::setDescriptors
     */
    public function getGetDescriptors()
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }
}
