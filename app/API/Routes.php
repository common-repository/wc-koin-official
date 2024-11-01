<?php

namespace WKO\API;

use WKO\API\Routes\CheckoutFields;

class Routes
{
	public function register()
	{
		new CheckoutFields();
	}
}
