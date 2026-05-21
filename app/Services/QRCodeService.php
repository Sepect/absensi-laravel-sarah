<?php

namespace App\Services;

use App\Models\OfficeSetting;
use Illuminate\Support\Facades\Cache;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Generate a new QR code payload
     *
     * @param string $type 'in' or 'out'
     * @return array
     */
    public function generatePayload(string $type = 'in'): array
    {
        $timestamp = now()->timestamp;
        $office = OfficeSetting::getActive();

        $payload = [
            'timestamp' => $timestamp,
            'type' => $type,
            'office_id' => $office->id,
        ];

        return [
            'data' => json_encode($payload),
            'timestamp' => $timestamp,
            'expires_at' => now()->addMinutes($office->qr_expiry_minutes)->timestamp,
            'type' => $type,
        ];
    }

    /**
     * Get the current active QR code data
     * Uses cache to ensure same QR for 5 minutes
     *
     * @param string $type 'in' or 'out'
     * @return array
     */
    public function getActiveQRCode(string $type = 'in'): array
    {
        $cacheKey = "qr_code_{$type}";
        $office = OfficeSetting::getActive();
        $ttl = ($office->qr_expiry_minutes ?? 5) * 60;

        if (!Cache::has($cacheKey)) {
            $payload = $this->generatePayload($type);
            Cache::put($cacheKey, $payload, $ttl);
            Cache::put("{$cacheKey}_generated_at", now()->timestamp, $ttl);
            return $payload;
        }

        return Cache::get($cacheKey);
    }

    /**
     * Validate QR code payload
     *
     * @param string $qrData JSON encoded QR data
     * @param string $qrTimestamp Timestamp from QR code
     * @return array ['valid' => bool, 'message' => string, 'payload' => array|null]
     */
    public function validatePayload(string $qrData, string $qrTimestamp): array
    {
        // Try to decode QR data
        $payload = json_decode($qrData, true);

        if (!$payload) {
            return [
                'valid' => false,
                'message' => 'QR Code tidak valid.',
                'payload' => null,
            ];
        }

        // Verify timestamp matches
        if (!isset($payload['timestamp']) || $payload['timestamp'] != $qrTimestamp) {
            return [
                'valid' => false,
                'message' => 'QR Code tidak valid. Timestamp mismatch.',
                'payload' => null,
            ];
        }

        // Check if QR is within valid time window
        $office = OfficeSetting::getActive();
        $timestamp = (int) $payload['timestamp'];
        $now = now()->timestamp;
        $expirySeconds = $office->qr_expiry_minutes * 60;

        if (($now - $timestamp) > $expirySeconds) {
            return [
                'valid' => false,
                'message' => 'QR Code sudah kedaluwarsa. Minta admin untuk memperbarui QR Code.',
                'payload' => null,
            ];
        }

        return [
            'valid' => true,
            'message' => 'QR Code valid.',
            'payload' => $payload,
        ];
    }

    /**
     * Generate QR code image as base64
     *
     * @param string $data Data to encode
     * @return string Base64 encoded PNG image
     */
    public function generateImage(string $data): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        $result = $builder->build();

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    /**
     * Get time remaining until QR code refreshes
     *
     * @param string $type 'in' or 'out'
     * @return int Seconds remaining
     */
    public function getTimeRemaining(string $type = 'in'): int
    {
        $cacheKey = "qr_code_{$type}";
        $cachedAt = Cache::get("{$cacheKey}_generated_at");

        if (!$cachedAt) {
            return 0;
        }

        $office = OfficeSetting::getActive();
        $ttl = ($office->qr_expiry_minutes ?? 5) * 60;

        $elapsed = now()->timestamp - $cachedAt;
        $remaining = $ttl - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Force refresh QR code (clear cache)
     *
     * @param string|null $type 'in', 'out', or null for both
     */
    public function forceRefresh(?string $type = null): void
    {
        if ($type) {
            Cache::forget("qr_code_{$type}");
            Cache::forget("qr_code_{$type}_generated_at");
        } else {
            Cache::forget('qr_code_in');
            Cache::forget('qr_code_out');
            Cache::forget('qr_code_in_generated_at');
            Cache::forget('qr_code_out_generated_at');
        }
    }
}