@extends('public.master')

@section('content')
	<div class='account-info'>
		<header>
			<h2>{{{ trans('account.headers.account-information') }}}</h2>
		</header>
		<div>
			<span class='title'>{{{ trans('account.spans.name') }}}:</span>
			<span class='value'>
				{{{ $account->name }}}
				@if(Config::get('zenith.allow_account_name_change'))
					{{ HTML::link(route('account.update'), trans('account.actions.change'), array('class' => 'right', 'data-action' => 'change-name', 'data-method' => 'PUT', 'data-key' => 'name' )) }}
				@endif
			</span>
		</div>
		{{--
		  -- Here I both have to create an variable and use if, hence the nested
			-- if.
  		--}}
		@if($current_ban = $account->bans->last())
			@unless($current_ban->expires_at->isPast())
				<div class='red'>
					<span class='title'>{{{ trans('account.spans.banished') }}}:</span>
					<span class='value'>until {{{ $current_ban->expires_at->format(Config::get('zenith.long_datetime_format')) }}} because of {{{ $current_ban->reason }}}</span>
				</div>
			@endunless
		@endif
		<div>
			<span class='title'>{{{ trans('account.spans.email-address') }}}:</span>
			<span class='value'>
				{{{ $account->email ?: trans('account.spans.email-not-set') }}}
				@if(empty($account->email) || Config::get('zenith.allow_account_email_change'))
					{{ HTML::link(route('account.update'), trans('account.change'), array('class' => 'right', 'data-action' => 'change-email', 'data-method' => 'PUT', 'data-key' => 'email' )) }}
				@endif
			</span>
		</div>
		<div>
			<span class='title'>{{{ trans('account.spans.created-at') }}}:</span>
			<span class='value'>{{{ $account->creation->format(Config::get('zenith.long_datetime_format')) }}}</span>
		</div>
		<div>
			<span class='title'>{{{ trans('account.spans.last-login') }}}:</span>
			<span class='value'>{{{ $account->lastday->timestamp ? $account->lastday->format(Config::get('zenith.long_datetime_format')) : trans('account.spans.never-logged-in') }}}</span>
		</div>
		<div>
			<span class='title'>{{{ trans('account.spans.status') }}}:</span>
			<span class='value {{{ $account->premend->isFuture() ? 'green' : 'red' }}}'>
				<strong>{{{ $account->premend->isFuture() ? 'premium' : 'free' }}} account</strong>
				@if ($account->premend->isFuture())
					<small>until {{{ $account->premend->diffInDays() > 2 ? $account->premend->format(Config::get('zenith.long_datetime_format')) : ($account->premend->isToday()	? 'today'	: 'tomorrow') }}}</small>
				@endif
			</span>
		</div>
		@section('scripts')
			@parent
			<script>
				$(document).ready(function() {
					$('.value>a[data-method]').click(function(event) {
						event.preventDefault();
						var _parent   = $(this).parent(),
						    _previous = _parent.html();
						
						var _form   = $('<form method="POST" action="{{{ route('account.update') }}}" accept-charset="UTF-8">'),
								_method = $('<input name="_method" type="hidden" value="PUT">'),
						    _token  = $('<input name="_token" type="hidden" value="' + token + '">'),
								_input  = $('<input type="text" name="' + $(this).data('key') + '">'),
								_submit = $('<input type="submit" value="Submit">'),
								_reset  = $('<input type="button" value="Cancel">').on('click', function() {
									_parent.html(_previous);
								});
								
						_form.append(_method).append(_token).append(_input).append(_submit).append(_reset);
						
						_parent.html(_form);
					});
				});
			</script>
		@stop
	</div>
	@unless($account->bans->isEmpty())
		<div class='criminal-record'>
			<header>
				<h2>Criminal record</h2>
				<h3>
					<span class='date'>Banned</span>
					<span class='reason'>Reason</span>
					<span class='duration'>Duration</span>
				</h3>
			</header>
			@foreach($account->bans as $ban)
				<div @if($ban->expires_at->isFuture()) {{ 'class=\'active\'' }} @endif >
					<span class='date'>{{{ $ban->banned_at->format(Config::get('zenith.long_datetime_format')) }}}</span>
					<span class='reason'>{{{ $ban->reason }}}</span>
					<span class='duration'>{{{ $ban->expires_at->diffInDays($ban->banned_at) }}} days</span>
				</div>
			@endforeach
		</div>
	@endunless
	<div class='account-characters'>
		<header>
			<h2>{{{ trans('account.headers.account-characters') }}}</h2>
			<h3>
				<span class='information'>Information</span>
				<span class='world'>World</span>
				<span class='status'>Status</span>
				<span class='options'>Options</span>
			</h3>
		</header>
		@unless($account->characters->isEmpty())
			@foreach($account->characters as $character)
				<div>
					<span class='information'>
						<strong>{{ HTML::link(route('character.show', array('name' => $character->name)), $character->name) }}</strong>
						<br/>
						<small>{{{ Config::get('zenith.vocations')[$character->vocation] }}} - {{{ trans('character.spans.level') }}} {{{ $character->level }}}</small>
					</span>
					<span class='world'>Forgotten</span>
					<span class='status'>{{{ implode(', ', $character->status) }}}</span>
					<span class='options'>
						{{ HTML::link(route('character.edit', array('name' => $character->name)), trans('character.actions.edit')) }}
						<br/>
						@unless($character->deletion->isFuture())
							{{ HTML::link(route('character.update', array('name' => $character->name)), trans('character.actions.delete'), array('data-action' => 'delete', 'data-method' => 'PUT')) }}
						@else
							{{ HTML::link(route('character.update', array('name' => $character->name)), trans('character.actions.undelete'), array('data-action' => 'undelete', 'data-method' => 'PUT')) }}
						@endunless
					</span>
				</div>
			@endforeach
			@section('scripts')
				@parent
				<script defer>
					$(document).ready(function() {
						$('.options>a[data-method]').click(function(event) {
							event.preventDefault();
							$.ajax({
								type: 'POST',
								url: $(this).attr('href'),
								data: { _method: $(this).data('method'), _token: token, _action: $(this).data('action') },
								success: function(data) { window.location.reload(true); }
							});
						});
					});
				</script>
			@stop
		@else
			<span class='empty'>{{ HTML::link(route('character.create'), 'You don\'t have any characters. Why don\'t you create your first now?') }}</span>
		@endunless
		<a href='{{{ route('character.create') }}}' class='create-character'>Create character</a>
	</div>
@stop
