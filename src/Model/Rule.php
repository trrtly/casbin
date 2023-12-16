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
namespace Trrtly\Casbin\Model;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Context\ApplicationContext;

/**
 * @property int $id
 * @property string $ptype
 * @property string $v0
 * @property string $v1
 * @property string $v2
 * @property string $v3
 * @property string $v4
 * @property string $v5
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Rule extends Model
{
    /**
     * Fillable.
     *
     * @var array
     */
    protected array $fillable = ['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->config = ApplicationContext::getContainer()->get(ConfigInterface::class);

        $this->setConnection(
            $this->config->get('casbin.database.connection') ?: $this->config->get('database.default')
        );
        $this->setTable($this->config->get('casbin.database.rules_table'));

        parent::__construct($attributes);
    }
}
