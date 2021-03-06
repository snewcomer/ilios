<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\AddNewStudentsToSchoolCommand;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\SchoolManager;
use App\Entity\Manager\UserManager;
use App\Entity\Manager\UserRoleManager;
use App\Service\Directory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class AddNewStudentsToSchoolCommandTest
 * @group cli
 */
class AddNewStudentsToSchoolCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:add-students';

    protected $userManager;
    protected $userRoleManager;
    protected $schoolManager;
    protected $authenticationManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->userManager = m::mock(UserManager::class);
        $this->userRoleManager = m::mock(UserRoleManager::class);
        $this->schoolManager = m::mock(SchoolManager::class);
        $this->authenticationManager = m::mock(AuthenticationManager::class);
        $this->directory = m::mock(Directory::class);

        $command = new AddNewStudentsToSchoolCommand(
            $this->userManager,
            $this->schoolManager,
            $this->authenticationManager,
            $this->userRoleManager,
            $this->directory
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userManager);
        unset($this->userRoleManager);
        unset($this->schoolManager);
        unset($this->authenticationManager);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute()
    {
        $fakeDirectoryUser1 = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $fakeDirectoryUser2 = [
            'firstName' => 'first2',
            'lastName' => 'last2',
            'email' => 'email2',
            'telephoneNumber' => 'phone2',
            'campusId' => 'abc2',
            'username' => 'username2',
        ];
        $school = m::mock('App\Entity\SchoolInterface')
            ->shouldReceive('getTitle')->andReturn('school 1')
            ->mock();
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email')
            ->shouldReceive('setPhone')->with('phone')
            ->shouldReceive('setCampusId')->with('abc')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('addRole')->with($school)
            ->mock();
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')->with($user)
            ->shouldReceive('setUsername')->with('username')
            ->mock();
        $this->directory->shouldReceive('findByLdapFilter')
            ->with('FILTER')
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);
        $this->userManager->shouldReceive('getAllCampusIds')
            ->andReturn(['abc2']);

        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager
            ->shouldReceive('update')
            ->with($user)->once();
        $this->schoolManager->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $role = m::mock('App\Entity\UserRoleInterface')
            ->shouldReceive('addUser')->with($user)
            ->mock();
        $user->shouldReceive('addRole')->with($role);
        $this->userRoleManager
            ->shouldReceive('findOneBy')
            ->with(['title' => 'Student'])
            ->andReturn($role);
        $this->userRoleManager
            ->shouldReceive('update')
            ->with($role);

        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication, false);

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command'   => self::COMMAND_NAME,
            'schoolId'    => 1,
            'filter'    => 'FILTER',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Found 2 students in the directory./',
            $output
        );

        $this->assertRegExp(
            '/There are 1 new students to be added to school 1./',
            $output
        );
        $this->assertRegExp(
            '/Do you wish to add these students to school 1?/',
            $output
        );
        $this->assertRegExp(
            '/abc\s+\| first\s+\| last\s+\| email /',
            $output
        );
        $this->assertRegExp(
            '/Success! New student #42 first last created./',
            $output
        );
    }

    public function testBadSchoolId()
    {
        $this->schoolManager->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(\Exception::class, 'School with id 1 could not be found.');
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'filter'         => 'FILTER',
            'schoolId'         => '1'
        ]);
    }

    public function testFilterRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'schoolId'         => '1'
        ]);
    }

    public function testSchoolRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'filter'         => '1',
        ]);
    }
}
