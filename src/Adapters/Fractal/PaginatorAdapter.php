<?php


namespace RandomState\LaravelApi\Adapters\Fractal;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceAbstract;
use RandomState\Api\Transformation\Adapters\FractalAdapter;

class PaginatorAdapter extends FractalAdapter {

	public function transforms($data)
	{
		return $data instanceof LengthAwarePaginator;
	}

	/** @param LengthAwarePaginator $data
	 *
	 * @return ResourceAbstract
	 */
	public function getResource($data)
	{
		$resource = new Collection($data->items(), $this->switchboard);
		$resource->setPaginator(new IlluminatePaginatorAdapter($data));

		return $resource;
	}
}