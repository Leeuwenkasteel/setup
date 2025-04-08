<div>
    <!-- Tabel weergave -->
    <table class="table">
        <thead>
            <tr>
                <th>Package</th>
                <th>Status</th>
                <th>Beschrijving</th>
            </tr>
        </thead>
        <tbody>
            @foreach($table as $package => $data)
                <tr id="package-{{ $package }}">
                    <td>{{ $package }}</td>
                    <td class="{{ $data['class'] }}">
                        {{ $data['status'] ?? 'Verwerken...' }}
                    </td>
                    <td>{{ $data['description'] ?? 'Geen beschrijving' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
Livewire.on('package-processing-start', (data) => {
    console.log(`Verwerken van package: ${data.package}`);
    // Eventueel de UI bijwerken met een indicator
});

Livewire.on('package-status-updated', (data) => {
    console.log(`Package ${data.package} is geüpdatet naar: ${data.status}`);
    // De UI bijwerken met de nieuwe status van het package
});
</script>
@endpush


