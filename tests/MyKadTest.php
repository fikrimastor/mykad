<?php

use FikriMastor\MyKad\Facades\MyKad;

const TEST_MYKAD = '010101-01-0101';

it('can test mykad length is valid', function () {

    $mykad = MyKad::lengthIsValid(TEST_MYKAD);

    expect($mykad)->toBeTrue(TEST_MYKAD.' length is valid');
});

it('can test mykad length is invalid', function () {

    $number = str()->random(13);
    $mykad = MyKad::lengthIsValid($number);

    expect($mykad)->toBeFalse($number.' length is invalid');
});

it('can test mykad output extraction is valid array', function () {

    $mykad = MyKad::extractMyKad(TEST_MYKAD);

    expect($mykad)->toBeArray(TEST_MYKAD.' input is valid');
});

it('can test mykad output extraction is invalid array', function () {

    $number = str()->random(13);
    $mykad = MyKad::extractMyKad($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input birth date is valid', function () {

    $number = MyKad::sanitize(TEST_MYKAD);
    $mykad = MyKad::birthDateIsValid($number);

    expect($mykad)->toBeTrue($number.' input is valid');
});

it('can test mykad input birth date is invalid', function () {

    $number = MyKad::sanitize(str()->random(13));
    $mykad = MyKad::birthDateIsValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input character is valid', function () {

    $number = MyKad::sanitize(TEST_MYKAD);
    $mykad = MyKad::characterIsValid($number);

    expect($mykad)->toBeTrue($number.' input is valid');
});

it('can test mykad input character is invalid', function () {

    $number = MyKad::sanitize(str()->random(13));
    $mykad = MyKad::characterIsValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});

it('can test mykad input state is valid', function () {

    $number = MyKad::sanitize(TEST_MYKAD);
    $mykad = MyKad::stateIsValid($number);

    expect($mykad)->toBeTrue($number.' input is valid');
});

it('can test mykad input state is invalid', function () {

    $number = MyKad::sanitize(str()->random(13));
    $mykad = MyKad::stateIsValid($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});
