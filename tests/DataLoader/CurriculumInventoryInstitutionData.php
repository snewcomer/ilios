<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryInstitutionDTO;

class CurriculumInventoryInstitutionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => $this->faker->text(25),
            'aamcCode' => "{$this->faker->randomDigit}",
            'addressStreet' => '221 West',
            'addressCity' => 'first city',
            'addressStateOrProvince' => $this->faker->stateAbbr,
            'addressZipCode' => $this->faker->postcode,
            'addressCountryCode' => 'UK',
            'school' => '1'
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second institution',
            'aamcCode' => "14",
            'addressStreet' => '221 East',
            'addressCity' => $this->faker->city,
            'addressStateOrProvince' => 'CA',
            'addressZipCode' => '90210',
            'addressCountryCode' => 'BC',
            'school' => '2'
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'name' => $this->faker->text(25),
            'aamcCode' => "{$this->faker->randomDigit}",
            'addressStreet' => '12 Main',
            'addressCity' => $this->faker->city,
            'addressStateOrProvince' => $this->faker->stateAbbr,
            'addressZipCode' => $this->faker->postcode,
            'addressCountryCode' => 'US',
            'school' => '3'
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CurriculumInventoryInstitutionDTO::class;
    }
}
