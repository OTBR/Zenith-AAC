<?php namespace App\Modules\Server\Controllers;

/**
 * Load module-related classes
 */
use App\Modules\Server\Models\Account;
use App\Modules\Server\Models\Character;
use App, Auth, Config, Input, Redirect, Validator, View;

class CharactersController extends \BaseController {

	protected $layout = 'public.master';

	public function index() {
		return View::make('characters.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function online($column = 'name', $order = 'asc') {
		$characters = Character::join('players_online', 'players.id', '=', 'players_online.player_id')->get();
		return View::make('characters.online', array(
			'characters' => $characters
		));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		return View::make('characters.create', array(	
			'title' => 'Create character',
		));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
		/**
		 * I think this code needs clean-up, it looks really messy (but works).
		 * I decided to use 'city' instead of `town_id` client-side because of
		 * convention (who calls them "Town" anyway? Everybody uses "City").
		 */
		$input = Input::only('name', 'sex', 'vocation', 'city');
		
		$input['town_id'] = (int)$input['city'];
		
		unset($input['city']);
		
		$validator = Validator::make($input, Character::onCreateRules());

		if ($validator->passes()) {
			/**
			 * Since cities are stored with an index pointing to it's name and
			 * position values (posx, posy, posz), I just append the value array.
			 */
			$input += Config::get('zenith.cities')[$input['town_id']];
			
			$input['account_id'] = Auth::user()->id;
			if (Character::create($input)) {
				return Redirect::route('account.show');
			} else {
				return Redirect::back()->with('flash_error', 'Your character could not be created. Contact the server administrator and ask him/her to fill a bug report for Zenith.');
			}
		} else {
			return Redirect::back()->withInput()->withErrors($validator);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $name
	 * @return Response
	 */
	public function show($name) {
		if ($character = Character::withTrashed()->with('house')->where('name', $name)->first()) {
			$account = Account::with('bans', 'characters')->find($character->account->id);
			return View::make('characters.show', array(
				'account'	=> $account,
				'character' => $character
			));
		} else {
			return Redirect::route('character.index')->with('error', trans('character.errors.not-found', array('name' => $name)));
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $name
	 * @return Response
	 */
	public function edit($name) {
		$character = Character::withTrashed()->where('name', $name)->first();
		if ($character->account_id === Auth::user()->id) {
			return View::make('characters.edit', array(
				'character' => $character
			));
		} else {
			App::abort(403);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string  $name
	 * @return Response
	 */
	public function update($name) {
		$character = Character::withTrashed()->where('name', $name)->first();
		if ($character->account_id === Auth::user()->id) {
			/**
			 * Special cases. These do not follow REST routing according to my fellow
			 * freaks at StackOverflow. Thanks to them for this information!
			 */
			switch(Input::get('_action')) {
				case 'delete':
					if ($character->delete()) {
						return Redirect::route('account.show');
					} else {
						return Redirect::route('account.show')->with('flash_error', 'Your character could not be deleted.');
					}
				case 'undelete':
					if ($character->restore()) {
						return Redirect::route('account.show');
					} else {
						return Redirect::route('account.show')->with('flash_error', 'Your character could not be restored.');
					}
			}
		
			/**
			 * Because of some drunken developers, HTML does not send the value if a
			 * checkbox is not checked. Instead, we have to check if the value exists,
			 * not whether it is checked or not.
			 */
			$character->is_hidden = Input::has('is_hidden');
			$character->comment = Input::get('comment');
			if ($character->save()) {
				return Redirect::route('account.show');
			} else {
				return Redirect::back()->with('flash_error', 'Your character could not be updated. Contact the server administrator and ask him/her to fill a bug report for Zenith.');
			}
		} else {
			App::abort(403);
		}
	}
}
