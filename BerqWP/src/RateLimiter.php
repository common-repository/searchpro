<?php

namespace BerqWP;

class RateLimiter
{
    private $limit;
    private $timeWindow; // In seconds
    private $storagePath;

    public function __construct($limit, $timeWindow, $storagePath)
    {
        $this->limit = $limit;
        $this->timeWindow = $timeWindow; // e.g. 60 seconds for 1 minute
        $this->storagePath = rtrim($storagePath, '/') . '/';
    }

    public function isRateLimited($clientIdentifier)
    {
        $filePath = $this->storagePath . md5($clientIdentifier) . '.json';

        if (!file_exists($filePath)) {
            // Create a new entry if it doesn't exist
            $this->createLog($filePath);
            return false; // Not rate limited
        }

        // Read the log file
        $logData = json_decode(file_get_contents($filePath), true);
        $currentTime = time();

        // Remove old entries from the time window
        $logData = array_filter($logData, function($timestamp) use ($currentTime) {
            return ($currentTime - $timestamp) <= $this->timeWindow;
        });

        // Check if the limit is reached
        if (count($logData) >= $this->limit) {
            return true; // Rate limited
        }

        // Otherwise, add the new timestamp and update the log
        $logData[] = $currentTime;
        file_put_contents($filePath, json_encode($logData));
        return false;
    }

    private function createLog($filePath)
    {
        $logData = [time()];
        file_put_contents($filePath, json_encode($logData));
    }
}
