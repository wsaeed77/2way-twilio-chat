<?php

namespace Chattermax\Config;

class Helper {
    public static function formatPhoneNumber($phone) {
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Check if the phone number starts with a "1" and ensure it has the correct length
        if (strlen($phone) == 10) {
            $phone = '1' . $phone;
        } elseif (strlen($phone) != 11) {
            throw new \InvalidArgumentException("Invalid phone number length: " . strlen($phone));
        }

        return $phone;
    }
}
