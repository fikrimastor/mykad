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
        $identityNumber = MyKad::sanitize($value);

        if (! MyKad::characterIsValid($identityNumber)) {
            $fail('mykad::messages.invalid_character')->translate(compact('attribute', 'value'));
        }

        if (! MyKad::lengthIsValid($identityNumber)) {
            $fail('mykad::messages.invalid_length')->translate(compact('attribute', 'value'));
        }

        if (! MyKad::birthDateIsValid($identityNumber)) {
            $fail('mykad::messages.invalid_birth_date')->translate(compact('attribute', 'value'));
        }

        if (! MyKad::stateIsValid($identityNumber)) {
            $fail('mykad::messages.invalid_state')->translate(compact('attribute', 'value'));
        }
    }
}
