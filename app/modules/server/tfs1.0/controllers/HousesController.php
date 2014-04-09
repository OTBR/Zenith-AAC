<?php namespace App\Modules\Server\Controllers;

use App\Modules\Server\Models\House;
use View;
class HousesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$houses = House::orderBy('town_id');
		return View::make('houses.index', array(
			'houses' => $houses,
		));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  mixed  $id
	 * @return Response
	 */
	public function show($id) {
		/**
		 * PHP is really a bizarre language. I can't rely on 'is_num' since it only
		 * checks the type, and for REST parameters, it is always string. On the
		 * other hand, I can't rely on 'is_numeric' too since it allows octal and
		 * hexadecimal values. That's why I use 'ctype_digit', which allows only
		 * characters in the range 0-9.
		 */
		if (ctype_digit($param)) {
			$house = House::find($param);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

}
