<div class="row">
<div class="col-md-3">
	<p class="
		@if($step == 1)text-primary
		@elseif($step > 1)text-success
		@else text-seconday
		@endif
		"><i class="bi bi-{!!$step > 1 ? 'check2-' : ''!!}circle"></i> Stap 1: Stap 1: GitHub Token invoeren</p>
	<p class="
		@if($step == 2)text-primary
		@elseif($step > 2)text-success
		@else text-seconday
		@endif
		"><i class="bi bi-{!!$step > 2 ? 'check2-' : ''!!}circle"></i> Stap 2: Instaleren benodige packages</p>
	<p class="
		@if($step == 3)text-primary
		@elseif($step > 3)text-success
		@else text-seconday
		@endif
		"><i class="bi bi-{!!$step > 3 ? 'check2-' : ''!!}circle"></i> Stap 3: Instaleren extra packages</p>
	<p class="
		@if($step == 4)text-primary
		@elseif($step > 4)text-success
		@else text-seconday
		@endif
		"><i class="bi bi-{!!$step > 4 ? 'check2-' : ''!!}circle"></i> Stap 4: Composer update</p>
	<p class="
		@if($step == 5)text-primary
		@elseif($step > 5)text-success
		@else text-seconday
		@endif
		"><i class="bi bi-{!!$step > 5 ? 'check2-' : ''!!}circle"></i> Stap 5: Database instellingen</p>
</div>
<div class="col-md-5">
@if($step == 1)
	<h2>Stap 1: GitHub Token invoeren</h2>
<form wire:submit="addToken">
	@csrf
	<label for="github_token">GitHub Token:</label>
	<input type="text" name="github_token" wire:model="token" required class="form-control">
	<button type="submit" class="btn btn-primary mt-3">Volgende</button>
</form>
@elseif($step == 2)
<h2>Stap 2: Instaleren benodige packages</h2>
<table class="table w-full border-collapse border border-gray-300">
        <thead>
            <tr>
				<th class="border border-gray-300 p-2"></th>
                <th class="border border-gray-300 p-2">Pacakge</th>
                <th class="border border-gray-300 p-2">Beschrijving</th>
				<th class="border border-gray-300 p-2">Benodigede packages</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packages as $key => $package)
			@if($package['kind'] == 'basic')
                <tr>
					<td><input type="checkbox" disabled checked></td>
                    <td class="border border-gray-300 p-2">{{ $package['name'] }}</td>
                    <td class="border border-gray-300 p-2">{{ $package['description'] }}</td>
					<td class="border border-gray-300 p-2">
						@if(!empty($package['dependencies']))
						@foreach($package['dependencies'] as $d)
						<span class="badge text-bg-primary">{{$d}}</span>
						@endforeach
						@endif</td>
                </tr>
			@endif
            @endforeach
        </tbody>
    </table>
	<button wire:click="installGeneral" class="btn btn-primary mt-3">Instaleren</button>
	<button wire:click="changeStep('3')" class="btn btn-danger mt-3">Overslaan</button>
@elseif($step == 3)
<h2>Stap 3: Instaleren extra packages</h2>
<table class="table w-full border-collapse border border-gray-300">
        <thead>
            <tr>
				<th class="border border-gray-300 p-2"></th>
                <th class="border border-gray-300 p-2">Pacakge</th>
                <th class="border border-gray-300 p-2">Beschrijving</th>
				<th class="border border-gray-300 p-2">Benodigede packages</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packages as $key => $package)
			@if($package['kind'] != 'basic')
                <tr>
					<td><input type="checkbox" wire:model="extra.{{$package['name']}}"></td>
                    <td class="border border-gray-300 p-2">{{ $package['name'] }}</td>
                    <td class="border border-gray-300 p-2">{{ $package['description'] }}</td>
					<td class="border border-gray-300 p-2">
						@if(!empty($package['dependencies']))
						@foreach($package['dependencies'] as $d)
						<span class="badge text-bg-primary">{{$d}}</span>
						@endforeach
						@endif</td>
                </tr>
			@endif
            @endforeach
        </tbody>
    </table>
	<button wire:click="installExtra" class="btn btn-primary mt-3">Instaleren</button>
	<button wire:click="changeStep('4')" class="btn btn-danger mt-3">Overslaan</button>
@elseif($step == 4)
	<h2>Stap 4: Composer update</h2>
	<p>Voer een composer update uit in de console</p>
	<button wire:click="changeStep('3')" class="btn btn-success mt-3">Vorige</button>
	<button wire:click="changeStep('5')" class="btn btn-danger mt-3">Volgende</button>
@elseif($step == 5)
	<h2>Stap 5: Database instellingen</h2>
	<form wire:submit="saveDb">
	<select class="form-control" wire:click="changeEvent($event.target.value)" wire:model="DB_CONNECTION">
		<option value="sqlite">sqlite</option>
		<option value="mysql">mysql</option>
	</select>
	@if($showDbDetails)
		<div class="row mt-2">
			<div class="col-md-3">DB_HOST</div>
			<div class="col-md-9"><input type="text" class="form-control" wire:model="DB_HOST"></div>
		</div>
		<div class="row mt-2">
			<div class="col-md-3">DB_PORT</div>
			<div class="col-md-9"><input type="text" class="form-control" wire:model="DB_PORT"></div>
		</div>
		<div class="row mt-2">
			<div class="col-md-3">DB_DATABASE</div>
			<div class="col-md-9"><input type="text" class="form-control" wire:model="DB_DATABASE"></div>
		</div>
		<div class="row mt-2">
			<div class="col-md-3">DB_USERNAME</div>
			<div class="col-md-9"><input type="text" class="form-control" wire:model="DB_USERNAME"></div>
		</div>
		<div class="row mt-2">
			<div class="col-md-3">DB_PASSWORD</div>
			<div class="col-md-9"><input type="text" class="form-control" wire:model="DB_PASSWORD"></div>
		</div>
	@endif
	<button wire:click="changeStep('4')" class="btn btn-success mt-3">Vorige</button>
	<button type="submit" class="btn btn-primary mt-3">Opslaan</button>
	<button wire:click="changeStep('6')" class="btn btn-danger mt-3">Overslaan</button>
	</form>
@elseif($step == 6)
	<h2>Stap 6: Instaleren Packages</h2>
	<table class="table w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2">Pacakge</th>
				<th class="border border-gray-300 p-2"></th>
            </tr>
        </thead>
        <tbody>
		@foreach($installPackages as $iPack)
                <tr>
                    <td class="border border-gray-300 p-2">{{$iPack}}</td>
                    <td class="border border-gray-300 p-2 text-end"><button class="btn btn-primary btn-sm" wire:click="install('{{$iPack}}')">Instaleren</button></td>
                </tr>
		@endforeach
        </tbody>
    </table>
	<button wire:click="changeStep('5')" class="btn btn-success mt-3">Vorige</button>
	<button wire:click="finish()" class="btn btn-danger mt-3">Afronden</button>
@endif
</div>
<div class="col-md-4">
	@if(!empty($notifications))
		<div class="bg-dark text-white p-3">
			@foreach($notifications as $note)
				{{$note}}<br/>
			@endforeach
		</div>
	@endif
</div>
</div>
