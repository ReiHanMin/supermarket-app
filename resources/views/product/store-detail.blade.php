<x-app-layout>
    <div class="bg-gray-100 min-h-screen p-8">
        <div class="container mx-auto p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: Store Details -->
            <div>
                <div class="store-header bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $store->name }}</h1>
                    <img src="{{ asset($store->photo) }}" alt="{{ $store->name }} image" class="w-full h-80 object-cover rounded-lg shadow-md mt-4">
                    <p class="store-distance mt-2"><strong>Distance:</strong> Calculating...</p>
                    <p><strong>Open Hours:</strong></p>
                    <ul>
                        @foreach($store->opening_hours as $day => $hours)
                            <li>{{ $day }}: {{ $hours['start'] }} - {{ $hours['end'] }}</li>
                        @endforeach
                    </ul>
                </div>                    
                <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $store->latitude }},{{ $store->longitude }}" target="_blank">
                            <iframe 
                                width="100%" 
                                height="300" 
                                frameborder="0" 
                                style="border:0; cursor: pointer;" 
                                src="https://www.google.com/maps?q={{ $store->latitude }},{{ $store->longitude }}&hl=en&z=14&output=embed" 
                                allowfullscreen>
                            </iframe>
                        </a>
                    </div>
                    
                </div>
            </div>

            <!-- Right Column: Store Info and Bentos -->
            <div>
                <div class="store-info bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h3 class="text-xl font-bold mb-4">Store Information</h3>
                    <p class="mt-2"><strong>Address:</strong> {{ $store->address }}</p>
                    <p class="mt-2"><strong>Contact:</strong> {{ $store->phone }}</p>
                </div>

                <div class="bentos-section bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h3 class="text-xl font-bold mb-4">Available Bentos</h3>
                    <table class="table-auto w-full mt-4 border-collapse border border-gray-300">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-300 p-4 text-left">Bento</th>
                                <th class="border border-gray-300 p-4 text-left">Bento Name</th>
                                <th class="border border-gray-300 p-4 text-left">Original Price</th>
                                <th class="border border-gray-300 p-4 text-left">Discounted Price</th>
                                <th class="border border-gray-300 p-4 text-left">Expected Discount</th>
                                <th class="border border-gray-300 p-4 text-left">Usual Markdown Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bentos as $bento)
                                <tr class="{{ $loop->index % 2 == 0 ? 'bg-gray-100' : 'bg-white' }}">
                                    <td class="border border-gray-300 p-4">
                                        <a href="{{ route('bento.show', $bento->id) }}">
                                            <img src="{{ asset('storage/' . $bento->image_url) }}" alt="{{ $bento->name }}" class="w-16 h-16 object-cover rounded-md">
                                        </a>
                                    </td>                          
                                    <td class="border border-gray-300 p-4">{{ $bento->name }}</td>
                                    <td class="border border-gray-300 p-4">¥{{ $bento->original_price }}</td>
                                    <td class="border border-gray-300 p-4">¥{{ $bento->usual_discounted_price }}</td>
                                    <td class="border border-gray-300 p-4">{{ $bento->discount }}% off</td>
                                    <td class="border border-gray-300 p-4">{{ $bento->markdown_time }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('home') }}" class="btn btn-lg bg-blue-600 text-white py-3 px-6 rounded shadow-md hover:bg-blue-700 fixed bottom-4 right-4">Back to Landing Page</a>
            </div>
        </div>
    </div>

    <!-- Google Maps JavaScript -->
    <script>
        function initMap() {
            var storeLocation = {lat: {{ $store->latitude }}, lng: {{ $store->longitude }}};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: storeLocation
            });
            var marker = new google.maps.Marker({
                position: storeLocation,
                map: map
            });
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap">
    </script>

    <!-- JavaScript for Calculating Distance -->
    <script type="module">
        import { getUserLocation, calculateDistance } from "{{ asset('js/distanceCalculator.js') }}";

        window.onload = function () {
            // Calculate and update distance using user's location
            getUserLocation(function (error, userLat, userLng) {
                if (!error) {
                    const storeLat = {{ $store->latitude }};
                    const storeLng = {{ $store->longitude }};
                    if (storeLat && storeLng) {
                        const distance = calculateDistance(userLat, userLng, storeLat, storeLng).toFixed(2);
                        document.querySelector('.store-distance').innerText = `Distance: ${distance} km away`;
                    } else {
                        document.querySelector('.store-distance').innerText = 'Distance: N/A';
                    }
                } else {
                    document.querySelector('.store-distance').innerText = 'Distance: N/A';
                }
            });
        };
    </script>
</x-app-layout>
