<?php
namespace Tests\AppBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\LearningMaterial;
use AppBundle\Traits\LearningMaterialsEntity;
use Mockery as m;
use Tests\AppBundle\TestCase;

/**
 * @coversDefaultClass \AppBundle\Traits\LearningMaterialsEntity
 */

class LearningMaterialsEntityTest extends TestCase
{
    /**
     * @var LearningMaterialsEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = LearningMaterialsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers ::setLearningMaterials
     */
    public function testSetLearningMaterials()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(LearningMaterial::class));
        $collection->add(m::mock(LearningMaterial::class));
        $collection->add(m::mock(LearningMaterial::class));

        $this->traitObject->setLearningMaterials($collection);
        $this->assertEquals($collection, $this->traitObject->getLearningMaterials());
    }

    /**
     * @covers ::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $collection = new ArrayCollection();
        $one = m::mock(LearningMaterial::class);
        $two = m::mock(LearningMaterial::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setLearningMaterials($collection);
        $this->traitObject->removeLearningMaterial($one);
        $learningMaterials = $this->traitObject->getLearningMaterials();
        $this->assertEquals(1, $learningMaterials->count());
        $this->assertEquals($two, $learningMaterials->first());
    }
}