<?php

declare(strict_types=1);

namespace Trrtly\Casbin\Command;

use Hyperf\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Trrtly\Casbin\Facade\Enforcer;

/**
 * PolicyAdd class.
 */
class GroupAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected ?string $name = 'group:add';

    public function configure()
    {
        parent::configure();
        $this->setDescription('Adds a role inheritance rule to the current policy.');
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
        $ret = Enforcer::addGroupingPolicy(...$params);
        if ($ret) {
            $this->info('Grouping `' . implode(', ', $params) . '` created');
        } else {
            $this->error('Grouping `' . implode(', ', $params) . '` creation failed');
        }
    }
}
