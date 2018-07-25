<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

trait Dataviewer
{
	public function scopeAdvancedFilter($query)
	{
		return $this->process($query, request()->all())
					->orderBy(
							request('order_column', 'created_at'),
							request('order_direction', 'desc')
					)
					->paginate(request('limit', 10));
	}

	public function process($query, $data)
	{
		$v = validator()->make($data, [
			'order_column' => 'sometimes|required|in:'.$this->orderAbleColumns(),
			'order_direction' => 'sometimes|required|in:asc,desc',
            'limit' => 'sometimes|required|integer|min:1',
            'filter_match'=> 'sometime|required|in:and,or',
            'f' => 'sometimes|required|array',
            'f.*.column' => 'required|in:'.$this->whiteListColumn(),
            'f.*.operator' => 'required_with:f.*.column|in:'.$this->allowedFilters()
		]);
		if($v->fails()) {
			//debug
			return dd($v->messages()->all());
			return new ValidationException;
		}
		return $query;
	}

	protected function whiteListColumn()
	{
		return implode(',', $this->allowedFilters);
	}

	protected function orderAbleColumns()
	{
		return implode(',', $this->orderable);
	}

	protected function allowedOperators()
    {

    }
}