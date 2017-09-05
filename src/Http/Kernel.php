<?php


namespace RandomState\LaravelApi\Http;

use Illuminate\Foundation\Http\Kernel as FoundationKernel;

class Kernel extends FoundationKernel
{
	protected function dispatchToRouter()
	{
		// Reconstruct the kernel and reset the class' router to use our new
		// extended router which got instantiated and bound into the IoC after
		// the default router got set up and bound. This might look kinda odd,
		// but poses no direct consequences.
		parent::__construct($this->app, $this->app->make('router'));

		// Continue as normal
		return parent::dispatchToRouter();
	}
}