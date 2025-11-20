<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        // Mínimo 8 caracteres
        if (strlen($value) < 8) {
            return false;
        }

        // Deve conter pelo menos uma letra maiúscula
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Deve conter pelo menos uma letra minúscula
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Deve conter pelo menos um número
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        // Deve conter pelo menos um caractere especial
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'A senha deve ter no mínimo 8 caracteres, incluindo letras maiúsculas, minúsculas, números e caracteres especiais.';
    }
}

