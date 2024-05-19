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
        $identityNumber = \FikriMastor\MyKad\Facades\MyKad::sanitize($value);

        if (! MyKad::characterIsValid($identityNumber)) {
            $fail('The :attribute is invalid character for mykad.');
        }

        if (! MyKad::lengthIsValid($identityNumber)) {
            $fail('The :attribute must be 12 characters.');
        }

        if (! MyKad::birthDateIsValid($identityNumber)) {
            $fail('The :attribute does not contains valid birth date.');
        }

        if (! MyKad::stateIsValid($identityNumber)) {
            $fail('The :attribute does not contains valid state.');
        }
    }
}
