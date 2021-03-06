<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class PendingUserUpdateDTO
 *
 * @IS\DTO("pendingUserUpdates")
 */
class PendingUserUpdateDTO
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
    public $type;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $property;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $value;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("string")
     */
    public $user;

    /**
     * PendingUserUpdateDTO constructor.
     * @param $id
     * @param $type
     * @param $property
     * @param $value
     */
    public function __construct($id, $type, $property, $value)
    {
        $this->id = $id;
        $this->type = $type;
        $this->property = $property;
        $this->value = $value;
    }
}
