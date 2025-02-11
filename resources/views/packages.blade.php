<div>
    <h2 class="text-lg font-bold mb-4">Beschikbare Modules</h2>

    @if(session()->has('success'))
        <div class="bg-green-200 text-green-800 p-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-200 text-red-800 p-2 mb-4">
            {{ session('error') }}
        </div>
    @endif
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2">Module</th>
                <th class="border border-gray-300 p-2">Beschrijving</th>
                <th class="border border-gray-300 p-2">Acties</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packages as $key => $package)
                <tr>
                    <td class="border border-gray-300 p-2">{{ $package['name'] }}</td>
                    <td class="border border-gray-300 p-2">{{ $package['description'] }}</td>
                    <td class="border border-gray-300 p-2">
                        <button wire:click="install('{{ $key }}')" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Installeren
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>