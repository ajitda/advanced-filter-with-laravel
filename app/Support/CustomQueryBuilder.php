<?php
namespace App\Support;

class CustomQueryBuilder
{
    public function apply($query, $data)
    {
        if(isset($data['f'])) {
            foreach ($data['f'] as $filter) {
                $filter['match'] = isset($data['filter_match']) ? $ $data['filter_match'] : "and";
                $this->makeFilter($query, $filter);
            }
        }
        return $query;
    }

    protected function makeFilter($query, $filter)
    {
        $this->{camel_case($filter['operator'])}($filter, $query);
    }

    public function equalTo($filter, $query)
    {
        return $query->where($filter['column'], '=', $filter['query_1'], $filter['match']);
    }

    public function notEqualTo($filter, $query)
    {
        return $query->where($filter['column'], '<>', $filter['query_1'], $filter['match']);
    }

    public function lessThan($filter, $query)
    {
        return $query->where($filter['column'], '<', $filter['query_1'], $filter['match']);
    }

    public function greaterThan($filter, $query)
    {
        return $query->where($filter['column'], '>', $filter['query_1'], $filter['match']);
    }

    public function between($filter, $query)
    {
        return $query->whereBetween($filter['column'], [
            $filter['query_1'], $filter['query_2']
        ], $filter['match']);
    }

    public function notBetween($filter, $query)
    {
        return $query->whereNotBetween($filter['column'], [
            $filter['query_1'], $filter['query_2']
        ], $filter['match']);
    }
}