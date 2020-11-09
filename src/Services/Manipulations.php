<?php

namespace App\Services;

class Manipulations
{
    public static function findIndex(array $bag, int $id)
    {
        foreach ($bag as $index => $b) {
            if ($b['product']->getId() === $id) {
                return $index;
            }
        }

        return null;
    }

    public static function fixAddress(array $input)
    {
        $street = explode(" ", $input['street']);
        foreach ($street as &$s) {
            $s = mb_strtolower($s);
            if (!in_array($s, ["ul", "ul.", "st", "st.", "i"])) {
                $s = ucfirst($s);
            }

        }

        $city = explode(" ", $input['city']);
        foreach ($city as &$c) {
            $c = mb_strtolower($c);
            if (!in_array($c, ["nad", "am", "la", "au", "of"])) {
                $c = ucfirst($c);
            }
        }

        return [
            'street' => implode(" ", $street),
            'city' => implode(" ", $city),
            'zip' => $input['zip'],
        ];
    }
}
