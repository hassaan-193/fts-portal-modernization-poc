<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;


class QRValidationService
{
    /**
     * Validate a QR code token
     * 
     * Since QR codes are generated online and passed from frontend,
     * we simply validate that the token is not empty.
     * 
     * @param string $token - The QR code token to validate
     * 
     * @return array [
     *     'is_valid' => bool,
     *     'message' => string
     * ]
     */
    public function validateToken($token)
    {
        try {
            // Simple validation: token must not be empty and must be a string
            if (empty($token) || !is_string($token)) {
                return [
                    'is_valid' => false,
                    'message' => 'Invalid QR code token',
                ];
            }

            // Token must be at least 3 characters (basic validation)
            if (strlen(trim($token)) < 3) {
                return [
                    'is_valid' => false,
                    'message' => 'QR code token is too short',
                ];
            }

            return [
                'is_valid' => true,
                'message' => 'QR code token is valid',
            ];

        } catch (\Exception $e) {
            Log::error('QRValidationService@validateToken error: ' . $e->getMessage());
            return [
                'is_valid' => false,
                'message' => 'Error validating QR code: ' . $e->getMessage(),
            ];
        }
    }
}
