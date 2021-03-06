<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\SessionsEntity;
use App\Traits\SchoolEntity;

/**
 * SessionType
 *
 * @ORM\Table(name="session_type",
 *   indexes={
 *     @ORM\Index(name="school_id", columns={"school_id"}),
 *     @ORM\Index(name="assessment_option_fkey", columns={"assessment_option_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Entity\Repository\SessionTypeRepository")
 *
 * @IS\Entity
 */
class SessionType implements SessionTypeInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use SessionsEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use ActivatableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="session_type_id", type="integer")
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
     * @ORM\Column(type="string", length=100)
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="calendar_color", type="string", length=7, nullable=false)
     *
     * @Assert\Type(type="string")
     * Validate that this is a valid hex color #000 or #faFAfa
     * @Assert\Regex(
     *     pattern = "/^#[0-9a-fA-F]{6}$/",
     *     message = "This not a valid HTML hex color code. Eg #aaa of #a1B2C3"
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $calendarColor;

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
     * @var bool
     *
     * @ORM\Column(name="assessment", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $assessment;

    /**
     * @var AssessmentOptionInterface
     *
     * @ORM\ManyToOne(targetEntity="AssessmentOption", inversedBy="sessionTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assessment_option_id", referencedColumnName="assessment_option_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $assessmentOption;

    /**
     * @var SchoolInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="sessionTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
     * @var ArrayCollection|AamcMethodInterface[]
     *
     * @ORM\ManyToMany(targetEntity="AamcMethod", inversedBy="sessionTypes")
     * @ORM\JoinTable(name="session_type_x_aamc_method",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_type_id", referencedColumnName="session_type_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="method_id", referencedColumnName="method_id")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $aamcMethods;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="sessionType")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aamcMethods = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->assessment = false;
        $this->active = true;
    }

    /**
     * @inheritdoc
     */
    public function setCalendarColor($color)
    {
        $this->calendarColor = $color;
    }

    /**
     * @return string
     */
    public function getCalendarColor()
    {
        return $this->calendarColor;
    }

    /**
     * Set assessment
     *
     * @param bool $assessment
     */
    public function setAssessment($assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Get assessment
     *
     * @return bool
     */
    public function isAssessment()
    {
        return $this->assessment;
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     */
    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption = null)
    {
        $this->assessmentOption = $assessmentOption;
    }

    /**
     * @return AssessmentOptionInterface
     */
    public function getAssessmentOption()
    {
        return $this->assessmentOption;
    }

    /**
     * @param Collection $aamcMethods
     */
    public function setAamcMethods(Collection $aamcMethods)
    {
        $this->aamcMethods = new ArrayCollection();

        foreach ($aamcMethods as $aamcMethod) {
            $this->addAamcMethod($aamcMethod);
        }
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function addAamcMethod(AamcMethodInterface $aamcMethod)
    {
        if (!$this->aamcMethods->contains($aamcMethod)) {
            $this->aamcMethods->add($aamcMethod);
        }
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function removeAamcMethod(AamcMethodInterface $aamcMethod)
    {
        $this->aamcMethods->removeElement($aamcMethod);
    }

    /**
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function getAamcMethods()
    {
        return $this->aamcMethods;
    }

    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setSessionType($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        $sessionId = $session->getId();
        throw new \Exception(
            'Sessions can not be removed from sessionTypes.' .
            "You must modify session #{$sessionId} directly."
        );
    }
}
