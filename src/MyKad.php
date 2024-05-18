<?php

namespace FikriMastor\MyKad;

class MyKad
{
    /**
     * Gender
     *
     * @var string|null $gender
     */
    protected ?string $gender = null;

    /**
     *  Date of Birth
     *
     * @var int|bool
     */
    protected int|bool $dob = false;

    /**
     * Extract MyKad Number
     *
     * @param  string  $myKad
     * @return string
     */
    private function extractMyKad(string $myKad): string
    {
        return str($myKad)->replaceMatches('/[^0-9]/', '');
    }

    /**
     * Get MyKad Length
     *
     * @param  string  $myKad
     * @return int
     */
    private function myKadLength(string $myKad): int
    {
        return strlen($myKad);
    }

    /**
     * Check MyKad Length is Valid
     *
     * @param  string  $myKad
     * @return bool
     */
    public function myKadLengthIsValid(string $myKad): bool
    {
        return $this->myKadLength($myKad) === 12;
    }

    /**
     * Process the IC number
     *
     * @access    private
     * @param  string  $myKad  The raw IC number
     * @param  string  $dateFormat  The date format to use
     * @return    array    The detail
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
            'state' => $this->getState($sections['state']), // get the state
            'gender' => $this->gender // get the gender
        ];
    }

    /**
     * Get the date of birth in human readable format
     *
     * @access    private
     * @param  string|null  $dateFormat  The date format
     * @return    string|bool    The date of birth
     */
    private function dobHumanReadable(string|null $dateFormat = 'j F Y'): string|bool
    {
        return $this->dob ? date($dateFormat, (int) $this->dob) : $this->dob;
    }

    /**
     * Splitting the code given to the proper sections
     *
     * @access    private
     * @param  string|null  $code  The IC number
     * @return    array    The sections
     */
    private function split(string|null $code = null): array
    {
        $output = [];

        if (!empty($code)) {
            // the output array
            $output = array();

            // split the number into 2 sections
            $sect = str($code)->split(6);

            // the DOB section
            $output['dob'] = $sect[0];

            // now get the state code
            $other = str($sect[1])->split(2);

            // assign it to the output
            $output['state'] = $other[0];

            // then, from the last array item in $code, get
            // the last item to be use when checking for gender
            $output['code'] = $other[1] . $other[2];
        }

        return $output;
    }


    /**
     * Get state based on the 2 digits code
     *
     * @access    private
     * @param  string|null  $code  The 2 digits state code
     * @return    string    The state name
     */
    private function getState(string|null $code = null): string
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
     * Get gender based on the last 4 digits code
     *
     * @access    private
     * @param  string|null  $code  The 4 digits IC number
     */
    private function getGender(string|null $code = null)
    {
        if (!empty($code)) {
            // basically, the last digit will determine the
            // gender; odd for Male and even for Female
            $this->gender = $code % 2 === 0 ? 'Female' : 'Male';
        }
    }


    /**
     * Get date of birth from the first 6 digits
     *
     * @access    private
     * @param  string|null  $code  The first 6 digits IC number
     */
    private function getDob(string|null $code = null): void
    {
        if (!empty($code)) {
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
            $this->dob = strtotime($year . '-' . $month . '-' . $day);
        }
    }
}
