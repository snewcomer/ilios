<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class OfferingDTO
 * Data transfer object for a offering
 *
 * @IS\DTO("offerings")
 */
class OfferingDTO
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
    public $room;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $site;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $url;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $updatedAt;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("string")
     */
    public $session;

    /**
     * For Voter use, not public
     * @var int
     */
    public $course;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $learnerGroups;

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
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $learners;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $instructors;

    /**
     * OfferingDTO constructor.
     * @param $id
     * @param $room
     * @param $site
     * @param $startDate
     * @param $endDate
     * @param $updatedAt
     */
    public function __construct($id, $room, $site, $url, $startDate, $endDate, $updatedAt)
    {
        $this->id = $id;
        $this->room = $room;
        $this->site = $site;
        $this->url = $url;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->updatedAt = $updatedAt;

        $this->learnerGroups = [];
        $this->instructorGroups = [];
        $this->learners = [];
        $this->instructors = [];
    }
}
