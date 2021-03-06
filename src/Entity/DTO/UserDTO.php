<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\UserInterface;

/**
 * Class UserDTO
 * Data transfer object for a user
 * @IS\DTO("users")
 */
class UserDTO
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
    public $lastName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $firstName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $middleName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $displayName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $phone;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $email;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $preferredEmail;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $addedViaIlios;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $enabled;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $campusId;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $otherId;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $examined;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $userSyncIgnore;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $icsFeedKey;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $reports;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("authentications")
     * @IS\Type("string")
     */
    public $authentication;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
    public $directedCourses;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
    public $administeredCourses;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
    public $studentAdvisedCourses;

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
     * @IS\Related("learnerGroups")
     * @IS\Type("array<string>")
     */
    public $instructedLearnerGroups;

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
     * @IS\Related("ilmSessions")
     * @IS\Type("array<string>")
     */
    public $instructorIlmSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("ilmSession")
     * @IS\Type("array<string>")
     */
    public $learnerIlmSessions;

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
     * @IS\Related("offerings")
     * @IS\Type("array<string>")
     */
    public $instructedOfferings;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $programYears;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("userRoles")
     * @IS\Type("array<string>")
     */
    public $roles;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $cohorts;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("cohorts")
     * @IS\Type("string")
     */
    public $primaryCohort;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $pendingUserUpdates;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")

     */
    public $root;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("array<string>")
     */
    public $directedSchools;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("array<string>")
     */
    public $administeredSchools;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public $administeredSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public $studentAdvisedSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("programs")
     * @IS\Type("array<string>")
     */
    public $directedPrograms;

    /**
     * @var array
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("array<string>")
     */
    public $administeredCurriculumInventoryReports;

    /**
     * @var array
     */
    public $auditLogs;

    /**
     * For index use, not public
     * @var string
     */
    public $username;

    public function __construct(
        $id,
        $firstName,
        $lastName,
        $middleName,
        $displayName,
        $phone,
        $email,
        $preferredEmail,
        $addedViaIlios,
        $enabled,
        $campusId,
        $otherId,
        $examined,
        $userSyncIgnore,
        $icsFeedKey,
        $root
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->displayName = $displayName;
        $this->phone = $phone;
        $this->email = $email;
        $this->preferredEmail = $preferredEmail;
        $this->addedViaIlios = $addedViaIlios;
        $this->enabled = $enabled;
        $this->campusId = $campusId;
        $this->otherId = $otherId;
        $this->examined = $examined;
        $this->userSyncIgnore = $userSyncIgnore;
        $this->icsFeedKey = $icsFeedKey;
        $this->root = $root;

        $this->directedCourses = [];
        $this->administeredCourses = [];
        $this->studentAdvisedCourses = [];
        $this->studentAdvisedSessions = [];
        $this->learnerGroups = [];
        $this->instructedLearnerGroups = [];
        $this->instructorGroups = [];
        $this->offerings = [];
        $this->instructedOfferings = [];
        $this->instructorIlmSessions = [];
        $this->programYears = [];
        $this->roles = [];
        $this->reports = [];
        $this->cohorts = [];
        $this->pendingUserUpdates = [];
        $this->auditLogs = [];
        $this->learnerIlmSessions = [];
        $this->directedSchools = [];
        $this->administeredSchools = [];
        $this->administeredSessions = [];
        $this->directedPrograms = [];
        $this->administeredCurriculumInventoryReports = [];
    }
}
