<?php

namespace FikriMastor\MyKad;

use Illuminate\Support\Arr;

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
     * Extract MyKad Number
     */
    private function trimReplace(string $myKad): string
    {
        return str(trim($myKad))->replaceMatches('/[^a-zA-Z0-9]/', '');
    }

    /**
     * Sanitize MyKad Details
     */
    public function sanitize(string $myKad): string
    {
        return $this->trimReplace($myKad);
    }

    /**
     * Get MyKad Length
     */
    private function myKadLength(string $myKad): int
    {
        return strlen($this->sanitize($myKad));
    }

    /**
     * Check MyKad Length is Valid
     */
    public function lengthIsValid(string $myKad): bool
    {
        $number = $this->sanitize($myKad);

        return $this->myKadLength($number) === 12;
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
     * Check MyKad birth date is Valid
     */
    public function stateIsValid(string $myKad): bool
    {
        $extractedData = $this->extractMyKad($myKad);

        return is_array($extractedData) && Arr::exists($extractedData, 'state');
    }

    /**
     * Get the date of birth from IC number
     */
    public function extractMyKad(string $myKad, ?string $dateFormat = 'j F Y'): array|bool
    {
        // sanitize characters
        $myKadNo = $this->sanitize($myKad);

        if (! empty($myKadNo)) {
            // if the numbers is less than 12 digits
            if (! $this->lengthIsValid($myKadNo)) {
                return false;
            }

            return $this->getData($myKadNo, $dateFormat);
        }

        return false;
    }

    /**
     * Process the IC number
     *
     * @param  string  $myKad  The raw IC number
     * @param  string  $dateFormat  The date format to use
     * @return array The detail
     */
    private function getData(string $myKad, string $dateFormat): array
    {
        // send it to function to split it
        $sections = $this->split($myKad);

        // get the DOB
        $this->getDob($sections['dob']);

        // get the gender
        $this->getGender($sections['code']);

        return [
            'date_of_birth' => $this->dobHumanReadable($dateFormat), // get the date of birth
            'state' => $this->getStateByCode($sections['state']), // get the state
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
        if (! empty($code)) {
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
     *
     * @param  string|null  $code  The 2 digits state code
     * @return string The state name
     */
    private function getState(?string $code = null): string
    {
        return match ($code) {
            '01', '21', '22', '23', '24' => 'Johor',
            '02', '25', '26', '27' => 'Kedah',
            '03', '28', '29' => 'Kelantan',
            '04', '30' => 'Melaka',
            '05', '31', '59' => 'Negeri Sembilan',
            '06', '32', '33' => 'Pahang',
            '07', '34', '35' => 'Penang',
            '08', '36', '37', '38', '39' => 'Perak',
            '09', '40' => 'Perlis',
            '10', '41', '42', '43', '44' => 'Selangor',
            '11', '45', '46' => 'Terengganu',
            '12', '47', '48', '49' => 'Sabah',
            '13', '50', '51', '52', '53' => 'Sarawak',
            '14', '54', '55', '56', '57' => 'Wilayah Persekutuan Kuala Lumpur',
            '15', '58' => 'Wilayah Persekutuan Labuan',
            '16' => 'Wilayah Persekutuan Putrajaya',
            default => 'Others',
        };
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
        $number = (int) $code;

        // check if the code is an integer
        if (! empty($code) && is_int($number)) {
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
