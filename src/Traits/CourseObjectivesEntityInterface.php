<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\CourseObjectiveInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface DescribableEntityInterface
 */
interface CourseObjectivesEntityInterface
{
    /**
     * @param Collection|CourseObjectiveInterface[] $courseObjectives
     */
    public function setCourseObjectives(Collection $courseObjectives = null): void;

    /**
     * @param CourseObjectiveInterface $courseObjective
     */
    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void;

    /**
     * @param CourseObjectiveInterface $courseObjective
     */
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void;

    /**
     * @return Collection|CourseObjectiveInterface[]
     */
    public function getCourseObjectives(): Collection;
}
