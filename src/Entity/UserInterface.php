<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\AlertableEntityInterface;
use App\Traits\CohortsEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\ProgramYearsEntityInterface;
use App\Traits\SchoolEntityInterface;

/**
 * Interface UserInterface
 */
interface UserInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
    OfferingsEntityInterface,
    ProgramYearsEntityInterface,
    LoggableEntityInterface,
    SchoolEntityInterface,
    AlertableEntityInterface,
    LearnerGroupsEntityInterface,
    CohortsEntityInterface,
    InstructorGroupsEntityInterface,
    LearningMaterialsEntityInterface
{
    /**
     * @param AuthenticationInterface|null $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication = null);


    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName);

    /**
     * @return string
     */
    public function getMiddleName();

    /**
     * @return string
     */
    public function getFirstAndLastName();

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName);

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setPreferredEmail($email);

    /**
     * @return string
     */
    public function getPreferredEmail();

    /**
     * @param bool $addedViaIlios
     */
    public function setAddedViaIlios($addedViaIlios);

    /**
     * @return bool
     */
    public function isAddedViaIlios();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId);

    /**
     * @return string
     */
    public function getCampusId();

    /**
     * @param string $otherId
     */
    public function setOtherId($otherId);

    /**
     * @return string
     */
    public function getOtherId();

    /**
     * @param bool $examined
     */
    public function setExamined($examined);

    /**
     * @return bool
     */
    public function isExamined();

    /**
     * @param bool $userSyncIgnore
     */
    public function setUserSyncIgnore($userSyncIgnore);

    /**
     * @return bool
     */
    public function isUserSyncIgnore();

    /**
     * Generate a random string to use as the calendar feed url
     */
    public function generateIcsFeedKey();

    /**
     * Sets the ICS Feed Key
     * @param string $icsFeedKey
     */
    public function setIcsFeedKey($icsFeedKey);

    /**
     * @return string
     */
    public function getIcsFeedKey();

    /**
     * @param Collection $courses
     */
    public function setDirectedCourses(Collection $courses);

    /**
     * @param CourseInterface $course
     */
    public function addDirectedCourse(CourseInterface $course);

    /**
     * @param CourseInterface $course
     */
    public function removeDirectedCourse(CourseInterface $course);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDirectedCourses();

    /**
     * @param Collection $administeredCourses
     */
    public function setAdministeredCourses(Collection $administeredCourses);

    /**
     * @param CourseInterface $administeredCourse
     */
    public function addAdministeredCourse(CourseInterface $administeredCourse);

    /**
     * @param CourseInterface $administeredCourse
     */
    public function removeAdministeredCourse(CourseInterface $administeredCourse);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getAdministeredCourses();

    public function setStudentAdvisedCourses(Collection $studentAdvisedCourses);

    public function addStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);

    public function removeStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getStudentAdvisedCourses();

    public function setStudentAdvisedSessions(Collection $studentAdvisedSessions);

    public function addStudentAdvisedSession(SessionInterface $studentAdvisedSession);

    public function removeStudentAdvisedSession(SessionInterface $studentAdvisedSession);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getStudentAdvisedSessions();

    /**
     * @param int $courseId
     * @return bool
     */
    public function isDirectingCourse($courseId);

    /**
     * @param Collection $instructedLearnerGroups
     */
    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups);

    /**
     * @param LearnerGroupInterface $instructedLearnerGroup
     */
    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    /**
     * @param LearnerGroupInterface $instructedLearnerGroup
     */
    public function removeInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getInstructedLearnerGroups();

    /**
     * @param Collection $roles
     */
    public function setRoles(Collection $roles);

    /**
     * @param UserRoleInterface $role
     */
    public function addRole(UserRoleInterface $role);

    /**
     * @param UserRoleInterface $role
     */
    public function removeRole(UserRoleInterface $role);

    /**
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function getRoles();

    /**
     * @param Collection $reports
     */
    public function setReports(Collection $reports);

    /**
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report);

    /**
     * @param ReportInterface $report
     */
    public function removeReport(ReportInterface $report);

    /**
     * @return ArrayCollection|ReportInterface[]
     */
    public function getReports();

    /**
     * @param Collection $pendingUserUpdates
     */
    public function setPendingUserUpdates(Collection $pendingUserUpdates);

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     */
    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     */
    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    /**
     * @return ArrayCollection|PendingUserUpdateInterface[]
     */
    public function getPendingUserUpdates();

    /**
     * @return ArrayCollection[School]
     */
    public function getAllSchools();

    /**
     * @param Collection $auditLogs
     */
    public function setAuditLogs(Collection $auditLogs);

    /**
     * @param AuditLogInterface $auditLog
     */
    public function addAuditLog(AuditLogInterface $auditLog);

    /**
     * @param AuditLogInterface $auditLog
     */
    public function removeAuditLog(AuditLogInterface $auditLog);

    /**
     * @return ArrayCollection|AuditLogInterface[]
     */
    public function getAuditLogs();

    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts);

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert);

    /**
     * @param AlertInterface $alert
     */
    public function removeAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();

    /**
     * @param Collection $administeredSessions
     */
    public function setAdministeredSessions(Collection $administeredSessions);

    /**
     * @param SessionInterface $administeredSession
     */
    public function addAdministeredSession(SessionInterface $administeredSession);

    /**
     * @param SessionInterface $administeredSession
     */
    public function removeAdministeredSession(SessionInterface $administeredSession);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getAdministeredSessions();

    /**
     * @param Collection $learnerIlmSessions
     */
    public function setLearnerIlmSessions(Collection $learnerIlmSessions);

    /**
     * @param IlmSessionInterface $learnerIlmSession
     */
    public function addLearnerIlmSession(IlmSessionInterface $learnerIlmSession);

    /**
     * @param IlmSessionInterface $learnerIlmSession
     */
    public function removeLearnerIlmSession(IlmSessionInterface $learnerIlmSession);

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getLearnerIlmSessions();

    /**
     * @param Collection $schools
     */
    public function setDirectedSchools(Collection $schools);

    /**
     * @param SchoolInterface $school
     */
    public function addDirectedSchool(SchoolInterface $school);

    /**
     * @param SchoolInterface $school
     */
    public function removeDirectedSchool(SchoolInterface $school);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getDirectedSchools();

    /**
     * @param Collection $administeredSchools
     */
    public function setAdministeredSchools(Collection $administeredSchools);

    /**
     * @param SchoolInterface $administeredSchool
     */
    public function addAdministeredSchool(SchoolInterface $administeredSchool);

    /**
     * @param SchoolInterface $administeredSchool
     */
    public function removeAdministeredSchool(SchoolInterface $administeredSchool);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getAdministeredSchools();

    /**
     * @param Collection $programs
     */
    public function setDirectedPrograms(Collection $programs);

    /**
     * @param ProgramInterface $program
     */
    public function addDirectedProgram(ProgramInterface $program);

    /**
     * @param ProgramInterface $program
     */
    public function removeDirectedProgram(ProgramInterface $program);

    /**
     * @return ArrayCollection|ProgramInterface[]
     */
    public function getDirectedPrograms();

    /**
     * @param bool $root
     */
    public function setRoot($root);

    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return ArrayCollection|CurriculumInventoryReportInterface[]
     */
    public function getAdministeredCurriculumInventoryReports();

    /**
     * @param Collection $reports
     */
    public function setAdministeredCurriculumInventoryReports(Collection $reports);

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);
}
