<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\ArrayTypeValidator;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedInclude;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Container\Container as Application;

abstract class BaseRepository
{
    use ArrayTypeValidator;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /**
     * The resource object
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    protected ?Model $resource;

    /**
     * Create a new class instance.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $resource
     */
    public function __construct(?Model $resource = null)
    {
        if (!is_null($resource) && false === $resource->exists) {
            throw new \Exception("Resource {$resource} must be existing record in database.");
        }

        $this->resource = $resource;

        $this->app = app();

        $this->makeModel();
    }

    /**
     * Make a new repository
     *
     * @param \Illuminate\Database\Eloquent\Model $resource
     *
     * @return $this
     */
    public static function make(Model $resource): static
    {
        return app()->make(static::class, [
            'resource' => $resource
        ]);
    }

    /**
     * Get searchable fields array
     *
     * @return array
     */
    abstract protected function searchableFields(): array;

    /**
     * Get sortable fields array
     *
     * @return array
     */
    abstract protected function sortableFields(): array;

    /**
     * Get needed fields array
     *
     * @return array
     */
    abstract protected function neededFields(): array;

    /**
     * Get include relations array
     *
     * @return array
     */
    abstract protected function includeRelations(): array;

    /**
     * Configure the Model
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Get all records for the given model
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array $must_relations
     * @param  bool $withTrashed = false,
     * @param  bool $paginate = true
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\Paginator
     */
    abstract public function getAll(Request $request, array $must_relations = [], bool $withTrashed = false, bool $paginate = true): Collection|Paginator;

    /**
     * Make Model instance
     *
     * @return Model
     *
     * @throws \Exception
     */
    private function makeModel()
    {
        $model = $this->app->make($this->model());

        if (! $model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Advannce query builder
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array $additionalFilters
     * @param  bool $withTrashed
     * @param  callback $additionalQuery
     * @param  bool $first = false
     * @param  bool $paginate = true
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Database\Eloquent\Model|null
     *
     * @throws \InvalidArgumentException if the callback does not return a \Illuminate\Database\Eloquent\Builder.
     */
    protected function buildQuery(Request $request, array $additionalFilters = [], bool $withTrashed, ?callable $additionalQuery = null, bool $first = false, bool $paginate = true): Collection|Paginator|Model|null
    {
        $query = $this->model->newQuery();

        if (is_callable($additionalQuery)) {

            $query = $additionalQuery($query);

            if (!$query instanceof Builder) {
                throw new \InvalidArgumentException('The callable must return an instance of \Illuminate\Database\Eloquent\Builder object');
            }
        }

        $this->ensureArrayOfType($additionalFilters, AllowedFilter::class);

        foreach ($this->searchableFields() as $field) {
            $additionalFilters[] = AllowedFilter::partial($field);
        }

        $sorts = array_map(fn ($sort) => AllowedSort::field($sort), $this->sortableFields());

        $relations = array_map(fn ($relation) => AllowedInclude::relationship($relation), $this->includeRelations());

        $builder = QueryBuilder::for($query, $request)->allowedFilters($additionalFilters);

        if (!empty($sorts)) {
            $builder->allowedSorts($sorts);
        }

        if (empty($this->neededFields())) {
            $builder->allowedFields($this->neededFields());
        }

        if (!empty($relations)) {
            $builder->allowedIncludes($relations);
        }

        if ($withTrashed === true) {
            $builder->withTrashed();
        }

        if (true === $first) {
            return $builder->first();
        }

        if (true === $paginate) {
            return $builder->paginate($request->input('perpage', 10))->appends($request->query());
        }

        return $builder->get();
    }

    /**
     * Create model record
     *
     * @param  array  $input
     * @return Model
     */
    public function create(array $input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    /**
     * Find model record for given id
     *
     * @param  string  $id
     * @param  array  $columns
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    /**
     * Update one resource
     *
     * @param  array  $input
     *
     * @return Builder|Builder[]|Collection|Model
     */
    public function updateOne(array $input)
    {
        if (is_null($this->resource)) {
            throw new \Exception('Please pass a resource to update');
        }

        $this->resource->update(Arr::whereNotNull($input));

        return $this->resource;
    }

    /**
     * Update model record for given id
     *
     * @param  array  $input
     * @param  string  $id
     * @return Builder|Builder[]|Collection|Model
     */
    public function update(array $input, string $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        return $model;
    }

    /**
     * @param  string  $id
     * @return bool|mixed|null
     *
     * @throws Exception
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        return $model->delete();
    }

    /**
     * Delete one resource
     *
     * @return bool
     */
    public function deleteOne(): bool
    {
        if (is_null($this->resource)) {
            throw new \Exception('Please pass resource to delete');
        }

        return $this->resource->delete();
    }

    /**
     * @param  string  $id
     * @param  array  $columns
     * @return mixed
     */
    public function findWithoutFail($id, $columns = ['*'])
    {
        try {
            return $this->find($id, $columns);
        } catch (Exception $e) {
            return null;
        }
    }
}
