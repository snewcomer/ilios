<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\MeshDescriptorsEntityInterface;
use AppBundle\Traits\SortableEntityInterface;

/**
 * Interface LearningMaterialRelationshipInterface
 */
interface LearningMaterialRelationshipInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    MeshDescriptorsEntityInterface,
    SortableEntityInterface
{
    /**
     * @param string $notes
     */
    public function setNotes($notes);

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @param boolean $required
     */
    public function setRequired($required);

    /**
     * @return boolean
     */
    public function isRequired();

    /**
     * @param boolean $publicNotes
     */
    public function setPublicNotes($publicNotes);

    /**
     * @return boolean
     */
    public function hasPublicNotes();

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function setLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial();

    /**
     * @return \DateTime|null
     */
    public function getStartDate();

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(\DateTime $startDate = null);

    /**
     * @return \DateTime|null
     */
    public function getEndDate();

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate(\DateTime $endDate = null);
}