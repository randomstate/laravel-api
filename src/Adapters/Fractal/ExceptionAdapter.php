<?php


namespace RandomState\LaravelApi\Adapters\Fractal;


use Illuminate\Http\Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use RandomState\Api\Transformation\Adapters\Adapter;
use RandomState\Api\Transformation\Adapters\FractalAdapter;
use RandomState\Api\Transformation\Fractal\Switchboard;
use RandomState\LaravelApi\Exceptions\Exception;

class ExceptionAdapter extends FractalAdapter implements Adapter {

	/**
	 * @var array
	 */
	protected $errorMap;

	public function __construct(Manager $manager, Switchboard $switchboard, array $includes = [], array $excludes = [], array $errorMap = [])
	{
		parent::__construct($manager, $switchboard, $includes, $excludes);
		$this->errorMap = $errorMap;
	}

	public function transforms($data)
	{
		return $data instanceof Exception;
	}

	/**
	 * @param Exception $data
	 * @return array|Response
	 */
	public function run($data)
	{
		return new Response(parent::run($data), $data->getHttpStatusCode());
	}

	/**
	 * @param Exception $data
	 *
	 * @return Item
	 */
	public function getResource($data)
	{
		$resource = new Item($data->getContent(), $this->switchboard);
		$resource->setMeta([
			'errors' => [
				// custom error code => // custom error message(s)
				$this->getErrorCode($data) => $data->getMessages()
			]
		]);

		return $resource;
	}

	protected function getErrorCode(Exception $exception)
	{
		return $this->errorMap[get_class($exception)] ?? $exception->getHttpStatusCode();
	}
}