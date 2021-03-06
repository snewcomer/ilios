<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntity;
use App\Traits\CourseObjectivesEntity;
use App\Traits\ProgramYearObjectivesEntity;
use App\Traits\SessionObjectivesEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\IdentifiableEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\SortableEntity;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;

/**
 * Class Objective
 *
 * @ORM\Table(name="objective")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\ObjectiveRepository")
 *
 * @IS\Entity
 */
class Objective implements ObjectiveInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use MeshDescriptorsEntity;
    use SortableEntity;
    use ActivatableEntity;
    use CourseObjectivesEntity;
    use SessionObjectivesEntity;
    use ProgramYearObjectivesEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="objective_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    protected $title;

    /**
     * @var CompetencyInterface
     *
     * @ORM\ManyToOne(targetEntity="Competency", inversedBy="objectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="competency_id", referencedColumnName="competency_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $competency;

    /**
     * @var ArrayCollection|CourseObjectiveInterface[]
     *
     * @ORM\OneToMany(targetEntity="CourseObjective", mappedBy="objective")
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $courseObjectives;

    /**
     * @var ArrayCollection|CourseObjectiveInterface[]
     *
     * @ORM\OneToMany(targetEntity="ProgramYearObjective", mappedBy="objective")
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $programYearObjectives;

    /**
     * @var ArrayCollection|SessionObjectiveInterface[]
     *
     * @ORM\OneToMany(targetEntity="SessionObjective", mappedBy="objective")
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionObjectives;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", inversedBy="children")
     * @ORM\JoinTable("objective_x_objective",
     *   joinColumns={@ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="parent_objective_id", referencedColumnName="objective_id")}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $parents;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", mappedBy="parents")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $children;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="objectives")
     * @ORM\JoinTable(name="objective_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $meshDescriptors;

    /**
     * @var ObjectiveInterface
     *
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="descendants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ancestor_id", referencedColumnName="objective_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $ancestor;

    /**
     * @var ObjectiveInterface
     *
     * @ORM\OneToMany(targetEntity="Objective", mappedBy="ancestor")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $descendants;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @deprecated
     */
    protected $position;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $active;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
        $this->active = true;
        $this->courseObjectives = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
        $this->sessionObjectives = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function setCompetency(CompetencyInterface $competency = null)
    {
        $this->competency = $competency;
    }

    /**
     * @return CompetencyInterface
     */
    public function getCompetency()
    {
        return $this->competency;
    }

    /**
     * @param Collection $parents
     */
    public function setParents(Collection $parents)
    {
        $this->parents = new ArrayCollection();

        foreach ($parents as $parent) {
            $this->addParent($parent);
        }
    }

    /**
     * @param ObjectiveInterface $parent
     */
    public function addParent(ObjectiveInterface $parent)
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
        }
    }

    /**
     * @param ObjectiveInterface $parent
     */
    public function removeParent(ObjectiveInterface $parent)
    {
        $this->parents->removeElement($parent);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param ObjectiveInterface $child
     */
    public function addChild(ObjectiveInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->addParent($this);
        }
    }

    /**
     * @param ObjectiveInterface $child
     */
    public function removeChild(ObjectiveInterface $child)
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            $child->removeParent($this);
        }
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ObjectiveInterface $ancestor
     */
    public function setAncestor(ObjectiveInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }

    /**
     * @return ObjectiveInterface
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * If the objective has no ancestor then we need to objective itself
     *
     * @return ObjectiveInterface
     */
    public function getAncestorOrSelf()
    {
        $ancestor = $this->getAncestor();

        return $ancestor ? $ancestor : $this;
    }

    /**
     * @param Collection $descendants
     */
    public function setDescendants(Collection $descendants)
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    /**
     * @param ObjectiveInterface $descendant
     */
    public function addDescendant(ObjectiveInterface $descendant)
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }

    /**
     * @param ObjectiveInterface $descendant
     */
    public function removeDescendant(ObjectiveInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getDescendants()
    {
        return $this->descendants;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        $sessionCourses = $this->sessionObjectives->map(function (SessionObjectiveInterface $sessionObjective) {
            return $sessionObjective->getSession()->getCourse();
        });

        $courses = $this->courseObjectives->map(function (CourseObjectiveInterface $courseObjective) {
            return $courseObjective->getCourse();
        });

        return array_merge(
            $courses->toArray(),
            $sessionCourses->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function getCourses(): array
    {
        $courseObjectives = $this->getCourseObjectives()->toArray();
        return array_map(function (CourseObjectiveInterface $courseObjective) {
            return $courseObjective->getCourse();
        }, $courseObjectives);
    }

    /**
     * @inheritdoc
     */
    public function getProgramYears(): array
    {
        $programYearObjectives = $this->getProgramYearObjectives()->toArray();
        return array_map(function (ProgramYearObjectiveInterface $programYearObjective) {
            return $programYearObjective->getProgramYear();
        }, $programYearObjectives);
    }

    /**
     * @inheritdoc
     */
    public function getSessions(): array
    {
        $sessionObjectives = $this->getSessionObjectives()->toArray();
        return array_map(function (SessionObjective $sessionObjective) {
            return $sessionObjective->getSession();
        }, $sessionObjectives);
    }
}
