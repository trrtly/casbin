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
namespace Trrtly\Casbin;

use Casbin\Bridge\Logger\LoggerBridge;
use Casbin\Enforcer;
use Casbin\Log\Log;
use Casbin\Model\Model;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

/**
 * @mixin Enforcer
 */
class EnforcerManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ConfigInterface
     */
    private $config;

    private $instance;

    /**
     * EnforcerManager constructor.
     *
     * @param ContainerInterface $container
     * @param ConfigInterface $config
     */
    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function __call($name, $arguments)
    {
        $enforcer = $this->resolveEnforcer();
        // reload policy.
        $enforcer->loadPolicy();

        return $enforcer->{$name}(...$arguments);
    }

    private function resolveEnforcer()
    {
        if (isset($this->instance)) {
            return $this->instance;
        }

        $this->instance = new Enforcer($this->resolveModel(), $this->resolveAdapter(), $this->enableLog());

        return $this->instance;
    }

    private function resolveModel(): Model
    {
        $model = new Model();

        $configType = $this->config->get('casbin.model.config_type');
        if ($configType == 'file') {
            $model->loadModel($this->config->get('casbin.model.config_file_path', ''));
        } elseif ($configType == 'text') {
            $model->loadModelFromText($this->config->get('casbin.model.config_text', ''));
        } else {
            throw new InvalidArgumentException('unknown casbin config type : ' . $configType);
        }

        return $model;
    }

    private function resolveAdapter()
    {
        $adapter = $this->config->get('casbin.adapter');
        if (! is_null($adapter)) {
            $adapter = $this->container->get($adapter);
        }

        return $adapter;
    }

    private function enableLog()
    {
        $enableLog = $this->config->get('casbin.log.enabled', false);

        if ($enableLog && $this->container->has(StdoutLoggerInterface::class)) {
            Log::setLogger(new LoggerBridge(
                $this->container->get(StdoutLoggerInterface::class)
            ));
        }

        return $enableLog;
    }
}
