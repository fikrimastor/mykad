<?php

use FikriMastor\MyKad\Facades\MyKad;
use FikriMastor\MyKad\Rules\IsMyKad;
use Illuminate\Support\Facades\Validator;

it('can test mykad input invalid birth date validation message in Malay', function () {
    $number = '010132-01-0101';

    $validator = Validator::make(
        ['mykad' => $number],
        ['mykad' => new IsMyKad],
        ['mykad' => ':attribute tidak mengandungi tarikh lahir yang sah.']
    );

    expect($validator->fails())->toBeTrue($number.' input is invalid');
    expect($validator->messages()->first())->toEqual('mykad tidak mengandungi tarikh lahir yang sah.');
});

it('can test mykad input invalid input character validation message in Malay', function () {
    $number = '010132B01A0101';

    $validator = Validator::make(
        ['mykad' => $number],
        ['mykad' => new IsMyKad],
        ['mykad' => ':attribute mengandungi aksara tidak sah untuk MyKad.']
    );

    expect($validator->fails())->toBeTrue($number.' input is invalid');
    expect($validator->messages()->first())->toEqual('mykad mengandungi aksara tidak sah untuk MyKad.');
});

it('can test mykad input invalid input length validation message in Malay', function () {
    $number = '010132B01A0101';

    $validator = Validator::make(
        ['mykad' => $number],
        ['mykad' => new IsMyKad],
        ['mykad' => ':attribute mesti mempunyai 12 aksara.']
    );

    expect($validator->fails())->toBeTrue($number.' input is invalid');
    expect($validator->messages()->first())->toEqual('mykad mesti mempunyai 12 aksara.');
});
