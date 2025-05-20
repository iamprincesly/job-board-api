<?php

namespace App\Repositories;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class JobRepository extends BaseRepository
{
    /**
     * Create a new instance of JonRepository
     *
     * @param \Illuminate\Database\Eloquent\Model|null $resource
     */
    public function __construct(?Model $resource = null)
    {
        parent::__construct($resource);
    }

    /**
     * Get searchable fields array
     *
     * @return array
     */
    protected function searchableFields(): array
    {
        return [
            'title',
            'description'
        ];
    }

    /**
     * Get sortable fields array
     *
     * @return array
     */
    protected function sortableFields(): array
    {
        return [
            'created_at'
        ];
    }

    /**
     * Get needed fields array
     *
     * @return array
     */
    protected function neededFields(): array
    {
        return [];
    }

    /**
     * Get include relations array
     *
     * @return array
     */
    protected function includeRelations(): array
    {
        return [
            'company',
            'applications'
        ];
    }

    /**
     * Configure the Model
     *
     * @return string
     */
    protected function model(): string
    {
        return Job::class;
    }

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
    public function getAll(Request $request, array $must_relations = [], bool $withTrashed = false, bool $paginate = true): Collection|Paginator
    {
        $filters = [
            AllowedFilter::callback('keyword', fn (Builder $query, $value) => $query->where(fn($q) => $q->where('title', 'like', '%'.$value.'%')->orWhere('description', 'like', '%'.$value.'%'))),
            AllowedFilter::exact('is_remote'),
            AllowedFilter::exact('location'),
        ];

        return $this->buildQuery($request, $filters, $withTrashed, function (Builder $query) use ($must_relations) {

            if (!empty($must_relations)) {
                $query->with($must_relations);
            }

            return $query;
        }, false, $paginate);
    }

    /**
     * Get all company jobs
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Company $company
     * @param  array $must_relations
     * @param  bool $withTrashed = false,
     * @param  bool $paginate = true
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\Paginator
     */
    public function companyJobs(Request $request, Company $company, array $must_relations = [], bool $withTrashed = false, bool $paginate = true): Collection|Paginator
    {
        $filters = [
            AllowedFilter::callback('keyword', fn (Builder $query, $value) => $query->where(fn($q) => $q->where('title', 'like', '%'.$value.'%')->orWhere('description', 'like', '%'.$value.'%'))),
            AllowedFilter::exact('is_remote'),
            AllowedFilter::exact('location'),
        ];

        return $this->buildQuery($request, $filters, $withTrashed, function (Builder $query) use ($must_relations, $company) {

            $query->where('company_id', $company->getKey());

            if (!empty($must_relations)) {
                $query->with($must_relations);
            }

            return $query;
        }, false, $paginate);
    }
}
