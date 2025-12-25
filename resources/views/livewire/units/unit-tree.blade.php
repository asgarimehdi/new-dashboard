<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">نمایش درختی واحدها</h1>

    @foreach($roots as $root)
        @include('livewire.units.partials.unit-node', ['unit' => $root])
    @endforeach

</div>
