<?php

namespace FikriMastor\MyKad;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MyKad
{
    /**
     * Gender
     */
    protected ?string $gender = null;

    /**
     *  Date of Birth
     */
    protected int|bool $dob = false;

    /**
     * State Code
     *
     * @var string|null
     */
    private ?string $stateCode = null;

    /**
     * Date of Birth Code
     *
     * @var string|null
     */
    private ?string $dateOfBirthCode = null;

    /**
     * Gender Code
     *
     * @var string|null
     */
    private ?string $genderCode = null;

    /**
     * Sanitize MyKad Details
     *
     * @param  string  $myKad
     * @return string
     */
    public function sanitize(string $myKad): string
    {
        return $this->trimReplace($myKad);
    }

    /**
     * Check MyKad Length is Valid
     *
     * @param  string  $myKad
     * @return bool
     */
    public function lengthIsValid(string $myKad): bool
    {
        return $this->myKadLength($myKad) === 12;
    }

    /**
     * Check MyKad Character is Valid
     *
     * @param  string  $myKad
     * @return bool
     */
    public function characterIsValid(string $myKad): bool
    {
        return is_numeric($myKad);
    }

    /**
     * Check MyKad birth date is Valid
     *
     * @param  string  $myKad
     * @return bool
     */
    public function birthDateIsValid(string $myKad): bool
    {
        $extractedData = $this->split($myKad);
        if (Arr::exists($extractedData, 'dob')) {
            $dob = $this->getDob($extractedData['dob']);

            // if the date is not empty, then check if it is valid date
            return $this->dob && checkdate($dob['month'], $dob['day'], $dob['year']);
        }

        return false;
    }

    /**
     * Check MyKad state is Valid
     *
     * @param  string  $myKad
     * @return bool
     */
    public function stateIsValid(string $myKad): bool
    {
        $extractedData = $this->extract($myKad);

        return is_array($extractedData) && Arr::exists($extractedData, 'state');
    }

    /**
     * Extract the details from the MyKad
     *
     * @param  string  $myKad
     * @param  string|null  $dateFormat
     * @return array|bool
     */
    public function extract(string $myKad, ?string $dateFormat = 'j F Y'): array|bool
    {
        // sanitize characters
        $myKadNo = $this->sanitize($myKad);

        // if the numbers is 12 digits
        return ! empty($myKadNo) && $this->lengthIsValid($myKadNo)
            ? $this->getData($myKadNo, $dateFormat)
            : false;
    }

    /**
     * Format MyKad with dashes
     *
     * @param  string  $myKad
     * @return string
     */
    public function format(string $myKad): string
    {
        return $this->formatting($myKad);
    }

    /**
     * Format MyKad with dashes
     *
     * @param  string  $myKad
     * @return string
     */
    public function formatWithoutDash(string $myKad): string
    {
        return $this->sanitize($this->formatting($myKad));
    }

    /**
     * Check if the MyKad is valid
     *
     * @param  string  $myKad
     * @return bool
     */
    public function isValid(string $myKad): bool
    {
        $identityNumber = $this->sanitize($myKad);

        return match (true) {
            ! $this->lengthIsValid($identityNumber),
            ! $this->characterIsValid($identityNumber),
            ! $this->birthDateIsValid($identityNumber),
            ! $this->stateIsValid($identityNumber) => false,
            default => true,
        };
    }

    /**
     * Replace the unwanted characters
     *
     * @param  string  $myKad
     * @return string
     */
    private function trimReplace(string $myKad): string
    {
        return Str::replaceMatches('/[^a-zA-Z0-9]/', '', trim($myKad));
    }

    /**
     * Process the IC number to get the details
     *
     * @param  string  $myKad  The raw IC number
     * @param  string  $dateFormat  The date format to use
     * @return array|bool The detail
     */
    private function getData(string $myKad, string $dateFormat): array|bool
    {
        // send it to function to split it
        $extractedData = $this->split($myKad);

        return Arr::exists($extractedData, 'dob')
            ? $this->processMyKad($extractedData, $dateFormat)
            : false;
    }

