<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ITMSHelper
{
    /**
     * Generate employee ID.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateEmployeeId(string $prefix = 'EMP'): string
    {
        $year = date('Y');
        $month = date('m');
        $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . $randomNumber;
    }

    /**
     * Generate asset ID.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateAssetId(string $prefix = 'AST'): string
    {
        $year = date('Y');
        $randomNumber = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $randomNumber;
    }

    /**
     * Generate incident ID.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateIncidentId(string $prefix = 'INC'): string
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . $day . $randomNumber;
    }

    /**
     * Generate service request ID.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateServiceRequestId(string $prefix = 'SRQ'): string
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . $day . $randomNumber;
    }

    /**
     * Generate agreement ID.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateAgreementId(string $prefix = 'AGR'): string
    {
        $year = date('Y');
        $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $randomNumber;
    }

    /**
     * Generate QR Code.
     *
     * @param string $data
     * @param int $size
     * @return string
     */
    public static function generateQRCode(string $data, int $size = 200): string
    {
        return QrCode::size($size)->generate($data);
    }

    /**
     * Format file size.
     *
     * @param int $size
     * @return string
     */
    public static function formatFileSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get priority color.
     *
     * @param string $priority
     * @return string
     */
    public static function getPriorityColor(string $priority): string
    {
        return match (strtolower($priority)) {
            'low' => '#28a745',
            'medium' => '#ffc107',
            'high' => '#fd7e14',
            'critical' => '#dc3545',
            default => '#6c757d'
        };
    }

    /**
     * Get status color.
     *
     * @param string $status
     * @return string
     */
    public static function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'active', 'approved', 'completed' => '#28a745',
            'pending', 'in_progress' => '#ffc107',
            'rejected', 'cancelled' => '#dc3545',
            'inactive', 'expired' => '#6c757d',
            default => '#17a2b8'
        };
    }

    /**
     * Sanitize filename.
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename(string $filename): string
    {
        return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    }

    /**
     * Generate random password.
     *
     * @param int $length
     * @return string
     */
    public static function generatePassword(int $length = 12): string
    {
        return Str::random($length);
    }

    /**
     * Check if user can access resource.
     *
     * @param \App\Models\User $user
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public static function canAccess($user, string $resource, string $action): bool
    {
        $permission = $resource . '.' . $action;
        
        return $user->hasPermission($permission) || $user->isAdmin();
    }
}
