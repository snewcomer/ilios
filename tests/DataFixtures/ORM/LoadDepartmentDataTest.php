<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\DepartmentInterface;

/**
 * Class LoadDepartmentDataTest
 */
class LoadDepartmentDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\DepartmentManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadDepartmentData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadDepartmentData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('department.csv');
    }

    /**
     * @param array $data
     * @param DepartmentInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `department_id`,`title`,`school_id`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
    }
}
