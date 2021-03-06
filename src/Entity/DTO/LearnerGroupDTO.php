<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class LearnerGroupDTO
 * Data transfer object for a learner group
 *
 * @IS\DTO("learnerGroups")
 */
class LearnerGroupDTO
{
    
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $location;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("cohorts")
     * @IS\Type("string")
     */
    public $cohort;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("array<string>")
     *
     */
    public $descendants;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("array<string>")
     */
    public $children;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $ilmSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $offerings;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $instructorGroups;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $users;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $instructors;

    public function __construct(
        $id,
        $title,
        $location
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->location = $location;


        $this->children = [];
        $this->ilmSessions = [];
        $this->offerings = [];
        $this->instructorGroups = [];
        $this->users = [];
        $this->instructors = [];
        $this->descendants = [];
    }
}
