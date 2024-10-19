<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreStatusService
{
    public function getStoreStatus($openingHours)
    {
        Log::info('Getting store status for opening hours:', $openingHours); // Log the input

        $currentDay = Carbon::now()->format('l'); // e.g., 'Monday', 'Tuesday', etc.
        $currentTime = Carbon::now()->format('H:i');
        Log::info("Current Day: $currentDay, Current Time: $currentTime");

        if (!isset($openingHours[$currentDay])) {
            Log::warning("No opening hours set for $currentDay");
            return "Closed, no opening hours available";
        }

        $storeOpenTime = $openingHours[$currentDay]['start'];
        $storeCloseTime = $openingHours[$currentDay]['end'];

        Log::info("Store open time: $storeOpenTime, Store close time: $storeCloseTime");

        // If the store is currently open
        if ($currentTime >= $storeOpenTime && $currentTime <= $storeCloseTime) {
            Log::info("Store is currently open.");
            if ($this->isClosingSoon($storeCloseTime)) {
                Log::info("Store is closing soon.");
                return "Closing soon, $storeCloseTime";
            }
            return "Open";
        }

        // If the store is currently closed
        if ($currentTime < $storeOpenTime) {
            Log::info("Store is closed but will open later today.");
            if ($this->isOpeningSoon($storeOpenTime)) {
                Log::info("Store is opening soon.");
                return "Opening soon, $storeOpenTime";
            }
            return "Closed, opens today at $storeOpenTime";
        }

        // If store is closed after hours, find next opening time
        Log::info("Store is closed for the day.");
        $nextOpeningTime = $this->getNextOpeningTime($openingHours, $currentDay);
        Log::info("Next opening time: $nextOpeningTime");
        return "Closed, opens $nextOpeningTime";
    }

    // Check if the store is closing within the next hour
    private function isClosingSoon($storeCloseTime)
    {
        Log::info("Checking if the store is closing soon. Close time: $storeCloseTime");

        $closeTime = Carbon::createFromFormat('H:i', $storeCloseTime);
        $diffInMinutes = Carbon::now()->diffInMinutes($closeTime, false);
        Log::info("Minutes until closing: $diffInMinutes");

        return $diffInMinutes > 0 && $diffInMinutes <= 60;
    }

    // Check if the store is opening within the next hour
    private function isOpeningSoon($storeOpenTime)
    {
        Log::info("Checking if the store is opening soon. Open time: $storeOpenTime");

        $openTime = Carbon::createFromFormat('H:i', $storeOpenTime);
        $diffInMinutes = Carbon::now()->diffInMinutes($openTime, false);
        Log::info("Minutes until opening: $diffInMinutes");

        return $diffInMinutes > 0 && $diffInMinutes <= 60;
    }

    // Get the next opening time (for stores closed past today's hours)
    private function getNextOpeningTime($openingHours, $currentDay)
    {
        Log::info("Getting next opening time after $currentDay.");

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $currentIndex = array_search($currentDay, $daysOfWeek);

        for ($i = 1; $i <= 7; $i++) {
            $nextDay = $daysOfWeek[($currentIndex + $i) % 7];
            if (!empty($openingHours[$nextDay]['start'])) {
                Log::info("Next opening time found: $nextDay at " . $openingHours[$nextDay]['start']);
                return "$nextDay at " . $openingHours[$nextDay]['start'];
            }
        }

        Log::warning("No future opening time found.");
        return null; // If no future opening time is found
    }
}
