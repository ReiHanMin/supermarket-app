<x-app-layout>
    @if ($bentos->count() === 0)
        <div>No bentos found</div>
    @else
        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-3">
            @foreach ($bentos as $bento)
                <div class="border border-1 border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white">
                    <a href="#">
                        <!-- No 'slug' in the test data, so just use '#' for the link -->
                        <img src="/img/noimage.png" alt="{{ $bento['name'] }}" class="object-cover rounded-lg hover:scale-105 hover:rotate-1 transition-transform"/>
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg">{{ $bento['name'] }}</h3>
                        <h5 class="font-bold">${{ $bento['price'] }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
