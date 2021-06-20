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
namespace Trrtly\Casbin\Adapter;

use Casbin\Model\Model;
use Casbin\Persist\Adapter;
use Casbin\Persist\AdapterHelper;
use Casbin\Persist\BatchAdapter;
use Hyperf\DbConnection\Db;
use Trrtly\Casbin\Contract\RuleInterface;
use Trrtly\Casbin\Model\Rule;

/**
 * DatabaseAdapter.
 */
class DatabaseAdapter implements Adapter, BatchAdapter
{
    use AdapterHelper;

    /**
     * Db.
     *
     * @var Db
     */
    private $db;

    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * the DatabaseAdapter constructor.
     *
     * @param RuleInterface $rule
     * @param Db $db
     */
    public function __construct(RuleInterface $rule, Db $db)
    {
        $this->db = $db;
        $this->rule = $rule;
    }

    /**
     * savePolicyLine function.
     *
     * @param string $ptype
     * @param array $rule
     * @return bool
     */
    public function savePolicyLine(string $ptype, array $rule): bool
    {
        return $this->rule->addPolicy(
            $this->parsePolicyLine($ptype, $rule)
        );
    }

    /**
     * loads all policy rules from the storage.
     *
     * @param Model $model
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function loadPolicy(Model $model): void
    {
        $rows = $this->rule->getAllFromCache();

        foreach ($rows as $row) {
            $line = implode(', ', array_filter($row, function ($val) {
                return $val != '' && ! is_null($val);
            }));
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * saves all policy rules to the storage.
     *
     * @param Model $model
     */
    public function savePolicy(Model $model): void
    {
        foreach ($model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        foreach ($model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
    }

    /**
     * adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
        $this->savePolicyLine($ptype, $rule);
    }

    /**
     * Adds a policy rules to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param string[][] $rules
     */
    public function addPolicies(string $sec, string $ptype, array $rules): void
    {
        $rows = [];
        foreach ($rules as $rule) {
            $rows[] = $this->parsePolicyLine($ptype, $rule);
        }
        $this->rule->addPolicys($rows);
        $this->rule->refreshCache();
    }

    /**
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
        $condition = [];
        foreach ($rule as $key => $value) {
            $condition['v' . strval($key)] = $value;
        }

        $this->rule->removePolicy($ptype, $condition);
    }

    /**
     * Removes policy rules from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param string[][] $rules
     * @throws \Throwable
     */
    public function removePolicies(string $sec, string $ptype, array $rules): void
    {
        $this->db->transaction(function () use ($sec, $ptype, $rules) {
            foreach ($rules as $rule) {
                $this->removePolicy($sec, $ptype, $rule);
            }
        });
    }

    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param int $fieldIndex
     * @param string ...$fieldValues
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void
    {
        $conditions = [];
        foreach (range(0, 5) as $value) {
            if ($fieldIndex <= $value && $value < $fieldIndex + count($fieldValues)) {
                if ($fieldValues[$value - $fieldIndex] != '') {
                    $conditions['v' . strval($value)] = $fieldValues[$value - $fieldIndex];
                }
            }
        }

        $this->rule->removePolicy($ptype, $conditions);
    }

    protected function parsePolicyLine(string $ptype, array $rule)
    {
        $policy = new Rule();
        $policy->ptype = $ptype;
        foreach ($rule as $key => $value) {
            $policy->{'v' . strval($key)} = $value;
        }

        return $policy;
    }
}
