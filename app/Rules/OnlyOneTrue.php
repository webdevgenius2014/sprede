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
        if($value == 1){
            $this->propertyToCheck++;
        }
        // echo $this->propertyToCheck;
        if($this->propertyToCheck > 1 ){
            $fail('You can work in one organisation at a time.');
        }
    }
}
