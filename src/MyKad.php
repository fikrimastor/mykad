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
     * Sanitize MyKad Details
     */
    public function sanitize(string $myKad): string
    {
        return $this->trimReplace($myKad);
    }

    /**
     * Check MyKad Length is Valid
     */
    public function lengthIsValid(string $myKad): bool
    {
        return $this->myKadLength($myKad) === 12;
    }

    /**
     * Check MyKad Character is Valid
     */
    public function characterIsValid(string $myKad): bool
    {
        return is_numeric($myKad);
    }

    /**
     * Check MyKad birth date is Valid
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
     */
    public function stateIsValid(string $myKad): bool
    {
        $extractedData = $this->extract($myKad);

        return is_array($extractedData) && Arr::exists($extractedData, 'state');
    }

    /**
     * Extract the details from the MyKad
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
     * Replace the unwanted characters
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
            // split the number into 2 sections
            $firstSection = str($code)->split(6);

            // the DOB section
            $dob = $firstSection[0];

            // now get the state code
            $secondSection = str($firstSection[1])->split(2);

            // assign it to the output
            $state = $secondSection[0];

            // then, from the last array item in $code, get
            // the last item to be use when checking for gender
            $code = $secondSection[1].$secondSection[2];

            return compact('dob', 'state', 'code');
        }

        return [];
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
