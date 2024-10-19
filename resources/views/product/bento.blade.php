<x-app-layout>
    <div class="container mx-auto p-5 bg-white rounded-lg shadow-md">
        <!-- Bento Image and Details -->
        <div class="flex flex-wrap lg:flex-nowrap items-start gap-6">
            <!-- Bento Image -->
            <div class="w-full lg:w-1/3">
                <img
                    src="{{ asset('storage/' . $bento->image_url) }}"
                    alt="{{ $bento->name }}"
                    class="w-full object-cover rounded-md shadow-md"
                />
            </div>
            <!-- Bento Details -->
            <div class="w-full lg:w-2/3">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $bento->name }}</h1>
                <div class="rating-overview mb-4">
                    <span class="text-yellow-400">⭐⭐⭐⭐☆</span>
                    <span class="text-gray-600 ml-2">4.5 (32件のレビュー) <a href="#" class="text-blue-500 hover:underline">レビューを見る</a></span>
                </div>
                <p class="text-lg text-purple-600 font-semibold">通常価格: <span class="line-through">{{ number_format($bento->original_price, 0) }}円</span></p>
                <p class="text-2xl font-bold text-red-600 mt-2">割引価格（税込）: {{ number_format($bento->usual_discounted_price, 0) }}円</p>
                <p class="mt-2 text-gray-700">割引: 割引はまだ適用されていない可能性があります。午後4時30分頃にもう一度ご確認ください。</p>
                <p class="mt-2"><strong>在庫状況:</strong> {{ $bento->availability }}</p>
                <p class="mt-2"><strong>推定割引時間:</strong> ~4:30 PM</p>
                <div class="button-container flex gap-4 mt-6">
                    <button class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">アラートを設定</button>
                    <button class="bg-[#F50057] text-white py-3 px-6 rounded-lg hover:bg-[#C51162] focus:ring-4 focus:ring-[#F50057]/50 transition-colors">❤ ウィッシュリストに追加</button>
                </div>
            </div>
        </div>

        <!-- Store Information -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">近隣店舗での在庫状況</h3>
            <div class="store-listing grid gap-6">
                @foreach ($bento->stores as $store)
                    <div class="flex items-start gap-4 p-4 bg-white rounded-lg shadow-md">
                        <img
                            src="{{ asset('storage/' . $store->image_url) }}"
                            alt="{{ $store->name }}"
                            class="w-20 h-20 object-cover rounded-md"
                        />
                        <div>
                            <h4 class="font-semibold text-gray-700">{{ $store->name }}</h4>
                            <p class="text-sm text-gray-500">距離: {{ $store->distance ?? 'N/A' }} km</p>
                            <p class="text-sm {{ $store->is_open ? 'text-green-600' : 'text-red-600' }}">
                                {{ $store->is_open ? 'Open until ' . $store->closing_time : 'Closed' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>