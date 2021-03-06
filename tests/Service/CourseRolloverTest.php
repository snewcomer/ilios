<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Cohort;
use App\Entity\Course;
use App\Entity\CourseClerkshipType;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterial;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\CourseObjective;
use App\Entity\CourseObjectiveInterface;
use App\Entity\IlmSession;
use App\Entity\IlmSessionInterface;
use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\LearningMaterial;
use App\Entity\Manager\BaseManager;
use App\Entity\Manager\CohortManager;
use App\Entity\Manager\CourseLearningMaterialManager;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\IlmSessionManager;
use App\Entity\Manager\LearningMaterialManager;
use App\Entity\Manager\OfferingManager;
use App\Entity\Manager\SessionLearningMaterialManager;
use App\Entity\Manager\SessionManager;
use App\Entity\MeshDescriptor;
use App\Entity\Offering;
use App\Entity\OfferingInterface;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearObjective;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterial;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\SessionObjective;
use App\Entity\SessionObjectiveInterface;
use App\Entity\SessionType;
use App\Entity\Term;
use App\Entity\User;
use App\Service\CourseRollover;
use App\Tests\TestCase;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Mockery as m;

/**
 * Class CourseRolloverTest
 */
class CourseRolloverTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $courseManager;

    /**
     * @var m\MockInterface
     */
    protected $learningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $courseLearningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionLearningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $offeringManager;

    /**
     * @var m\MockInterface
     */
    protected $ilmSessionManager;

    /**
     * @var m\MockInterface
     */
    protected $cohortManager;

    /**
     * @var m\MockInterface
     */
    protected $courseObjectiveManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionObjectiveManager;

    /**
     * @var CourseRollover
     */
    protected $service;


    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->courseManager = m::mock(CourseManager::class);
        $this->learningMaterialManager = m::mock(LearningMaterialManager::class);
        $this->courseLearningMaterialManager = m::mock(CourseLearningMaterialManager::class);
        $this->sessionManager = m::mock(SessionManager::class);
        $this->sessionLearningMaterialManager = m::mock(SessionLearningMaterialManager::class);
        $this->offeringManager = m::mock(OfferingManager::class);
        $this->ilmSessionManager = m::mock(IlmSessionManager::class);
        $this->cohortManager = m::mock(CohortManager::class);
        $this->sessionObjectiveManager = m::mock(BaseManager::class);
        $this->courseObjectiveManager = m::mock(BaseManager::class);
        $this->service = new CourseRollover(
            $this->courseManager,
            $this->learningMaterialManager,
            $this->courseLearningMaterialManager,
            $this->sessionManager,
            $this->sessionLearningMaterialManager,
            $this->offeringManager,
            $this->ilmSessionManager,
            $this->cohortManager,
            $this->courseObjectiveManager,
            $this->sessionObjectiveManager
        );
    }

    /**
     * @inheritdoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->courseManager);
        unset($this->learningMaterialManager);
        unset($this->courseLearningMaterialManager);
        unset($this->sessionManager);
        unset($this->sessionLearningMaterialManager);
        unset($this->offeringManager);
        unset($this->ilmSessionManager);
        unset($this->cohortManager);
        unset($this->courseObjectiveManager);
        unset($this->sessionObjectiveManager);
        unset($this->service);
    }

    public function testRolloverWithEverything()
    {
        $course = $this->createTestCourseWithAssociations();
        $newCourse = m::mock(CourseInterface::class);
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newCourse->shouldReceive('setTitle')->with($course->getTitle())->once();
        $newCourse->shouldReceive('setYear')->with($newYear)->once();
        $newCourse->shouldReceive('setLevel')->with($course->getLevel())->once();
        $newCourse->shouldReceive('setExternalId')->with($course->getExternalId())->once();

        $newCourse->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course) {
            $oldStart = $course->getStartDate();
            return (
                //day of the week is the same
                $oldStart->format('w') === $newStart->format('w') &&
                //Week of the year is the same
                $oldStart->format('W') === $newStart->format('W')
            );
        }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is the same
                $oldEnd->format('W') === $newEnd->format('W')
            );
        }))->once();
        $newCourse->shouldReceive('setClerkshipType')->with($course->getClerkshipType())->once();
        $newCourse->shouldReceive('setSchool')->with($course->getSchool())->once();
        $newCourse->shouldReceive('setDirectors')->with($course->getDirectors())->once();
        $newCourse->shouldReceive('setAdministrators')->with($course->getAdministrators())->once();
        $newCourse->shouldReceive('setTerms')->with($course->getTerms())->once();
        $newCourse->shouldReceive('setMeshDescriptors')->with($course->getMeshDescriptors())->once();

        $ancestor = $course->getAncestor();
        $newCourse->shouldReceive('setAncestor')->with($ancestor)->once();

        /* @var CourseObjectiveInterface $objective */
        foreach ($course->getCourseObjectives() as $courseObjective) {
            $newCourseObjective = m::mock(CourseObjectiveInterface::class);
            $newCourseObjective->shouldReceive('setTitle')->with($courseObjective->getTitle())->once();
            $newCourseObjective->shouldReceive('setMeshDescriptors')
                ->with($courseObjective->getMeshDescriptors())->once();
            $newCourseObjective->shouldReceive('setPosition')->with($courseObjective->getPosition())->once();
            $newCourseObjective->shouldReceive('setCourse')->with($newCourse)->once();
            $newCourseObjective->shouldReceive('setTerms')->with($courseObjective->getTerms())->once();

            $ancestor = $courseObjective->getAncestor();
            if ($ancestor) {
                $newCourseObjective->shouldReceive('setAncestor')->with($ancestor)->once();
            } else {
                $newCourseObjective->shouldReceive('setAncestor')->with($courseObjective)->once();
            }

            $this->courseObjectiveManager
                ->shouldReceive('create')->once()
                ->andReturn($newCourseObjective);
            $this->courseObjectiveManager->shouldReceive('update')
                ->once()->withArgs([$newCourseObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock(CourseLearningMaterialInterface::class);
            $newLearningMaterial->shouldReceive('setLearningMaterial')
                ->with($learningMaterial->getLearningMaterial())->once();
            $newLearningMaterial->shouldReceive('setCourse')->with($newCourse)->once();
            $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
            $newLearningMaterial->shouldReceive('setPublicNotes')->with($learningMaterial->hasPublicNotes())->once();
            $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
            $newLearningMaterial->shouldReceive('setMeshDescriptors')
                ->with($learningMaterial->getMeshDescriptors())->once();
            $newLearningMaterial->shouldReceive('setPosition')->with($learningMaterial->getPosition())->once();

            $this->courseLearningMaterialManager
                ->shouldReceive('create')->once()
                ->andReturn($newLearningMaterial);
            $this->courseLearningMaterialManager->shouldReceive('update')->once()
                ->withArgs([$newLearningMaterial, false, false]);
        }

        /* @var SessionInterface $session */
        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldReceive('setTitle')->with($session->getTitle())->once();
            $newSession->shouldReceive('setDescription')->with($session->getDescription())->once();
            $newSession->shouldReceive('setCourse')->with($newCourse)->once();
            $newSession->shouldReceive('setAttireRequired')->with($session->isAttireRequired())->once();
            $newSession->shouldReceive('setEquipmentRequired')->with($session->isEquipmentRequired())->once();
            $newSession->shouldReceive('setSessionType')->with($session->getSessionType())->once();
            $newSession->shouldReceive('setSupplemental')->with($session->isSupplemental())->once();
            $newSession->shouldReceive('setPublished')->with(false)->once();
            $newSession->shouldReceive('setPublishedAsTbd')->with(false)->once();
            $newSession->shouldReceive('setInstructionalNotes')->with($session->getInstructionalNotes())->once();
            $newSession->shouldReceive('setMeshDescriptors')->with($session->getMeshDescriptors())->once();
            $newSession->shouldReceive('setTerms')->with($session->getTerms())->once();
            $this->sessionManager
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            /** @var SessionObjectiveInterface $sessionObjective */
            foreach ($session->getSessionObjectives() as $sessionObjective) {
                $newSessionObjective = m::mock(SessionObjectiveInterface::class);
                $newSessionObjective->shouldReceive('setTitle')->with($sessionObjective->getTitle())->once();
                $newSessionObjective->shouldReceive('setMeshDescriptors')
                    ->with($sessionObjective->getMeshDescriptors())->once();
                $newSessionObjective->shouldReceive('setPosition')->with($sessionObjective->getPosition())->once();
                $newSessionObjective->shouldReceive('setSession')->with($newSession)->once();
                $newSessionObjective->shouldReceive('setTerms')->with($sessionObjective->getTerms())->once();
                $newSessionObjective->shouldReceive('setCourseObjectives')
                    ->with(m::on(function (Collection $collection) use ($sessionObjective) {
                        return count($collection) === count($sessionObjective->getCourseObjectives());
                    }))->once();
                $ancestor = $sessionObjective->getAncestor();
                if ($ancestor) {
                    $newSessionObjective->shouldReceive('setAncestor')->with($ancestor)->once();
                } else {
                    $newSessionObjective->shouldReceive('setAncestor')->with($sessionObjective)->once();
                }

                $this->sessionObjectiveManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newSessionObjective);
                $this->sessionObjectiveManager->shouldReceive('update')->withArgs([$newSessionObjective, false, false]);
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock(SessionLearningMaterialInterface::class);
                $newLearningMaterial->shouldReceive('setLearningMaterial')
                    ->with($learningMaterial->getLearningMaterial())->once();
                $newLearningMaterial->shouldReceive('setSession')->with($newSession)->once();
                $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
                $newLearningMaterial->shouldReceive('setPublicNotes')
                    ->with($learningMaterial->hasPublicNotes())->once();
                $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
                $newLearningMaterial->shouldReceive('setPosition')->with($learningMaterial->getPosition())->once();

                $newLearningMaterial->shouldReceive('setMeshDescriptors')
                    ->with($learningMaterial->getMeshDescriptors())->once();
                $this->sessionLearningMaterialManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialManager->shouldReceive('update')->once()
                    ->withArgs([$newLearningMaterial, false, false]);
            }

            if ($oldIlmSession = $session->getIlmSession()) {
                $newIlmSession = m::mock(IlmSessionInterface::class);
                $newIlmSession->shouldReceive('setHours')->with($oldIlmSession->getHours())->once();
                $newIlmSession->shouldReceive('setDueDate')
                    ->with(m::on(function (DateTime $newDueDate) use ($oldIlmSession) {
                        $oldDueDate = $oldIlmSession->getDueDate();
                        return (
                            //day of the week is the same
                            $oldDueDate->format('w') === $newDueDate->format('w') &&
                            //Week of the year is the same
                            $oldDueDate->format('W') === $newDueDate->format('W')
                        );
                    }))->once();
                $newSession->shouldReceive('setIlmSession')->with($newIlmSession)->once();
                $this->ilmSessionManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newIlmSession);
                $this->ilmSessionManager->shouldReceive('update')->once()
                    ->withArgs([$newIlmSession, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldReceive('setRoom')->once()->with($offering->getRoom());
                $newOffering->shouldReceive('setSite')->once()->with($offering->getSite());
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $oldStart->format('W') === $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $oldEnd->format('W') === $newEnd->format('W')
                    );
                }))->once();

                $newOffering->shouldReceive('setSession')->once()->with($newSession);
                $newOffering->shouldReceive('setInstructors')->once()->with($offering->getInstructors());
                $newOffering->shouldReceive('setInstructorGroups')->once()->with($offering->getInstructorGroups());
                $newOffering->shouldNotReceive('setLearnerGroups');
                $newOffering->shouldNotReceive('setLearners');

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }

        $newCourse->shouldReceive('getCohorts')->once()->andReturn(new ArrayCollection());
        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, []);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithYearFarInTheFuture()
    {
        $course = $this->createTestCourseWithOfferings();


        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse, 15);
        $newCourse->shouldReceive('setYear')->with($newYear)->once();

        $newCourse->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course) {
            $oldStart = $course->getStartDate();
            return (
                //day of the week is the same
                $oldStart->format('w') === $newStart->format('w') &&
                //Week of the year is the same
                $oldStart->format('W') === $newStart->format('W')
            );
        }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is the same
                $oldEnd->format('W') === $newEnd->format('W')
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $oldStart->format('W') === $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $oldEnd->format('W') === $newEnd->format('W')
                    );
                }))->once();

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, []);
    }

    public function testRolloverWithSpecificStartDate()
    {
        $course = $this->createTestCourseWithOfferings();

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newCourse->shouldReceive('setYear')->with($newYear)->once();

        $newStartDate = clone $course->getStartDate();
        $newStartDate->setISODate(
            $newYear,
            (int) $course->getStartDate()->format('W') + 2,
            (int) $course->getStartDate()->format('N')
        );
        $this->assertEquals($course->getStartDate()->format('w'), $newStartDate->format('w'));

        $newCourse
            ->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course, $newStartDate) {
                $oldStart = $course->getStartDate();
                $oldStartWeekOfYear = (int) $oldStart->format('W');
                $newStartWeekOfYear = (int) $newStart->format('W');
                $weeksDiff = 0;
                if ($newStartWeekOfYear > $oldStartWeekOfYear) {
                    $weeksDiff = $newStartWeekOfYear - $oldStartWeekOfYear;
                } elseif ($newStartWeekOfYear < $oldStartWeekOfYear) {
                    /* @link http://stackoverflow.com/a/21480444 */
                    $yearPreviousToNewYear = $newStart->format('Y') - 1;
                    $weeksInPreviousYear = (int) (new DateTime("December 28th, ${yearPreviousToNewYear}"))->format('W');
                    $weeksDiff = ($weeksInPreviousYear - $oldStartWeekOfYear) + $newStartWeekOfYear;
                }
                return (
                    $newStart->format('c') === $newStartDate->format('c')
                    // day of the week is the same
                    && $oldStart->format('w') === $newStart->format('w')
                    // dates are two weeks apart
                    && 2 === $weeksDiff
                );
            }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            $oldEndWeekOfYear = (int) $oldEnd->format('W');
            $newEndWeekOfYear = (int) $newEnd->format('W');
            $weeksDiff = 0;
            if ($newEndWeekOfYear > $oldEndWeekOfYear) {
                $weeksDiff = $newEndWeekOfYear - $oldEndWeekOfYear;
            } elseif ($newEndWeekOfYear < $oldEndWeekOfYear) {
                $yearPreviousToNewYear = $newEnd->format('Y') - 1;
                $weeksInPreviousYear = (int) (new DateTime("December 28th, ${yearPreviousToNewYear}"))->format('W');
                $weeksDiff = ($weeksInPreviousYear - $oldEndWeekOfYear) + $newEndWeekOfYear;
            }
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w')
                // dates are two weeks apart
                && 2 === $weeksDiff
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    $oldStartWeekOfYear = (int) $oldStart->format('W');
                    $newStartWeekOfYear = (int) $newStart->format('W');
                    $weeksDiff = 0;
                    if ($newStartWeekOfYear > $oldStartWeekOfYear) {
                        $weeksDiff = $newStartWeekOfYear - $oldStartWeekOfYear;
                    } elseif ($newStartWeekOfYear < $oldStartWeekOfYear) {
                        $yearPreviousToNewYear = $newStart->format('Y') - 1;
                        $weeksInPreviousYear
                            = (int) (new DateTime("December 28th, ${yearPreviousToNewYear}"))->format('W');
                        $weeksDiff = ($weeksInPreviousYear - $oldStartWeekOfYear) + $newStartWeekOfYear;
                    }
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //dates are two weeks apart
                        2 === $weeksDiff
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    $oldEndWeekOfYear = (int) $oldEnd->format('W');
                    $newEndWeekOfYear = (int) $newEnd->format('W');
                    $weeksDiff = 0;
                    if ($newEndWeekOfYear > $oldEndWeekOfYear) {
                        $weeksDiff = $newEndWeekOfYear - $oldEndWeekOfYear;
                    } elseif ($newEndWeekOfYear < $oldEndWeekOfYear) {
                        $yearPreviousToNewYear = $newEnd->format('Y') - 1;
                        $weeksInPreviousYear
                            = (int) (new DateTime("December 28th, ${yearPreviousToNewYear}"))->format('W');
                        $weeksDiff = ($weeksInPreviousYear - $oldEndWeekOfYear) + $newEndWeekOfYear;
                    }
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w')
                        // dates are two weeks apart
                        && 2 === $weeksDiff
                    );
                }))->once();

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }


    public function testRolloverWithInSameYearWithNewStartDate()
    {
        $course = $this->createTestCourseWithOfferings();

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();

        $newYear = $course->getYear();
        $newTitle = $course->getTitle();

        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $newTitle, 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();
        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);
        $this->courseManager->shouldReceive('flushAndClear')->once();

        $newCourse->shouldReceive('setYear')->with($newYear)->once();
        $newCourse->shouldReceive('setTitle')->with($newTitle)->once();

        $newStartDate = clone $course->getStartDate();
        //start the new course 16 weeks (112 days) later
        $newStartDate->add(new DateInterval('P112D'));

        $newCourse
            ->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course, $newStartDate) {
                $oldStart = $course->getStartDate();
                $expectedStartWeek = (int) $oldStart->format('W') + 16;
                if ($expectedStartWeek > 52) {
                    $expectedStartWeek = $expectedStartWeek - 52;
                }
                return (
                    $newStart->format('c') === $newStartDate->format('c') &&
                    //day of the week is the same
                    $oldStart->format('w') === $newStart->format('w') &&
                    //Week of the year is two weeks later
                    $expectedStartWeek ===  (int) $newStart->format('W')
                );
            }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            $expectedEndWeek = (int) $oldEnd->format('W') + 16;
            if ($expectedEndWeek > 52) {
                $expectedEndWeek = $expectedEndWeek - 52;
            }
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is two weeks laters
                $expectedEndWeek ===  (int) $newEnd->format('W')
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    $expectedStartWeek = (int) $oldStart->format('W') + 16;
                    if ($expectedStartWeek > 52) {
                        $expectedStartWeek = $expectedStartWeek - 52;
                    }
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $expectedStartWeek ===  (int) $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    $expectedEndWeek = (int) $oldEnd->format('W') + 16;
                    if ($expectedEndWeek > 52) {
                        $expectedEndWeek = $expectedEndWeek - 52;
                    }
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $expectedEndWeek ===  (int) $newEnd->format('W')
                    );
                }))->once();

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }

    public function testRolloverSessionObjectiveWithOrphanedParents()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $courseXObjective = new CourseObjective();
        $courseXObjective->setId(13);
        $courseXObjective->setTitle('test');
        $course->addCourseObjective($courseXObjective);

        $session = new Session();
        $session->setSessionType(new SessionType());
        $sessionXObjective = new SessionObjective();
        $sessionXObjective->addCourseObjective(new CourseObjective());
        $sessionXObjective->addCourseObjective($courseXObjective);
        $session->addSessionObjective($sessionXObjective);
        $course->addSession($session);

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newSession = m::mock(SessionInterface::class);
        $newSession->shouldIgnoreMissing();
        $newCourseObjective = m::mock(CourseObjectiveInterface::class);
        $newCourseObjective->shouldIgnoreMissing();
        $newSessionObjective = m::mock(SessionObjectiveInterface::class);
        $newSessionObjective->shouldIgnoreMissing();

        //We should end up with 1 parent since the other one is an orphan
        $newSessionObjective->shouldReceive('setCourseObjectives')
            ->with(m::on(function (Collection $collection) use ($newCourseObjective) {
                $this->assertEquals($collection->count(), 1);
                $this->assertEquals($newCourseObjective, $collection->first());
                return count($collection) === 1;
            }));
        $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
        $this->courseObjectiveManager->shouldReceive('create')->once()->andReturn($newCourseObjective);
        $this->sessionObjectiveManager->shouldReceive('create')->once()->andReturn($newSessionObjective);

        $this->sessionManager->shouldIgnoreMissing();
        $this->sessionObjectiveManager->shouldIgnoreMissing();
        $this->courseObjectiveManager->shouldIgnoreMissing();

        $newCourse->shouldReceive('getCohorts')->once()->andReturn(new ArrayCollection());
        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['']);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverInSameYearKeepsRelationships()
    {
        $course = $this->createTestCourseWithAssociations();
        $newCourse = m::mock(CourseInterface::class);
        $newYear = $course->getYear();
        $newTitle = $course->getTitle() . ' again';
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $newTitle, 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseManager->shouldReceive('flushAndClear')->once();
        $newCourse->shouldReceive('setCohorts')->with($course->getCohorts());
        $newCourse->shouldReceive('getCohorts')->once()->andReturn($course->getCohorts());
        $newCourse->shouldIgnoreMissing();

        /** @var CourseObjectiveInterface $courseObjective */
        foreach ($course->getCourseObjectives() as $courseObjective) {
            $newCourseObjective = m::mock(CourseObjectiveInterface::class);
            $newCourseObjective->shouldReceive('setCourse')->with($newCourse)->once();
            $newCourseObjective->shouldReceive('setPosition')->with($courseObjective->getPosition())->once();
            $newCourseObjective->shouldReceive('setTitle')->with($courseObjective->getTitle())->once();
            $newCourseObjective->shouldReceive('setAncestor')->with($courseObjective->getAncestorOrSelf())->once();
            $newCourseObjective->shouldReceive('setMeshDescriptors')
                ->with($courseObjective->getMeshDescriptors())->once();
            $newCourseObjective->shouldReceive('setProgramYearObjectives')
                ->with($courseObjective->getProgramYearObjectives());
            $newCourseObjective->shouldReceive('setTerms')->with($courseObjective->getTerms())->once();

            $this->courseObjectiveManager->shouldReceive('create')->once()->andReturn($newCourseObjective);
            $this->courseObjectiveManager
                ->shouldReceive('update')->once()->withArgs([$newCourseObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock(CourseLearningMaterialInterface::class);
            $newLearningMaterial->shouldIgnoreMissing();
            $this->courseLearningMaterialManager->shouldReceive('create')->once()->andReturn($newLearningMaterial);
            $this->courseLearningMaterialManager->shouldIgnoreMissing();
        }

        /* @var SessionInterface $session */
        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();
            $this->sessionManager
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            /** @var SessionObjectiveInterface $sessionObjective */
            foreach ($session->getSessionObjectives() as $sessionObjective) {
                $newSessionObjective = m::mock(SessionObjectiveInterface::class);
                $newSessionObjective->shouldReceive('setSession')->with($newSession)->once();
                $newSessionObjective->shouldReceive('setPosition')->with($sessionObjective->getPosition())->once();
                $newSessionObjective->shouldReceive('setTitle')->with($sessionObjective->getTitle())->once();
                $newSessionObjective->shouldReceive('setMeshDescriptors')
                    ->with($sessionObjective->getMeshDescriptors())->once();
                $newSessionObjective->shouldReceive('setAncestor')
                    ->with($sessionObjective->getAncestorOrSelf())->once();
                $newSessionObjective->shouldReceive('setTerms')->with($sessionObjective->getTerms())->once();

                $newSessionObjective->shouldReceive('setCourseObjectives')
                    ->with(m::on(function (Collection $collection) use ($sessionObjective) {
                        return count($collection) === count($sessionObjective->getCourseObjectives());
                    }))->once();

                $this->sessionObjectiveManager->shouldReceive('create')->once()->andReturn($newSessionObjective);
                $this->sessionObjectiveManager->shouldReceive('update')->withArgs([$newSessionObjective, false, false]);
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock(SessionLearningMaterialInterface::class);
                $newLearningMaterial->shouldIgnoreMissing();
                $this->sessionLearningMaterialManager->shouldReceive('create')->once()->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialManager->shouldIgnoreMissing();
            }

            if ($oldIlmSession = $session->getIlmSession()) {
                $newIlmSession = m::mock(IlmSessionInterface::class);
                $newIlmSession->shouldReceive('setHours')->with($oldIlmSession->getHours())->once();
                $newIlmSession->shouldReceive('setDueDate')
                    ->with(m::on(function (DateTime $newDueDate) use ($oldIlmSession) {
                        $oldDueDate = $oldIlmSession->getDueDate();
                        return (
                            //day of the week is the same
                            $oldDueDate->format('w') === $newDueDate->format('w') &&
                            //Week of the year is the same
                            $oldDueDate->format('W') === $newDueDate->format('W')
                        );
                    }))->once();
                $newSession->shouldReceive('setIlmSession')->with($newIlmSession)->once();
                $this->ilmSessionManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newIlmSession);
                $this->ilmSessionManager->shouldReceive('update')->once()
                    ->withArgs([$newIlmSession, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldReceive('setRoom')->once()->with($offering->getRoom());
                $newOffering->shouldReceive('setSite')->once()->with($offering->getSite());
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $oldStart->format('W') === $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $oldEnd->format('W') === $newEnd->format('W')
                    );
                }))->once();

                $newOffering->shouldReceive('setSession')->once()->with($newSession);
                $newOffering->shouldReceive('setInstructors')->once()->with($offering->getInstructors());
                $newOffering->shouldReceive('setInstructorGroups')->once()->with($offering->getInstructorGroups());
                $newOffering->shouldNotReceive('setLearnerGroups');
                $newOffering->shouldNotReceive('setLearners');
                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }
        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['new-course-title' => $newTitle]);

        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithEmptyClerkshipType()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setClerkshipType');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);
    }

    public function testRolloverWithNewStartDate()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessions()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseLearningMaterials()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addLearningMaterial(new CourseLearningMaterial());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addLearningMaterial');
        $this->courseLearningMaterialManager->shouldNotReceive('create');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-learning-materials' => true]);
    }

    public function testRolloverWithoutCourseObjectives()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $courseObjective = new CourseObjective();
        $course->addCourseObjective($courseObjective);
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addCourseObjective');
        $this->courseObjectiveManager->shouldNotReceive('create');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-objectives' => true]);
    }

    public function testRolloverWithoutCourseTerms()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addTerm(new Term());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setTerms');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-terms' => true]);
    }

    public function testRolloverWithoutCourseMesh()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addMeshDescriptor(new MeshDescriptor());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setMeshDescriptors');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-mesh' => true]);
    }

    public function testRolloverWithoutCourseAncestor()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldReceive('setAncestor')->with($course)->once();
        $newCourse->shouldReceive('getCohorts')->once()->andReturn(new ArrayCollection());
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, []);
    }

    public function testRolloverWithoutSessionLearningMaterials()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionObjectives()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionTerms()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionMesh()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutOfferings()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructors()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructorGroups()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithNewCourseTitle()
    {
        $this->markTestIncomplete();
    }

    // @todo test the hell out of this. use a data provider here. [ST 2016/06/17]
    public function testRolloverOffsetCalculation()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverFailsOnDuplicate()
    {
        $course = $this->createTestCourse();
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $course->getId()]])->andReturn($course);
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(new Course());

        $this->expectException(
            Exception::class,
            "Another course with the same title and academic year already exists."
            . " If the year is correct, consider setting a new course title with '--new-course-title' option."
        );

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);
    }

    public function testRolloverFailsOnYearPast()
    {
        $courseId = 10;
        $pastDate = new DateTime();
        $pastDate->add(DateInterval::createFromDateString('-2 year'));
        $year = (int) $pastDate->format('Y');

        $this->expectException(
            Exception::class,
            "Courses cannot be rolled over to a new year before"
        );

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnMissingCourse()
    {
        $courseId = -1;
        $futureDate = new DateTime();
        $futureDate->add(DateInterval::createFromDateString('+2 year'));
        $year = (int) $futureDate->format('Y');
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $courseId]])->andReturn(false);

        $this->expectException(Exception::class, "There are no courses with courseId {$courseId}.");

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnStartDateOnDifferentDay()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(false)->once();

        $newStartDate = clone $course->getStartDate();
        $newStartDate->add(new DateInterval('P1Y2D'));

        $this->expectException(
            Exception::class,
            "The new start date must take place on the same day of the week as the original course start date"
        );
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }

    public function testRolloverCohortAndReLinkObjectives()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);
        $pyXObjective = new ProgramYearObjective();
        $pyXObjective->setId(1);
        $pyXObjective->setTitle('test program year objective');
        $programYear->addProgramYearObjective($pyXObjective);

        $newProgramYear = new ProgramYear();
        $newCohort = new Cohort();
        $newCohort->setId(11);
        $newCohort->setProgramYear($newProgramYear);
        $newProgramYear->setCohort($newCohort);
        $newPyXObjective = new ProgramYearObjective();
        $newPyXObjective->setId(1);
        $newPyXObjective->setTitle('test program year objective');
        $newPyXObjective->setAncestor($pyXObjective);

        $newProgramYear->addProgramYearObjective($newPyXObjective);

        $courseXObjective1 = new CourseObjective();
        $courseXObjective1->setId(808);
        $courseXObjective1->setTitle('test course objective1');
        $courseXObjective1->addProgramYearObjective($pyXObjective);
        $course->addCourseObjective($courseXObjective1);

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newCourse->shouldReceive('setAncestor')->with($course)->once();
        $newCourse->shouldReceive('addCohort')->once()->with($newCohort);
        $newCourse->shouldReceive('getCohorts')->andReturn(new ArrayCollection([$newCohort]));



        $newCourseXObjective = m::mock(CourseObjectiveInterface::class);
        $newCourseXObjective->shouldReceive('setCourse')->with($newCourse)->once();
        $newCourseXObjective->shouldReceive('setTerms')->with($courseXObjective1->getTerms())->once();
        $newCourseXObjective->shouldReceive('setPosition')->with($courseXObjective1->getPosition())->once();
        $newCourseXObjective->shouldReceive('setTitle')->with('test course objective1')->once();
        $newCourseXObjective->shouldReceive('setAncestor')->with($courseXObjective1)->once();
        $newCourseXObjective->shouldReceive('addProgramYearObjective')->with($newPyXObjective)->once();
        $newCourseXObjective->shouldReceive('setMeshDescriptors')
            ->with($courseXObjective1->getMeshDescriptors())->once();

        $this->cohortManager->shouldReceive('findOneBy')->with(['id' => 11])->andReturn($newCohort);


        $this->courseObjectiveManager->shouldReceive('create')->andReturn($newCourseXObjective);
        $this->courseObjectiveManager
            ->shouldReceive('update')->once()->withArgs([$newCourseXObjective, false, false]);

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, [], [11]);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverLinkedSessions()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $session1 = new Session();
        $session1->setId(1);
        $session1->setSessionType(new SessionType());
        $course->addSession($session1);

        $session2 = new Session();
        $session2->setId(2);
        $session2->setSessionType(new SessionType());
        $session2->setPostrequisite($session1);

        $course->addSession($session2);

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $firstNewSession = m::mock(SessionInterface::class);
        $firstNewSession->shouldIgnoreMissing();
        $this->sessionManager
            ->shouldReceive('create')->once()
            ->andReturn($firstNewSession);
        $this->sessionManager->shouldReceive('update')->withArgs([$firstNewSession, false, false])->once();

        $secondNewSession = m::mock(SessionInterface::class);
        $secondNewSession->shouldIgnoreMissing();
        $this->sessionManager
            ->shouldReceive('create')->once()
            ->andReturn($secondNewSession);
        $this->sessionManager->shouldReceive('update')->withArgs([$secondNewSession, false, false])->twice();
        $secondNewSession->shouldReceive('setPostrequisite')->with($firstNewSession)->once();

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, [], []);
        $this->assertSame($newCourse, $rhett);
    }

    /**
     * Gets a basic filled out course
     *
     * @return Course
     */
    protected function createTestCourse()
    {
        $course = new Course();
        $course->setId(10);
        $course->setTitle('test course');
        $course->setLevel(1);
        $now = new DateTime();
        $course->setYear((int) $now->format('Y'));
        $course->setStartDate(new DateTime('yesterday'));
        $course->setEndDate(new DateTime('tomorrow'));
        $course->setExternalId('I45');
        $course->setLocked(true);
        $course->setArchived(true);
        $course->setPublished(true);
        $course->setPublishedAsTbd(true);

        return $course;
    }

    /**
     * Gets a course with a bunch of relationships attached
     * @return Course
     */
    protected function createTestCourseWithAssociations()
    {
        $course = $this->createTestCourse();

        $course->setClerkshipType(new CourseClerkshipType());
        $course->setSchool(new School());

        $objectiveTerm1 = new Term();
        $objectiveTerm2 = new Term();
        $objectiveTerm3 = new Term();

        $ancestorCourse = new Course();
        $ancestorCourse->setId(1);
        $ancestorCourse->setTitle('test ancestor course');
        $course->setAncestor($ancestorCourse);

        $ancestorCourseObjective = new CourseObjective();
        $ancestorCourseObjective->setId(1);
        $ancestorCourseObjective->setTitle('test ancestor objective');

        $courseXObjective1 = new CourseObjective();
        $courseXObjective1->setId(808);
        $courseXObjective1->setTitle('test course objective1');
        $courseXObjective1->addMeshDescriptor(new MeshDescriptor());
        $courseXObjective1->setCourse($course);
        $courseXObjective1->setPosition(10);
        $courseXObjective1->addTerm($objectiveTerm1);
        $courseXObjective1->addTerm($objectiveTerm2);
        $courseXObjective1->addTerm($objectiveTerm3);
        $courseXObjective1->addProgramYearObjective(new ProgramYearObjective());

        $course->addCourseObjective($courseXObjective1);

        $courseXObjective2 = new CourseObjective();
        $courseXObjective2->setId(42);
        $courseXObjective2->setTitle('test course objective2');
        $courseXObjective2->setAncestor($ancestorCourseObjective);
        $courseXObjective2->setCourse($course);

        $course->addCourseObjective($courseXObjective2);

        $courseTerm1 = new Term();
        $courseTerm1->setId(808);
        $course->addTerm($courseTerm1);

        $lm = new LearningMaterial();

        $courseLearningMaterial1 = new CourseLearningMaterial();
        $courseLearningMaterial1->setLearningMaterial($lm);
        $courseLearningMaterial1->setId(808);
        $courseLearningMaterial1->addMeshDescriptor(new MeshDescriptor());
        $courseLearningMaterial1->setNotes('notes');
        $courseLearningMaterial1->setPublicNotes(true);
        $courseLearningMaterial1->setRequired(false);
        $course->addLearningMaterial($courseLearningMaterial1);

        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);
        $course->addCohort($cohort);

        $session1 = new Session();
        $session1->setDescription('test description');
        $session1->setSessionType(new SessionType());

        $ancestorSessionObjective = new SessionObjective();
        $ancestorSessionObjective->setId(2);
        $ancestorSessionObjective->setTitle('test session ancestor');

        $sessionXObjective1 = new SessionObjective();
        $sessionXObjective1->setId(99);
        $sessionXObjective1->setTitle('test session objective 1');
        $sessionXObjective1->addMeshDescriptor(new MeshDescriptor());
        $sessionXObjective1->addCourseObjective($courseXObjective1);
        $sessionXObjective1->addCourseObjective($courseXObjective2);
        $sessionXObjective1->addTerm($objectiveTerm1);
        $sessionXObjective1->setSession($session1);
        $sessionXObjective1->setPosition(5);
        $session1->addSessionObjective($sessionXObjective1);

        $sessionXObjective2 = new SessionObjective();
        $sessionXObjective2->setId(9);
        $sessionXObjective2->setTitle('test session objective 2');
        $sessionXObjective2->addMeshDescriptor(new MeshDescriptor());
        $sessionXObjective2->addCourseObjective($courseXObjective1);
        $sessionXObjective2->setAncestor($ancestorSessionObjective);
        $sessionXObjective2->setSession($session1);
        $session1->addSessionObjective($sessionXObjective2);

        $sessionLearningMaterial1 = new SessionLearningMaterial();
        $sessionLearningMaterial1->setLearningMaterial($lm);
        $sessionLearningMaterial1->setId(808);
        $sessionLearningMaterial1->addMeshDescriptor(new MeshDescriptor());
        $sessionLearningMaterial1->setNotes('notes');
        $sessionLearningMaterial1->setPublicNotes(true);
        $sessionLearningMaterial1->setRequired(false);
        $session1->addLearningMaterial($sessionLearningMaterial1);

        $sessionTerm1 = new Term();
        $sessionTerm1->setId(808);
        $session1->addTerm($sessionTerm1);

        $user = new User();

        $offering1 = new Offering();
        $offering1->setRoom('111b');
        $offering1->setSite('Off Campus');
        $offering1->setStartDate(new DateTime('8am'));
        $offering1->setEndDate(new DateTime('9am'));
        $offering1->addInstructor($user);
        $offering1->addLearner($user);

        $instructorGroup = new InstructorGroup();
        $instructorGroup->addUser($user);
        $offering1->addInstructorGroup($instructorGroup);

        $learnerGroup = new LearnerGroup();
        $learnerGroup->addUser($user);
        $offering1->addLearnerGroup($learnerGroup);

        $session1->addOffering($offering1);

        $course->addSession($session1);

        $session2 = new Session();
        $session2->setSessionType(new SessionType());
        $ilm = new IlmSession();
        $ilm->setHours(4.3);
        $ilm->setDueDate(new DateTime());
        $ilm->addInstructorGroup($instructorGroup);
        $ilm->addLearnerGroup($learnerGroup);
        $ilm->addInstructor($user);
        $ilm->addLearner($user);
        $session2->setIlmSession($ilm);

        $course->addSession($session2);

        return $course;
    }

    /**
     * Gets a course with a few offerings to use in date testing
     *
     * @return Course
     */
    protected function createTestCourseWithOfferings()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $session = new Session();
        $session->setSessionType(new SessionType());
        $offering1 = new Offering();
        $offering1->setStartDate(new DateTime('8am'));
        $offering1->setEndDate(new DateTime('9am'));
        $session->addOffering($offering1);

        $offering2 = new Offering();
        $offering2->setStartDate(new DateTime('1pm tomorrow'));
        $offering2->setEndDate(new DateTime('10am next week'));
        $session->addOffering($offering2);

        $course->addSession($session);

        return $course;
    }

    /**
     * Setup the course manager mock to do basic stuff we need in most tests
     *
     * @param CourseInterface $course
     * @param CourseInterface $newCourse
     * @param int $interval the length of time in the future for the new academic year
     *
     * @return int
     */
    protected function setupCourseManager(CourseInterface $course, CourseInterface $newCourse, $interval = 1)
    {
        $newYear = $course->getYear() + $interval;
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseManager->shouldReceive('flushAndClear')->once();

        return $newYear;
    }
}