    /**
     * Get MyKad Length
     *
     * @param  string  $myKad
     * @return int
     */
    private function myKadLength(string $myKad): int
    {
        return strlen($this->sanitize($myKad));
    }

    /**
     * Process the MyKad
     *
     * @param  array  $extractedCode  The extracted code
     * @param  string  $dateFormat  The date format
     * @return array The details
     */
    private function processMyKad(array $extractedCode, string $dateFormat): array
    {
        // get the DOB
        $this->getDob($extractedCode['dob']);

        // get the gender
        $this->getGender($extractedCode['code']);

        return [
            'date_of_birth' => $this->dobHumanReadable($dateFormat), // get the date of birth
            'state' => $this->getStateByCode($extractedCode['state']), // get the state
            'gender' => $this->gender, // get the gender
        ];
    }

    /**
     * Get the date of birth in human readable format
     *
     * @param  string|null  $dateFormat  The date format
     * @return string|bool The date of birth
     */
    private function dobHumanReadable(?string $dateFormat = 'j F Y'): string|bool
    {
        return $this->dob
            ? date($dateFormat, (int) $this->dob)
            : $this->dob;
    }

    /**
     * Splitting the code given to the proper sections
     *
     * @param  string|null  $code  The IC number
     * @return array The sections
     */
    private function split(?string $code = null): array
    {
        if (! empty($code) && $this->lengthIsValid($code)) {
            // formatting MyKad
            $this->formatting($code);

            // assign dob
            $dob = $this->dateOfBirthCode;

            // assign state
            $state = $this->stateCode;

            // assign gender code
            $code = $this->genderCode;

            return compact('dob', 'state', 'code');
        }

        return [];
    }

    /**
     * Formatting the MyKad
     *
     * @param  string  $myKad  The IC number
     * @return string The formatted IC number
     */
    private function formatting(string $myKad): string
    {
        $formattedMyKad = $myKad;

        if ($this->lengthIsValid($myKad)) {
            $ic = $this->sanitize($myKad);

            // split the number into 2 sections
            $firstSection = Str::of($ic)->split(6);

            // the DOB section
            $this->dateOfBirthCode = $firstSection[0];

            // now get the state code
            $secondSection = Str::of($firstSection[1])->split(2);

            // assign it to the output
            $this->stateCode = $secondSection[0];

            // then, from the last array item in $code, get
            // the last item to be use when checking for gender
            $this->genderCode = $secondSection[1].$secondSection[2];

            $formattedMyKad = $this->dateOfBirthCode . '-' . $this->stateCode . '-' . $this->genderCode;
        }

        return $formattedMyKad;
    }

    /**
     * Get state based on the 2 digits code
     * Source: https://www.jpn.gov.my/my/kod-negeri
     *
     * @param  string  $code  The 2 digits state code
     * @return string The state name
     */
    private function getStateByCode(string $code): string
    {
        $states = config('mykad.states-code');

        return Arr::exists($states, $code)
            ? $states[$code]
            : 'Others';
    }

    /**
     * Get gender based on the last 4 digits code
     *
     * @param  string|null  $code  The 4 digits IC number
     */
    private function getGender(?string $code = null)
    {
        if (! empty($number = (int) $code)) {
            // basically, the last digit will determine the
            // gender; odd for Male and even for Female
            $this->gender = $number % 2 === 0 ? 'Female' : 'Male';
        }
    }

    /**
     * Get date of birth from the first 6 digits
     *
     * @param  string|null  $code  The first 6 digits IC number
     */
    private function getDob(?string $code = null): array
    {
        if (! empty($code)) {
            // split it into 3 section, 2 digits per section
            $dob = str($code)->split(2);

            // get the day
            $day = $dob[2];

            // get the month
            $month = $dob[1];

            // get the integer value for the year
            $year = (int) $dob[0];

            // we need to add 1900 to the year
            if ($year >= 50) {
                $year += 1900;
            } else {
                $year += 2000;
            }

            // now convert it into the string and assign it to
            // our variable
            $this->dob = strtotime($year.'-'.$month.'-'.$day);

            return compact('day', 'month', 'year');
        }

        return [];
    }
}
