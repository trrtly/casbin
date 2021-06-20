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
namespace Trrtly\Casbin\Contract;

use Trrtly\Casbin\Model\Rule;

interface RuleInterface
{
    /**
     * Gets rules from caches.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Gets rules from caches.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @return mixed
     */
    public function getAllFromCache();

    /**
     * Refresh Cache.
     *
     * @return bool
     */
    public function refreshCache(): bool;

    /**
     * Forget Cache.
     *
     * @return bool
     */
    public function forgetCache(): bool;

    /**
     * insert policy to db.
     *
     * @param Rule $rule
     * @return bool
     */
    public function addPolicy(Rule $rule): bool;

    /**
     * remove policy from db.
     *
     * @param string $ptype
     * @param array $fieldConditions
     * @return bool
     */
    public function removePolicy(string $ptype, array $fieldConditions): bool;
}
