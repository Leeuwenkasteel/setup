<x-templates::app>
	<table class="table">
        <thead>
            <tr>
                <th>{{__('Package')}}</th>
                <th>{{__('Status')}}</th>
                <th>{{__('Description')}}</th>
				<th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($table as $t)
                <tr id="package-{{ $t['name'] }}">
                    <td>{{ $t['name'] }}</td>
                    <td id="status-{{ $t['name'] }}" class="text-muted">Verwerken...</td>
                    <td>{{ $t['description'] }}</td>
					<td id="pull-{{ $t['name'] }}">
					</td>
                </tr>
            @endforeach
        </tbody>
    </table>
	@pushonce('scripts')
		<script>
        document.addEventListener('DOMContentLoaded', function() {
            const packages = @json($table);

            for (const packageName in packages) {
                fetchPackageStatus(packageName);
            }

            function fetchPackageStatus(packageName) {
                fetch(`/admin/git-status/${packageName}`)
                    .then(response => response.json())
                    .then(data => {
						console.log(data.statusText);
                        // Update de status in de tabel
                        const statusCell = document.getElementById('status-' + packageName);
                        statusCell.textContent = data.statusText;
                        statusCell.className = data.statusClass;
						if(data.statusClass == 'text-warning'){
							console.log('pull');
							const pullCell = document.getElementById('pull-' + packageName);
							pullCell.innerHTML = '<a href="/admin/pull/' + packageName + '" class="btn btn-warning">{{__("Pull")}}</a>';
						}
                    })
                    .catch(error => {
                        console.error('Error fetching status for package ' + packageName, error);
                    });
            }
        });
    </script>
	@endpushonce
</x-templates::app>