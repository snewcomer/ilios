<?php

namespace Tests\AppBundle\Fixture;

use AppBundle\Entity\CurriculumInventorySequence;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventorySequenceData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\AppBundle\DataLoader\CurriculumInventorySequenceData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventorySequence();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $manager->persist($entity);
            $this->addReference('curriculumInventorySequences' . $arr['report'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryReportData',
        );
    }
}