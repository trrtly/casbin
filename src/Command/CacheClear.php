<?php

declare(strict_types=1);

namespace Trrtly\Casbin\Command;

use Hyperf\Command\Command;
use Hyperf\Context\ApplicationContext;
use Trrtly\Casbin\Model\Rule;

/**
 * Class CacheClear.
 */
class CacheClear extends Command
{
    protected ?string $name = 'casbin:cache-clear';

    public function __construct()
    {
        parent::__construct('casbin:cache-clear');
        $this->setDescription('Clear the casbin cache');
    }

    public function handle()
    {
        ApplicationContext::getContainer()->get(Rule::class)->forgetCache();
        $this->line('casbin cache flushed.');
    }
}
