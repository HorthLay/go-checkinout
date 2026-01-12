<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius',
        'is_active',
        'description',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all active office locations
     */
    public static function getActiveLocations()
    {
        return self::where('is_active', true)->get();
    }

    /**
     * Get the default/main office location
     */
    public static function getDefaultLocation()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    public function calculateDistance($userLat, $userLng)
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($userLat);
        $lonFrom = deg2rad($userLng);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius);
    }

    /**
     * Check if user location is within allowed radius
     */
    public function isWithinRadius($userLat, $userLng)
    {
        $distance = $this->calculateDistance($userLat, $userLng);
        return $distance <= $this->radius;
    }

    /**
     * Get attendances for this office location
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}