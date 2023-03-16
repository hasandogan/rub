<?php

namespace App\Service\Auth\Constraint;

use Symfony\Component\Validator\Constraints;

class AuthConstraint
{
    public static function loginRules(): Constraints\Collection
    {
        return new Constraints\Collection(
            [
                'email' => new Constraints\Email(['message' => 'Please fill in valid email']),
                'password' => new Constraints\NotBlank()
            ]
        );
    }

    public static function registerRules(): Constraints\Collection
    {
        return new Constraints\Collection(
            [
                'email' => new Constraints\Email(['message' => 'Please fill in valid email']),
                'password' => new Constraints\NotBlank(),
                'username' => new Constraints\NotBlank(),
                'firstname' => new Constraints\NotBlank(),
                'lastname' => new Constraints\NotBlank()
            ]
        );
    }
}