@include ('layouts.header')

@include ('layouts.sidebar')

<div class="content">
    <div class="container-fluid">
@yield('content')
	</div>
</div>

@include ('layouts.footer')