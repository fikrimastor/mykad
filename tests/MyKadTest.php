<?php

use FikriMastor\MyKad\Facades\MyKad;

const TEST_MYKAD = '010101-01-0101';
const TEST_MYKAD_INVALID = '01010-01-01';

it('can test mykad length is valid', function () {

    $mykad = MyKad::lengthIsValid(TEST_MYKAD);

    $mykad ? expect($mykad)->toBeTrue(TEST_MYKAD.' length is valid') : expect($mykad)->toBeFalse(TEST_MYKAD.' length is invalid');
});

it('can test mykad length is invalid', function () {

    $mykad = MyKad::lengthIsValid(TEST_MYKAD_INVALID);

    expect($mykad)->toBeFalse(TEST_MYKAD_INVALID.' length is invalid');
});

it('can test mykad output extraction is valid array', function () {

    $mykad = MyKad::extract(TEST_MYKAD);

    expect($mykad)->toBeArray(TEST_MYKAD.' input is valid');
});

it('can test mykad output extraction is invalid array', function () {

    $mykad = MyKad::extract(TEST_MYKAD_INVALID);

    expect($mykad)->toBeFalse(TEST_MYKAD_INVALID.' input is invalid');
});

it('can test mykad input birth date is valid', function () {

    $number = MyKad::sanitize(TEST_MYKAD);
    $mykad = MyKad::birthDateIsValid($number);

    expect($mykad)->toBeTrue($number.' input is valid');
});

it('can test mykad input birth date is invalid', function () {

    $number = MyKad::sanitize(TEST_MYKAD_INVALID);
    $mykad = MyKad::birthDateIsValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input character is valid', function () {

    $number = MyKad::sanitize(TEST_MYKAD);
    $mykad = MyKad::characterIsValid($number);

    expect($mykad)->toBeTrue($number.' input is valid');
});

it('can test mykad input character is invalid', function () {

    $number = '0909!';
    $mykad = MyKad::characterIsValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input state is valid', function () {

    $mykad = MyKad::stateIsValid(TEST_MYKAD);

    expect($mykad)->toBeTrue(TEST_MYKAD.' input is valid');
});

it('can test mykad input state is invalid', function () {

    $number = '0909!';
    $mykad = MyKad::stateIsValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input is invalid', function () {

    $number = '0909!';
    $mykad = MyKad::isValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input is valid', function () {

    $number = TEST_MYKAD;
    $mykad = MyKad::isValid($number);

    expect($mykad)->toBeTrue($number.' input is valid');
});
