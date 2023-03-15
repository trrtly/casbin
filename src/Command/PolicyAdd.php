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
 * PolicyAdd class.
 */
class PolicyAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected ?string $name = 'policy:add';

    public function configure()
    {
        parent::configure();
        $this->setDescription('Adds an authorization rule to the current policy.');
        $this->addArgument('policy', InputArgument::OPTIONAL, 'the rule separated by commas');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $params = explode(',', $this->input->getArgument('policy'));
        array_walk($params, function (&$value) {
            $value = trim($value);
        });
        $ret = Enforcer::addPolicy(...$params);
        if ($ret) {
            $this->info('Policy `' . implode(', ', $params) . '` created');
        } else {
            $this->error('Policy `' . implode(', ', $params) . '` creation failed');
        }
    }
}
