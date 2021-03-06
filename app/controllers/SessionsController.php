<?php

use Illuminate\Support\MessageBag;
use App\Modules\Server\Models\Account;
use Auth, View;
class SessionsController extends \BaseController {

	protected $layout = 'public.master';
	
	/**
	 * Show the form for creating a new resource.
	 * GET /login
	 *
	 * @return Response
	 */
	public function create() {
		return View::make('sessions.create', array(	
			'title' => 'Login account',
		));
	}

	/**
	 * Remove the specified resource from storage.
	 * GET /logout
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy() {
		Auth::logout();
		return Redirect::home();
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /login
	 *
	 * @return Response
	 */
	public function store() {
		if ($auth = Auth::attempt(array(Input::only('name', 'password'), true)) {
			return Redirect::route('account.show');
		} else {
			return Redirect::back()->with('error', 'Account name or password is not correct.');
		}
	}
}
