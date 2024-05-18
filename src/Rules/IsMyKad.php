<?php

namespace FikriMastor\MyKad\Rules;

use Closure;
use FikriMastor\MyKad\Facades\MyKad;
use Illuminate\Contracts\Validation\ValidationRule;

class IsMyKad implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (MyKad::myKadLengthIsValid($value)) {
            $fail('The :attribute must be 12 characters.');
        }
    }
}
