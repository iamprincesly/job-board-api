<?php

namespace App\Repositories;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class CandidateRepository extends BaseRepository
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
            'name',
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
            //
        ];
    }

    /**
     * Configure the Model
     *
     * @return string
     */
    protected function model(): string
    {
        return Candidate::class;
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
        return $this->buildQuery($request, [], $withTrashed, null, false, $paginate);
    }
}
