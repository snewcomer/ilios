<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AamcPcrsDTO
 * Data transfer object for a aamcPcrs
 *
 * @IS\DTO("aamcPcrses")
 */
class AamcPcrsDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $description;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $competencies;

    public function __construct(
        $id,
        $description
    ) {
        $this->id = $id;
        $this->description = $description;

        $this->competencies = [];
    }
}
