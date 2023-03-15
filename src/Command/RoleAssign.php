<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Trrtly\Casbin\Command;

use Hyperf\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Trrtly\Casbin\Facade\Enforcer;

/**
 * RoleAssign class.
 */
class RoleAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected ?string $name = 'role:assign';

    public function configure()
    {
        parent::configure();
        $this->setDescription('Adds a role for a user.');
        $this->addArgument('user', InputArgument::OPTIONAL, 'the identifier of user');
        $this->addArgument('role', InputArgument::OPTIONAL, 'the name of role');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = $this->input->getArgument('user');
        $role = $this->input->getArgument('role');

        $ret = Enforcer::addRoleForUser($user, $role);
        if ($ret) {
            $this->info('Added `' . $role . '` role to `' . $user . '` successfully');
        } else {
            $this->error('Added `' . $role . '` role to `' . $user . '` failed');
        }
    }
}
