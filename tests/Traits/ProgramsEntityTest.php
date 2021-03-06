<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Program;
use App\Traits\ProgramsEntity;
use Mockery as m;
use App\Tests\TestCase;

/**
 * @coversDefaultClass \App\Traits\ProgramsEntity
 */

class ProgramsEntityTest extends TestCase
{
    /**
     * @var ProgramsEntity
     */
    private $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $traitName = ProgramsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * @covers ::setPrograms
     */
    public function testSetPrograms()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(Program::class));
        $collection->add(m::mock(Program::class));
        $collection->add(m::mock(Program::class));

        $this->traitObject->setPrograms($collection);
        $this->assertEquals($collection, $this->traitObject->getPrograms());
    }

    /**
     * @covers ::removeProgram
     */
    public function testRemoveProgram()
    {
        $collection = new ArrayCollection();
        $one = m::mock(Program::class);
        $two = m::mock(Program::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setPrograms($collection);
        $this->traitObject->removeProgram($one);
        $programs = $this->traitObject->getPrograms();
        $this->assertEquals(1, $programs->count());
        $this->assertEquals($two, $programs->first());
    }
}
