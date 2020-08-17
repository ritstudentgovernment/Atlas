<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OnCampus implements Rule
{
    protected $maxLat;
    protected $minLat;
    protected $maxLng;
    protected $minLng;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $centerLat = env('GOOGLE_MAPS_CENTER_LAT');
        $centerLng = env('GOOGLE_MAPS_CENTER_LNG');
        $latChange = env('GOOGLE_MAPS_LAT_CHANGE');
        $lngChange = env('GOOGLE_MAPS_LNG_CHANGE');

        $this->maxLat = $centerLat + $latChange;
        $this->minLat = $centerLat - $latChange;
        $this->maxLng = $centerLng + $lngChange;
        $this->minLng = $centerLng - $lngChange;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $latLngMax = 'max' . ucfirst($attribute);
        $latLngMin = 'min' . ucfirst($attribute);

        return $value > $this->$latLngMin && $value < $this->$latLngMax;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The spot must be on campus.';
    }
}
