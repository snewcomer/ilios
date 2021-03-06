<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CourseInterface;
use App\Entity\CourseObjective;
use App\Entity\CourseObjectiveInterface;
use App\Entity\Objective;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearObjective;
use App\Entity\Session;
use App\Entity\SessionInterface;
use App\Entity\SessionObjective;
use App\Entity\SessionObjectiveInterface;
use Mockery as m;

/**
 * Tests for Entity Objective
 * @group model
 */
class ObjectiveTest extends EntityBase
{
    /**
     * @var Objective
     */
    protected $object;

    /**
     * Instantiate a Objective object
     */
    protected function setUp(): void
    {
        $this->object = new Objective();
    }

    public function testNotBlankValidation()
    {
        $notBlank = [
            'title'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Objective::__construct
     */
    public function testConstructor()
    {
        $this->assertEquals(0, $this->object->getPosition());
        $this->assertEquals(true, $this->object->isActive());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getSessionObjectives());
        $this->assertEmpty($this->object->getCourseObjectives());
        $this->assertEmpty($this->object->getProgramYearObjectives());
        $this->assertEmpty($this->object->getDescendants());
        $this->assertEmpty($this->object->getParents());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\Objective::setTitle
     * @covers \App\Entity\Objective::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Objective::setCompetency
     * @covers \App\Entity\Objective::getCompetency
     */
    public function testSetCompetency()
    {
        $this->entitySetTest('competency', 'Competency');
    }


    /**
     * @covers \App\Entity\Objective::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('children', 'Objective', 'getChildren', 'addChild', 'addParent');
    }

    /**
     * @covers \App\Entity\Objective::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest(
            'children',
            'Objective',
            'getChildren',
            'addChild',
            'removeChild',
            'removeParent'
        );
    }

    /**
     * @covers \App\Entity\Objective::getChildren
     * @covers \App\Entity\Objective::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('children', 'Objective', 'getChildren', 'setChildren', 'addParent');
    }

    /**
     * @covers \App\Entity\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Objective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Objective::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::removeParent
     */
    public function testRemoveParent()
    {
        $this->entityCollectionRemoveTest('parent', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::getParents
     * @covers \App\Entity\Objective::setParents
     */
    public function testGetParents()
    {
        $this->entityCollectionSetTest('parent', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::setAncestor
     * @covers \App\Entity\Objective::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('App\Entity\Objective');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\Objective::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::getDescendants
     * @covers \App\Entity\Objective::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::setPosition
     * @covers \App\Entity\Objective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \App\Entity\Objective::setActive
     * @covers \App\Entity\Objective::isActive
     */
    public function testSetActive()
    {
        $this->booleanSetTest('active');
    }

    /**
     * @covers \App\Entity\Objective::addProgramYearObjective
     */
    public function testAddProgramYearObjective()
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Objective::removeProgramYearObjective
     */
    public function testRemoveProgramYearObjective()
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Objective::setProgramYearObjectives
     * @covers \App\Entity\Objective::getProgramYearObjectives
     */
    public function testGetProgramYearObjectives()
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Objective::addCourseObjective
     */
    public function testAddCourseObjective()
    {
        $this->entityCollectionAddTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Objective::removeCourseObjective
     */
    public function testRemoveCourseObjective()
    {
        $this->entityCollectionRemoveTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Objective::setCourseObjectives
     * @covers \App\Entity\Objective::getCourseObjectives
     */
    public function testGetCourseObjectives()
    {
        $this->entityCollectionSetTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Objective::addSessionObjective
     */
    public function testAddSessionObjective()
    {
        $this->entityCollectionAddTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Objective::removeSessionObjective
     */
    public function testRemoveSessionObjective()
    {
        $this->entityCollectionRemoveTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Objective::setSessionObjectives
     * @covers \App\Entity\Objective::getSessionObjectives
     */
    public function testGetSessionObjectives()
    {
        $this->entityCollectionSetTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Objective::getIndexableCourses
     */
    public function testGetIndexableCourses()
    {
        $course1 = m::mock(CourseInterface::class);
        $courseObjective = m::mock(CourseObjectiveInterface::class);
        $courseObjective->shouldReceive('getCourse')->once()->andReturn($course1);
        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getCourse')->once()->andReturn($course2);
        $sessionObjective = m::mock(SessionObjectiveInterface::class);
        $sessionObjective->shouldReceive('getSession')->once()->andReturn($session);

        $this->object->addCourseObjective($courseObjective);
        $this->object->addSessionObjective($sessionObjective);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals($rhett, [$course1, $course2]);
    }

    /**
     * @covers \App\Entity\Objective::getSessions
     */
    public function testGetSessions()
    {
        $session1 = new Session();
        $session2 = new Session();
        $sessionObjective1 = new SessionObjective();
        $sessionObjective1->setSession($session1);
        $this->object->addSessionObjective($sessionObjective1);
        $sessionObjective2 = new SessionObjective();
        $sessionObjective2->setSession($session2);
        $this->object->addSessionObjective($sessionObjective2);
        $sessions = $this->object->getSessions();
        $this->assertCount(2, $sessions);
        $this->assertContains($session1, $sessions);
        $this->assertContains($session2, $sessions);
    }

    /**
     * @covers \App\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $course1 = new Course();
        $course2 = new Course();
        $courseObjective1 = new CourseObjective();
        $courseObjective1->setCourse($course1);
        $this->object->addCourseObjective($courseObjective1);
        $courseObjective2 = new CourseObjective();
        $courseObjective2->setCourse($course2);
        $this->object->addCourseObjective($courseObjective2);
        $courses = $this->object->getCourses();
        $this->assertCount(2, $courses);
        $this->assertContains($course1, $courses);
        $this->assertContains($course2, $courses);
    }

    /**
     * @covers \App\Entity\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $programYear1 = new ProgramYear();
        $programYear2 = new ProgramYear();
        $programYearObjective1 = new ProgramYearObjective();
        $programYearObjective1->setProgramYear($programYear1);
        $this->object->addProgramYearObjective($programYearObjective1);
        $programYearObjective2 = new ProgramYearObjective();
        $programYearObjective2->setProgramYear($programYear2);
        $this->object->addProgramYearObjective($programYearObjective2);
        $programYears = $this->object->getProgramYears();
        $this->assertCount(2, $programYears);
        $this->assertContains($programYear1, $programYears);
        $this->assertContains($programYear2, $programYears);
    }
}
