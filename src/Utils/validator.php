<?php

namespace App\Utils;

use DateTime;
use Exception;

class Validator
{
    public static function validate(array $fields)
    {
        $errors = [];

        foreach ($fields as $field => $value) {
            if (is_string($value) && trim($value) === "") {
                $errors[] = $field;
            }
        }

        if (!empty($errors)) {

            $qtdErrors = count($errors);

            if ($qtdErrors > 1) {
                $message = "Os campos [" . implode(", ", $errors) . "] são obrigatórios";
            } else {
                $message = "O campo [" . implode(", ", $errors) . "] é obrigatório";
            }

            throw new Exception($message);
        }


        return $fields;
    }

    public static function validateEmail(string $email): string
    {
        $pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        $isValid = preg_match($pattern, $email);

        if (!$isValid) {
            throw new Exception("Email Inválido");
        }

        return $email;
    }

    public static function validatePhone(array $phone): array
    {
        $errors = [];

        foreach ($phone as $field => $value) {
            if (empty(trim($value))) {
                $errors[] = $field;
            }
        }

        if (!empty($errors)) {
            $qtdErrors = count($errors);

            if ($qtdErrors > 1) {
                $message = "Os campos [" . implode(", ", $errors) . "] são obrigatórios";
            } else {
                $message = "O campo [" . implode(", ", $errors) . "] é obrigatório";
            }

            throw new Exception($message);
        }

        switch ($phone['type']) {
            case "WHATSAPP":
            case "CELLPHONE":
                if (!preg_match('/^\d{11}$/', $phone['number'])) {
                    throw new Exception("Número de celular inválido. Deve conter 11 dígitos.");
                }
                break;

            case "PHONE":
                if (!preg_match('/^\d{10}$/', $phone['number'])) {
                    throw new Exception("Número de telefone fixo inválido. Deve conter 10 dígitos.");
                }
                break;
            case "BUSINESS":
                if (!preg_match('/^\d{10,11}$/', $phone['number'])) {
                    throw new Exception("Número de telefone comercial inválido. Deve conter 10 ou 11 dígitos.");
                }
                break;

            default:
                throw new Exception("Tipo de telefone inválido");
        }

        return $phone;
    }


    public static function validateNaturalPerson(array $fields)
    {
        $errors = [];

        foreach ($fields as $field => $value) {
            if (empty(trim($value))) {
                $errors[] = $field;
            }
        }

        if (!empty($errors)) {
            $qtdErrors = count($errors);

            if ($qtdErrors > 1) {
                $message = "Os campos [" . implode(", ", $errors) . "] são obrigatórios";
            } else {
                $message = "O campo [" . implode(", ", $errors) . "] é obrigatório";
            }

            throw new Exception($message);
        }

        self::validateCPF($fields['cpf']);
        self::validateBirthDate($fields['dt_birth']);

        return $fields;
    }

    public static function validateBirthDate(string $birthDate): string
    {
        $date = DateTime::createFromFormat('Y-m-d', $birthDate);

        if (!$date || $date->format('Y-m-d') !== $birthDate) {
            throw new Exception("A data de nascimento informada é inválida (use o formato YYYY-MM-DD).");
        }

        $today = new DateTime();
        if ($date > $today) {
            throw new Exception("A data de nascimento não pode ser no futuro.");
        }

        $minAgeDate = $today->modify('-16 years');
        if ($date > $minAgeDate) {
            throw new Exception("É necessário ter pelo menos 16 anos.");
        }

        return $birthDate;
    }


    public static function validateCPF(string $cpf): string
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11) {
            throw new Exception("O CPF tem um tamanho inválido.");
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            throw new Exception("O CPF informado é inválido (sequência repetida).");
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $firstVerifier = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cpf[9] != $firstVerifier) {
            throw new Exception("O CPF informado é inválido (primeiro dígito verificador incorreto).");
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $secondVerifier = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cpf[10] != $secondVerifier) {
            throw new Exception("O CPF informado é inválido (segundo dígito verificador incorreto).");
        }

        return $cpf;
    }

    public static function validateAddress(array $fields)
    {
        $errors = [];

        foreach ($fields as $field => $value) {
            if (empty(trim($value))) {
                $errors[] = $field;
            }
        }

        if (!empty($fields['zip_code']) && !preg_match('/^\d{8}$/', $fields['zip_code'])) {
            $errors[] = 'zip_code';
        }

        if (!empty($fields['state']) && !preg_match('/^[A-Za-z]{2}$/', $fields['state'])) {
            $errors[] = 'state';
        }

        if (!empty($fields['public_area']) && preg_match('/^\d+$/', $fields['public_area'])) {
            $errors[] = 'public_area';
        }

        if (!empty($errors)) {
            $qtdErrors = count($errors);

            if ($qtdErrors > 1) {
                $message = "Os campos [" . implode(", ", $errors) . "] são obrigatórios ou inválidos";
            } else {
                $message = "O campo [" . implode(", ", $errors) . "] é obrigatório ou inválido";
            }

            throw new Exception($message);
        }

        return $fields;
    }


    public static function validateLegalPerson(array $fields)
    {
        $errors = [];

        foreach ($fields as $field => $value) {
            if (empty(trim($value))) {
                $errors[] = $field;
            }
        }

        if (!empty($errors)) {

            $qtdErrors = count($errors);

            if ($qtdErrors > 1) {
                $message = "Os campos [" . implode(", ", $errors) . "] são obrigatórios";
            } else {
                $message = "O campo [" . implode(", ", $errors) . "] é obrigatório";
            }

            throw new Exception($message);
        }

        self::validateCNPJ($fields['cnpj']);

        $corporate_name = self::validateName($fields['corporate_name'], 100);

        if (!$corporate_name) {
            throw new Exception("A razão social deve ser válida.");
        }

        $trade_name = self::validateName($fields['trade_name'], 100);

        if (!$trade_name) {
            throw new Exception("Nome fantasia deve ser válido.");
        }

        $state_registration = self::validateName($fields['state_registration'], 20);

        if (!$state_registration) {
            throw new Exception("A inscrição estadual deve ser válida.");
        }

        return $fields;
    }

    public static function validateCNPJ(string $cnpj): string
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            throw new Exception("O CNPJ tem um tamanho inválido");
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            throw new Exception("O CNPJ informado é inválido (sequência repetida).");
        }

        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $firstVerifier = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cnpj[12] != $firstVerifier) {
            throw new Exception("O CNPJ informado é inválido (primeiro digito verificador incorreto).");
        }

        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $secondVerifier = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cnpj[13] != $secondVerifier) {
            throw new Exception("O CNPJ informado é inválido (segundo digito verificador incorreto).");
        }

        return $cnpj;
    }

    private static function validateName(string $name, int $max)
    {
        if (!is_string($name)) {
            return false;
        }

        if (strlen($name) > $max) {
            return false;
        }

        return true;
    }
}
