<div wire:poll>
	<a class="card w-100 text-decoration-none" href="{{route('packages.index')}}">
		<div class="card-body text-center " >
			<p class="text-decoration-none">Packages update:</p>
			<div class="row">
				<div class="col-6 h1"><i class="bi bi-boxes"></i></div>
				<div class="col-6 h1 text-decoration-none">{{$count}}</div>
			</div>
		</div>
	</a>
</div>