<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\SortableEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\SessionsEntityInterface;
use App\Traits\ProgramYearsEntityInterface;

/**
 * Interface ObjectiveInterface
 */
interface ObjectiveInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    MeshDescriptorsEntityInterface,
    SortableEntityInterface,
    ActivatableEntityInterface,
    IndexableCoursesEntityInterface,
    ProgramYearObjectivesEntityInterface,
    CourseObjectivesEntityInterface,
    SessionObjectivesEntityInterface
{
    /**
     * @param CompetencyInterface $competency
     */
    public function setCompetency(CompetencyInterface $competency);

    /**
     * @return CompetencyInterface
     */
    public function getCompetency();

    /**
     * @param Collection $parents
     */
    public function setParents(Collection $parents);

    /**
     * @param ObjectiveInterface $parent
     */
    public function addParent(ObjectiveInterface $parent);

    /**
     * @param ObjectiveInterface $parent
     */
    public function removeParent(ObjectiveInterface $parent);

    /**
     * @return Collection
     */
    public function getParents();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param ObjectiveInterface $child
     */
    public function addChild(ObjectiveInterface $child);

    /**
     * @param ObjectiveInterface $child
     */
    public function removeChild(ObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getChildren();

    /**
     * @param ObjectiveInterface $ancestor
     */
    public function setAncestor(ObjectiveInterface $ancestor);

    /**
     * @return ObjectiveInterface
     */
    public function getAncestor();

    /**
     * @return ObjectiveInterface
     */
    public function getAncestorOrSelf();

    /**
     * @param Collection $children
     */
    public function setDescendants(Collection $children);

    /**
     * @param ObjectiveInterface $child
     */
    public function addDescendant(ObjectiveInterface $child);

    /**
     * @param ObjectiveInterface $child
     */
    public function removeDescendant(ObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getDescendants();

    /**
     * @return array
     */
    public function getCourses(): array;

    /**
     * @return array
     */
    public function getProgramYears(): array;

    /**
     * @return array
     */
    public function getSessions(): array;
}
