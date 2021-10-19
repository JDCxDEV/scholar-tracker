<footer class="main-footer bg-light overflow-hidden">
	@if ($env->dev())
    	<developer-mode label="{{ config('web.version') }}" fetch-url="{{ route('developer.users.fetch') }}" submit-url="{{ route('developer.users.change-account') }}"></developer-mode>
	@else
	    <div class="float-right d-none d-sm-block text-dark">
			<b>Version</b> {{ config('web.version') }}
	    </div>
	@endif
	<strong>Copyright &copy; {{ now()->year }} <a class="text-secondary" href="javascript:void(0)">{{ config('app.name') }}</a>.</strong> All rights reserved.
</footer>
