<x-app-layout>
    @if ($closestBentos->count() === 0 && $hotDeals->count() === 0 && $exploreMoreBentos->count() === 0)
        <div class="text-center text-gray-600">No bentos found</div>
    @else
        <!-- Main Wrapper Flexbox Layout -->
        <div class="flex gap-6 p-6">
            <!-- Stores Section: Flex Basis set to occupy 1/4 of the width -->
            <div class="basis-1/4 flex-shrink-0">
                <div class="nearby-stores bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">近くの店舗</h3>
                    
                    <!-- Loop through stores and display relevant information -->
                    @foreach ($stores as $store)
                    <a href="{{ route('store.detail', $store->id) }}">
                        <div class="store-item mb-6 flex items-center bg-white p-4 rounded-lg shadow-md transform transition-all duration-300 hover:bg-gray-100 hover:scale-105 cursor-pointer" data-store-id="{{ $store->id }}">
                            <!-- Store Image -->
                            <img src="{{ asset($store->photo) }}" alt="{{ $store->name }}" class="w-16 h-16 object-cover rounded-md mr-4" /> 
                    
                            <!-- Store Details -->
                            <div>
                                <h4 class="font-semibold text-gray-700">{{ $store->name }}</h4>
                                <p class="text-sm text-gray-500 distance" data-lat="{{ $store->latitude }}" data-lon="{{ $store->longitude }}">
                                    Calculating distance...
                                </p>                            
                                <!-- Show store status -->
                                @if (isset($store->status))
                                    <p class="text-sm {{ $store->status === 'Open' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $store->status }}
                                    </p>
                                @else
                                    <p class="text-sm text-red-600">Status not available</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach                    
                </div>
            </div>

            <!-- Bento Sections: Flex-grow to take the remaining 3/4 of the width -->
            <div class="flex-grow">
                <!-- Closest Bentos Section -->
                <div class="mb-6">
                    <h3 class="text-xl font-bold mb-4">Closest Bentos</h3>
                    <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                        @foreach ($closestBentos as $bento)
                            <div class="border border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white overflow-hidden">
    <a href="{{ route('bento.show', $bento->id) }}">
        <img src="{{ asset('storage/' . $bento->image_url) }}"
             alt="{{ $bento->name }}"
             class="w-full h-48 object-cover transition-transform transform hover:scale-105 hover:rotate-1" />
    </a>
    <div class="p-4">
        <h3 class="text-lg font-bold text-gray-800">{{ $bento->name }}</h3>
        <p class="text-sm text-gray-600">{{ $bento->updates->first()->store->name ?? 'Store Unknown' }}</p>
        <h5 class="font-bold text-purple-600 mt-2">{{ number_format($bento->usual_discounted_price, 0) }}円</h5>
        <p class="text-sm text-red-600">{{ round($bento->discount_percentage) }}% off</p>
        <p class="text-sm text-gray-500">{{ $bento->availability }}</p>
    </div>
</div>
                        @endforeach
                    </div>
                </div>

                <!-- Hot Deals Right Now Section (only visible between 4pm and 10pm) -->
                @php
                    $currentHour = now()->format('H');
                @endphp
                @if ($currentHour >= 16 && $currentHour < 22)
                    <div class="hot-deals-section mb-6">
                        <h3 class="text-xl font-bold mb-4">Hot Deals Right Now</h3>
                        <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                            @foreach ($hotDeals as $bento)
                                <div class="border border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white overflow-hidden">
    <a href="{{ route('bento.show', $bento->id) }}">
        <img src="{{ asset('storage/' . $bento->image_url) }}"
             alt="{{ $bento->name }}"
             class="w-full h-48 object-cover transition-transform transform hover:scale-105 hover:rotate-1" />
    </a>
    <div class="p-4">
        <h3 class="text-lg font-bold text-gray-800">{{ $bento->name }}</h3>
        <p class="text-sm text-gray-600">{{ $bento->updates->first()->store->name ?? 'Store Unknown' }}</p>
        <h5 class="font-bold text-purple-600 mt-2">{{ number_format($bento->discounted_price, 0) }}円</h5>
        <p class="text-sm text-red-600">{{ round($bento->discount_percentage) }}% off</p>
        <p class="text-sm text-gray-500">{{ $bento->availability }}</p>
    </div>
</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Explore More Section -->
                <div class="explore-more-section mb-6">
                    <h3 class="text-xl font-bold mb-4">Explore More</h3>
                    <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                        @foreach ($exploreMoreBentos as $bento)
                            <div class="border border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white overflow-hidden">
    <a href="{{ route('bento.show', $bento->id) }}">
        <img src="{{ asset('storage/' . $bento->image_url) }}"
             alt="{{ $bento->name }}"
             class="w-full h-48 object-cover transition-transform transform hover:scale-105 hover:rotate-1" />
    </a>
    <div class="p-4">
        <h3 class="text-lg font-bold text-gray-800">{{ $bento->name }}</h3>
        <p class="text-sm text-gray-600">{{ $bento->updates->first()->store->name ?? 'Store Unknown' }}</p>
        <h5 class="font-bold text-purple-600 mt-2">{{ number_format($bento->discounted_price, 0) }}円</h5>
        <p class="text-sm text-red-600">{{ round($bento->discount_percentage) }}% off</p>
        <p class="text-sm text-gray-500">{{ $bento->availability }}</p>
    </div>
</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- JavaScript to handle geolocation and distance calculation -->
    <!-- Import distanceCalculator.js module -->
<script type="module">
    import { getUserLocation } from "{{ asset('/js/distanceCalculator.js') }}";

    // Use the imported function
    window.onload = function () {
        getUserLocation(function (error, userLat, userLng) {
            if (!error) {
                fetch("{{ route('store.updateLocation') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        userLat: userLat,
                        userLng: userLng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.stores.forEach(store => {
                            const storeElement = document.querySelector(`.store-item[data-store-id="${store.id}"] .distance`);
                            if (storeElement) {
                                storeElement.innerHTML = `${store.distance} km away`;
                            }
                        });
                    } else {
                        console.error("Error:", data.message);
                    }
                })
                .catch(error => {
                    console.error("Error sending geolocation data:", error);
                });
            } else {
                console.log("Unable to retrieve user location.");
            }
        });
    };
</script>

</x-app-layout>
