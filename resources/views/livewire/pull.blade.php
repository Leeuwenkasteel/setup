<div>
<table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $package)
                        <tr>
                            <td>{{ $package }}</td>
                            <td>
                                <span class="{{ $packagesStatus[$package]['class'] ?? 'text-muted' }}">
                                    {{ $packagesStatus[$package]['text'] ?? 'Onbekend' }}
                                </span>
                            </td>
                            <td>
								@if (isset($packagesStatus[$package]['status']))
									@if ($packagesStatus[$package]['status'] === 'pull_needed')
										<button wire:click="pullPackage('{{ $package }}')" class="btn btn-warning btn-sm">Pull</button>
									@else
										<span class="text-muted">Geen actie nodig</span>
									@endif
								@else
									<span class="text-muted">Status onbekend</span>
								@endif
							</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
</div>