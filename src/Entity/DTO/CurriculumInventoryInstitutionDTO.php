<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CurriculumInventoryInstitutionDTO
 *
 * @IS\DTO("curriculumInventoryInstitutions")
 */
class CurriculumInventoryInstitutionDTO
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
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $aamcCode;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $addressStreet;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $addressCity;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $addressStateOrProvince;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $addressZipCode;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $addressCountryCode;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public $school;

    /**
     * Constructor
     */
    public function __construct(
        $id,
        $name,
        $aamcCode,
        $addressStreet,
        $addressCity,
        $addressStateOrProvince,
        $addressZipCode,
        $addressCountryCode
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->aamcCode = $aamcCode;
        $this->addressStreet = $addressStreet;
        $this->addressCity = $addressCity;
        $this->addressStateOrProvince = $addressStateOrProvince;
        $this->addressZipCode = $addressZipCode;
        $this->addressCountryCode = $addressCountryCode;
    }
}
