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
namespace Trrtly\Casbin\Repository;

use Hyperf\Contract\ConfigInterface;
use Psr\SimpleCache\CacheInterface;
use Trrtly\Casbin\Contract\RuleInterface;
use Trrtly\Casbin\Model\Rule;

class RuleRepository implements RuleInterface
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(CacheInterface $cache, ConfigInterface $config)
    {
        $this->cache = $cache;
        $this->config = $config;
    }

    public function getAll(): array
    {
        return Rule::query()->select(['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'])->get()->toArray();
    }

    /**
     * Gets rules from caches.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @return mixed
     */
    public function getAllFromCache()
    {
        if (! $this->config->get('casbin.cache.enabled', false)) {
            return $this->getAll();
        }

        $value = $this->cache->get($this->config->get('casbin.cache.key'));
        if (! is_null($value)) {
            return $value;
        }

        $this->cache->set(
            $this->config->get('casbin.cache.key'),
            $value = $this->getAll(),
            $this->config->get('casbin.cache.ttl')
        );

        return $value;
    }

    /**
     * Refresh Cache.
     */
    public function refreshCache(): bool
    {
        if (! $this->config->get('casbin.cache.enabled', false)) {
            return true;
        }

        if ($this->forgetCache()) {
            $this->getAllFromCache();
            return true;
        }

        return false;
    }

    /**
     * Forget Cache.
     */
    public function forgetCache(): bool
    {
        return $this->cache->delete($this->config->get('casbin.cache.key'));
    }

    public function addPolicy(Rule $rule): bool
    {
        return Rule::query()->insert($rule->getAttributes());
    }

    /**
     * @param Rule[] $rules
     * @return bool
     */
    public function addPolicys(array $rules): bool
    {
        $data = [];
        foreach ($rules as $rule) {
            $data[] = $rule->getAttributes();
        }

        return Rule::query()->insert($data);
    }

    public function removePolicy(string $ptype, array $fieldConditions): bool
    {
        $query = Rule::query()->where('ptype', $ptype);

        foreach ($fieldConditions as $fieldColumn => $fieldValue) {
            $query->where($fieldColumn, $fieldValue);
        }

        return $query->delete() ? true : false;
    }
}
