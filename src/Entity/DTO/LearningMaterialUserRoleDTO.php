<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class LearningMaterialUserRoleDTO
 * Data transfer object for a learning material user role
 *
 * @IS\DTO("learningMaterialUserRoles")
 */
class LearningMaterialUserRoleDTO
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
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * Constructor
     */
    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
