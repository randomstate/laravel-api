<?php


namespace RandomState\Tests\LaravelApi\Model;


use League\Fractal\TransformerAbstract;

class CurrentUserTransformer extends TransformerAbstract {

	public function transform(User $user)
	{
		return [
			'name' => 'new'
		];
	}
}