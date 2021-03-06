<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\ProgramYearObjectiveInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface DescribableEntityInterface
 */
interface ProgramYearObjectivesEntityInterface
{
    /**
     * @param Collection|ProgramYearObjectiveInterface[] $programYearObjectives
     */
    public function setProgramYearObjectives(Collection $programYearObjectives = null): void;

    /**
     * @param ProgramYearObjectiveInterface $programYearObjective
     */
    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void;

    /**
     * @param ProgramYearObjectiveInterface $programYearObjective
     */
    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void;

    /**
     * @return Collection|ProgramYearObjectiveInterface[]
     */
    public function getProgramYearObjectives(): Collection;
}
