<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\RemoveRootUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Tests the Remove Root User command.
 *
 * Class RemoveRootUserCommandTest
 * @group cli
 */
class RemoveRootUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */
    protected $userManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManager = m::mock('App\Entity\Manager\UserManager');

        $command = new RemoveRootUserCommand($this->userManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(RemoveRootUserCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userManager);
        unset($this->commandTester);
    }

    /**
     * @covers \App\Command\RemoveRootUserCommand::execute
     */
    public function testRemoveRootUser()
    {
        $userId = 1;
        $user = m::mock('App\Entity\User');

        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $this->userManager->shouldReceive('update');
        $user->shouldReceive('setRoot');

        $this->commandTester->execute([
            'command' => RemoveRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);

        $this->userManager->shouldHaveReceived('update', [ $user, true, true ]);
        $user->shouldHaveReceived('setRoot', [ false ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("Root-level privileges have been revoked from user with id #{$userId}.", trim($output));
    }

    /**
     * @covers \App\Command\RemoveRootUserCommand::execute
     */
    public function testMissingInput()
    {
        $this->expectException(\RuntimeException::class, 'Not enough arguments (missing: "userId").');
        $this->commandTester->execute([
            'command' => RemoveRootUserCommand::COMMAND_NAME
        ]);
    }

    /**
     * @covers \App\Command\RemoveRootUserCommand::execute
     */
    public function testUserNotFound()
    {
        $userId = 0;
        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn(null);

        $this->expectException(\Exception::class, "No user with id #{$userId} was found.");
        $this->commandTester->execute([
            'command' => RemoveRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);
    }
}
