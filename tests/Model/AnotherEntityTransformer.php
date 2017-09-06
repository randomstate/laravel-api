<?php


namespace RandomState\Tests\LaravelApi\Model;


use League\Fractal\TransformerAbstract;

class AnotherEntityTransformer extends TransformerAbstract {

	public function transform(AnotherEntity $entity)
	{
		return ['entity' => $entity->property];
	}
}