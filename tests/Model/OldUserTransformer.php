<?php


namespace RandomState\Tests\LaravelApi\Model;


use League\Fractal\TransformerAbstract;

class OldUserTransformer extends TransformerAbstract {

	public function transform(User $user)
	{
		return [
			'name' => $user->name,
		];
	}
}
