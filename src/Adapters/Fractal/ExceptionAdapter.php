<?php


namespace RandomState\LaravelApi\Adapters\Fractal;


use Illuminate\Http\Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use RandomState\Api\Transformation\Adapters\Adapter;
use RandomState\Api\Transformation\Adapters\FractalAdapter;
use RandomState\Api\Transformation\Fractal\Switchboard;
use RandomState\LaravelApi\Adapters\RequiresAdapters;
use RandomState\LaravelApi\Exceptions\Exception;

class ExceptionAdapter extends FractalAdapter implements Adapter, RequiresAdapters
{

    /**
     * @var array
     */
    protected $errorMap;

    /**
     * @var FractalAdapter[]
     */
    protected $adapters;

    public function __construct(
        Manager $manager,
        Switchboard $switchboard,
        array $includes = [],
        array $excludes = [],
        array $errorMap = []
    ) {
        parent::__construct($manager, $switchboard, $includes, $excludes);
        $this->errorMap = $errorMap;
    }

    public function transforms($data)
    {
        return $data instanceof Exception;
    }

    /**
     * @param Exception $data
     *
     * @return array|Response
     */
    public function run($data)
    {
        $resource = $this->getResource($data);

        if($resource instanceof Primitive){
            $output = $this->manager->createData($resource)->transformPrimitiveResource() ?? "";
        } else {
            $output = $this->manager->createData($resource)->toArray();
        }

        return new Response([
            'data' => $output,
            'meta' => [
                'errors' => [
                    // custom error code => // custom error message(s)
                    $this->getErrorCode($data) => $data->getMessages(),
                ]
            ],
        ], $data->getHttpStatusCode());
    }

    /**
     * @param Exception $data
     *
     * @return Item
     */
    public function getResource($data)
    {
        foreach($this->adapters as $adapter) {
            if($adapter->transforms($data->getContent())) {
                return $adapter->getResource($data->getContent());
            }
        }

        return null;
    }

    protected function getErrorCode(Exception $exception)
    {
        return $this->errorMap[get_class($exception)] ?? $exception->getHttpStatusCode();
    }

    public function setAdapters(array $adapters)
    {
        $this->adapters = $adapters;

        return $this;
    }
}