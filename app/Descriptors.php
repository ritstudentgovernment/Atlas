<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Descriptors extends Model
{
    protected $fillable = ['name', 'value_type', 'default_value', 'allowed_values', 'icon'];

    public function spots()
    {
        return $this->belongsToMany(Spot::class, 'descriptors_spot', 'descriptor_id', 'spot_id')->withPivot('value');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_descriptors', 'descriptor_id', 'category_id');
    }

    /**
     * This is the validator for the Select value type.
     */
    private function validateSelectType($value): bool
    {
        $allowedValues = collect(explode('|', $this->allowed_values));

        return $allowedValues->contains($value);
    }

    /**
     * This is the validator for the Multi-Select value type.
     */
    private function validateMultiSelectType($values): bool
    {
        $isValid = true;
        $values = explode(', ', $values);
        foreach ($values as $selectedValue) {
            if (!$this->validateSelectType(trim($selectedValue))) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * This is the validator for the Number value type.
     */
    private function validateNumberType($value): bool
    {
        $range = explode('-', $this->allowed_values);
        $min = trim($range[0]);
        $max = trim($range[1]);

        if ($max == 0) {
            return true;
        }

        return $value >= $min && $value <= $max;
    }

    /**
     * Proxy method that passes a validation request to the descriptors specified
     * validator.
     */
    public function validate($value): bool
    {
        $validator = 'validate'.ucfirst($this->value_type).'Type';
        if (method_exists($this, $validator)) {
            return $this->$validator($value);
        }

        Log::error("The validator for the '$this->value_type' value type does not exist.");

        return false;
    }
}
