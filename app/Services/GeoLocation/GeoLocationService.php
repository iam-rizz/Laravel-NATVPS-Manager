<?php

namespace App\Services\GeoLocation;

use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Get location data for a server's IP address.
     * Uses ip-api.com (free, no API key required, 45 requests/minute limit)
     */
    public function getLocationForServer(Server $server, bool $forceRefresh = false): ?array
    {
        // Return cached data if available and not forcing refresh
        if (!$forceRefresh && $server->location_data && $server->location_cached_at) {
            // Cache for 30 days
            if ($server->location_cached_at->diffInDays(now()) < 30) {
                return $server->location_data;
            }
        }

        $locationData = $this->lookupIp($server->ip_address);

        if ($locationData) {
            $server->update([
                'location_data' => $locationData,
                'location_cached_at' => now(),
            ]);
        }

        return $locationData;
    }

    /**
     * Lookup IP address using ip-api.com
     */
    protected function lookupIp(string $ip): ?array
    {
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,message,country,countryCode,region,regionName,city,lat,lon,isp,org',
            ]);

            if (!$response->successful()) {
                Log::warning('GeoLocation API request failed', ['ip' => $ip, 'status' => $response->status()]);
                return null;
            }

            $data = $response->json();

            if (($data['status'] ?? '') !== 'success') {
                Log::warning('GeoLocation lookup failed', ['ip' => $ip, 'message' => $data['message'] ?? 'Unknown error']);
                return null;
            }

            return [
                'city' => $data['city'] ?? null,
                'region' => $data['regionName'] ?? null,
                'country' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
                'latitude' => (string) ($data['lat'] ?? ''),
                'longitude' => (string) ($data['lon'] ?? ''),
                'isp' => $data['isp'] ?? null,
                'org' => $data['org'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('GeoLocation lookup error', ['ip' => $ip, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get location string for display.
     */
    public function getLocationString(?array $locationData): ?string
    {
        if (!$locationData) {
            return null;
        }

        $parts = array_filter([
            $locationData['city'] ?? null,
            $locationData['country_code'] ?? null,
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }
}
