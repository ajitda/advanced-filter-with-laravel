<?php
namespace App\Support;

class CustomQueryBuilder
{
    public function apply($query, $data)
    {
        if(isset($data['f'])) {
            foreach ($data['f'] as $filter) {
                $filter['match'] = isset($data['filter_match']) ? $data['filter_match'] : "and";
                
            }
        }
        return $query;
    }
}