<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\QRValidationService;

class QRValidationServiceTest extends TestCase
{
    protected $qrService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrService = new QRValidationService();
    }

    /**
     * Test that valid tokens pass validation.
     */
    public function testValidTokensPass()
    {
        $result = $this->qrService->validateToken('valid_token_12345');
        $this->assertTrue($result['is_valid']);
        $this->assertEquals('QR code token is valid', $result['message']);

        // 3-character minimal valid token
        $resultMin = $this->qrService->validateToken('abc');
        $this->assertTrue($resultMin['is_valid']);
    }

    /**
     * Test that empty or non-string tokens fail validation.
     */
    public function testEmptyOrNonStringTokensFail()
    {
        $emptyResult = $this->qrService->validateToken('');
        $this->assertFalse($emptyResult['is_valid']);
        $this->assertEquals('Invalid QR code token', $emptyResult['message']);

        $nullResult = $this->qrService->validateToken(null);
        $this->assertFalse($nullResult['is_valid']);
        $this->assertEquals('Invalid QR code token', $nullResult['message']);

        $arrayResult = $this->qrService->validateToken([]);
        $this->assertFalse($arrayResult['is_valid']);
        $this->assertEquals('Invalid QR code token', $arrayResult['message']);
    }

    /**
     * Test that tokens with length < 3 fail validation.
     */
    public function testTooShortTokensFail()
    {
        $result1 = $this->qrService->validateToken('a');
        $this->assertFalse($result1['is_valid']);
        $this->assertEquals('QR code token is too short', $result1['message']);

        $result2 = $this->qrService->validateToken('ab');
        $this->assertFalse($result2['is_valid']);
        $this->assertEquals('QR code token is too short', $result2['message']);

        // Whitespace only is treated as too short after trim
        $whitespaceResult = $this->qrService->validateToken('  ');
        $this->assertFalse($whitespaceResult['is_valid']);
        $this->assertEquals('QR code token is too short', $whitespaceResult['message']);
    }

    /**
     * Test that exception inside try block is caught and formatted properly.
     */
    public function testExceptionHandling()
    {
        $result = $this->qrService->validateToken('__TRIGGER_EXCEPTION__');
        $this->assertFalse($result['is_valid']);
        $this->assertStringContainsString('Error validating QR code: Malformed QR encoding', $result['message']);
    }
}

namespace App\Services;

if (!function_exists('App\Services\trim')) {
    function trim($str) {
        if ($str === '__TRIGGER_EXCEPTION__') {
            throw new \Exception('Malformed QR encoding');
        }
        return \trim($str);
    }
}
