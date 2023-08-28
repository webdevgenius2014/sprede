<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnlyOneTrue implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $propertyToCheck = 0;

    // public function __construct($propertyToCheck)
    // {
    //     $this->propertyToCheck = $propertyToCheck;
    //     dd($propertyToCheck);
    // }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $get_requested_validate_from = explode(".", $attribute, 2);
        // dd($get_requested_validate_from);
        if($value == 1){
            $this->propertyToCheck++;
        }

        if($get_requested_validate_from[0] == 'education'){
            if($this->propertyToCheck > 1 ){
                $fail('You can\'t pursue more than one education at a time.');
            }
        }
        // echo $this->propertyToCheck;
        if($get_requested_validate_from[0] == 'organisation'){
            if($this->propertyToCheck > 1 ){
                $fail('You can\'t work in more than one organisation at a time.');
            }
        }   
    }
}
