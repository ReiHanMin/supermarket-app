<x-app-layout>
    @if ($bentos->count() === 0)
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
                        <div class="store-item mb-6 flex items-center bg-white p-4 rounded-lg shadow-md">
                            <!-- Store Image -->
                            <img 
                                src="{{ asset($store->photo) }}" 
                                alt="{{ $store->name }}" 
                                class="w-16 h-16 object-cover rounded-md mr-4"
                            /> 
                            <!-- Store Details -->
                            <div>
                                <h4 class="font-semibold text-gray-700">{{ $store->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $store->distance }} km</p>
                                <p class="text-sm {{ $store->is_open ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $store->is_open ? 'Open until ' . $store->closing_time : 'Closed' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Bento Section: Flex-grow to take the remaining 3/4 of the width -->
            <div class="flex-grow">
                <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                    @foreach ($bentos as $bento)
                        @if ($bento->updates->isNotEmpty())
                            @php $latestUpdate = $bento->updates->first(); @endphp
                            <div class="border border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white overflow-hidden">
                                <a href="{{ route('bento.show', $bento->id) }}">
                                    <!-- Display the image using the 'image_url' field with uniform size and covering top 2/3 -->
                                    <img src="{{ asset('storage/' . $bento->image_url) }}"
                                         alt="{{ $bento->name }}"
                                         class="w-full h-48 object-cover transition-transform transform hover:scale-105 hover:rotate-1" />
                                </a>
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $bento->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $latestUpdate->store->name }}</p>
                                    <h5 class="font-bold text-purple-600 mt-2">{{ number_format($latestUpdate->discounted_price, 0) }}円</h5>
                                    <p class="text-sm text-red-600">{{ round($latestUpdate->discount_percentage) }}% off</p>
                                    <p class="text-sm text-gray-500">{{ $latestUpdate->availability }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
