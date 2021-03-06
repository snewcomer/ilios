<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SchoolConfigDTO
 *
 * @IS\DTO("schoolConfigs")
 */
class SchoolConfigDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $value;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public $school;

    public function __construct($id, $name, $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }
}
