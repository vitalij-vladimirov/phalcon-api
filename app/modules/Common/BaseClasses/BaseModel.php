<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Support\Collection;
use Closure;
use Common\Regex;
use Common\Text;

/**
 * phpcs:disable
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static EloquentBuilder|BaseModel whereId($value)
 * @method static EloquentBuilder|BaseModel whereIdNot($value)
 * @method static EloquentBuilder|BaseModel whereCreatedAt($value)
 * @method static EloquentBuilder|BaseModel whereUpdatedAt($value)
 *
 * @method static Collection|BaseModel all()
 * @method static Collection|BaseModel median($key = null)
 * @method static Collection|BaseModel diff($items)
 * @method static Collection|BaseModel diffUsing($items, callable $callback)
 * @method static Collection|BaseModel diffKeys($items)
 * @method static Collection|BaseModel diffKeysUsing($items, callable $callback)
 * @method static Collection|BaseModel duplicates($callback = null, $strict = false)
 * @method static Collection|BaseModel duplicatesStrict($callback = null)
 * @method static Collection|BaseModel duplicateComparator($strict)
 * @method static Collection|BaseModel except($keys)
 * @method static Collection|BaseModel filter(callable $callback = null)
 * @method static Collection|BaseModel keyBy($keyBy)
 * @method static Collection|BaseModel last(callable $callback = null, $default = null)
 * @method static Collection|BaseModel merge($items)
 * @method static Collection|BaseModel random($number = null)
 * @method static Collection|BaseModel sort($callback = null)
 * @method static Collection|BaseModel sortDesc($options = SORT_REGULAR)
 * @method static Collection|BaseModel sortBy($callback, $options = SORT_REGULAR, $descending = false)
 * @method static Collection|BaseModel sortByDesc($callback, $options = SORT_REGULAR)
 * @method static Collection|BaseModel sortKeys($options = SORT_REGULAR, $descending = false)
 * @method static Collection|BaseModel sortKeysDesc($options = SORT_REGULAR)
 * @method static EloquentBuilder|BaseModel query()
 * @method static EloquentBuilder|BaseModel fromQuery($query, array $bindings = [])
 * @method static EloquentBuilder|BaseModel where(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method static EloquentBuilder|BaseModel firstWhere(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method static EloquentBuilder|BaseModel find(int $id, array $columns = ['*'])
 * @method static EloquentBuilder|BaseModel findMany(array $ids, array $columns = ['*'])
 * @method static EloquentBuilder|BaseModel findOrFail(int $id, array $columns = ['*'])
 * @method static EloquentBuilder|BaseModel findOrNew(int $id, array $columns = ['*'])
 * @method static BuildsQueries|BaseModel first(array $columns = ['*'])
 * @method static EloquentBuilder|BaseModel firstOrNew(array $attributes = [], array $values = [])
 * @method static EloquentBuilder|BaseModel firstOrCreate(array $attributes, array $values = [])
 * @method static EloquentBuilder|BaseModel firstOrFail(array $columns = ['*'])
 * @method static EloquentBuilder|BaseModel get(array $columns = ['*'])
 * @method static EloquentBuilder|BaseModel pluck(string $column, $key = null)
 * @method static EloquentBuilder|BaseModel paginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int $page = null)
 * @method static EloquentBuilder|BaseModel simplePaginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int $page = null)
 * @method static EloquentBuilder|BaseModel create(array $attributes = [])
 * @method static EloquentBuilder|BaseModel forceCreate(array $attributes)
 * @method static EloquentBuilder|BaseModel update(array $values)
 * @method static EloquentBuilder|BaseModel increment(string $column, int $amount = 1, array $extra = [])
 * @method static EloquentBuilder|BaseModel decrement(string $column, int $amount = 1, array $extra = [])
 * @method static EloquentBuilder|BaseModel with($relations)
 * @method static EloquentBuilder|BaseModel without($relations)
 * @method static EloquentBuilder|BaseModel getModel()
 * @method static QueryBuilder|BaseModel select(array $columns = ['*'])
 * @method static QueryBuilder|BaseModel selectSub($query, $as)
 * @method static QueryBuilder|BaseModel selectRaw($expression, array $bindings = [])
 * @method static QueryBuilder|BaseModel fromSub($query, $as)
 * @method static QueryBuilder|BaseModel fromRaw($expression, $bindings = [])
 * @method static QueryBuilder|BaseModel distinct()
 * @method static QueryBuilder|BaseModel join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static QueryBuilder|BaseModel joinWhere($table, $first, $operator, $second, $type = 'inner')
 * @method static QueryBuilder|BaseModel joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static QueryBuilder|BaseModel leftJoin($table, $first, $operator = null, $second = null)
 * @method static QueryBuilder|BaseModel leftJoinWhere($table, $first, $operator, $second)
 * @method static QueryBuilder|BaseModel leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method static QueryBuilder|BaseModel rightJoin($table, $first, $operator = null, $second = null)
 * @method static QueryBuilder|BaseModel rightJoinWhere($table, $first, $operator, $second)
 * @method static QueryBuilder|BaseModel rightJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method static QueryBuilder|BaseModel crossJoin($table, $first = null, $operator = null, $second = null)
 * @method static QueryBuilder|BaseModel newJoinClause(self $parentQuery, $type, $table)
 * @method static QueryBuilder|BaseModel addArrayOfWheres(string $column, string $boolean, $method = 'where')
 * @method static QueryBuilder|BaseModel whereColumn($first, $operator = null, $second = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereRaw(string $sql, $bindings = [], string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereIn(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereNotIn(string $column, array $values, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereIntegerInRaw(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereIntegerNotInRaw(string $column, $values, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereNull($columns, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereNotNull($columns, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereBetween(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereNotBetween(string $column, array $values, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereDate(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereTime(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereDay(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereMonth(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereYear(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereSub(string $column, $operator, Closure $callback, string $boolean)
 * @method static QueryBuilder|BaseModel whereExists(Closure $callback, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereNotExists(Closure $callback, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel addWhereExistsQuery(self $query, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereRowValues($columns, $operator, $values, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereJsonContains(string $column, $value, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel whereJsonDoesntContain(string $column, $value, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel whereJsonLength(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel groupBy(...$groups)
 * @method static QueryBuilder|BaseModel groupByRaw($sql, array $bindings = [])
 * @method static QueryBuilder|BaseModel having(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method static QueryBuilder|BaseModel havingBetween(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder|BaseModel havingRaw($sql, array $bindings = [], string $boolean = 'and')
 * @method static QueryBuilder|BaseModel latest($column = 'created_at')
 * @method static QueryBuilder|BaseModel oldest($column = 'created_at')
 * @method static QueryBuilder|BaseModel inRandomOrder($seed = '')
 * @method static QueryBuilder|BaseModel skip($value)
 * @method static QueryBuilder|BaseModel limit($value)
 * @method static QueryBuilder|BaseModel forPage($page, $perPage = 15)
 * @method static QueryBuilder|BaseModel forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static QueryBuilder|BaseModel forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static QueryBuilder|BaseModel removeExistingOrdersFor($column)
 * @method static QueryBuilder|BaseModel toSql()
 * @method static bool exists()
 * @method static bool doesntExist()
 * @method static int count($columns = '*')
 * @method static int min($column)
 * @method static int max($column)
 * @method static int|float sum($column)
 * @method static int|float avg($column)
 * @method static int|float average($column)
 * @method static BaseModel insert(array $values)
 * @method static BaseModel insertOrIgnore(array $values)
 * @method static int insertGetId(array $values, $sequence = null)
 * @method static BaseModel updateOrInsert($attributes, array $values = [])
 * @method static dd()
 *
 * @mixin Model
 *
 * phpcs:enable
 */
abstract class BaseModel extends Model
{
    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->table === null) {
            $this->setTableName();
        }
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at ?? null;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at ?? null;
    }

    private function setTableName(): void
    {
        $modelClass = last(explode('\\', get_class($this)));
        if (Regex::isValidPattern($modelClass, '/(Model)$/')) {
            $modelClass = substr($modelClass, 0, -5);
        }

        $this->table = Text::toSnakeCase($modelClass);
    }
}
