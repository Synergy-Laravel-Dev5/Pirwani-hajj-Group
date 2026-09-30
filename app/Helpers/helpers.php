<?php

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

if (!function_exists('logUserActivity')) {
    function logUserActivity($activityType, $details = null, $entityId = null, $entityType = null, $targetCompanyId = null)
    {
        try {
            $userId = null;
            $companyId = $targetCompanyId;
            $companyLoginId = null;
            $actorName = 'System';

            try {
                if (config()->has('auth.guards.company') && Auth::guard('company')->check()) {
                    $compLogin = Auth::guard('company')->user();
                    $companyLoginId = $compLogin->id;
                    $companyId = $compLogin->company_id;
                    $compName = $compLogin->company->company_name ?? ($compLogin->company->name ?? 'Company');
                    $actorName = "Company: {$compName} ({$compLogin->email})";
                }
            } catch (\Throwable $e) {}

            if (!$userId && Auth::guard('web')->check()) {
                $webUser = Auth::guard('web')->user();
                $userId = $webUser->id;
                if (!empty($webUser->company_id)) {
                    $companyId = $webUser->company_id;
                    $compName = $webUser->company->company_name ?? ($webUser->company->name ?? 'Company');
                    $actorName = "User: {$webUser->name} ({$compName})";
                } else {
                    $actorName = "Admin: {$webUser->name}";
                }
            }

            if (Schema::hasTable('user_activities')) {
                $data = [
                    'activity_type' => $activityType,
                    'details'       => $details,
                    'entity_id'     => $entityId,
                    'entity_type'   => $entityType,
                    'ip_address'    => request()->ip(),
                ];

                if (Schema::hasColumn('user_activities', 'user_id')) {
                    $data['user_id'] = $userId;
                }
                if (Schema::hasColumn('user_activities', 'company_id')) {
                    $data['company_id'] = $companyId;
                }
                if (Schema::hasColumn('user_activities', 'company_login_id')) {
                    $data['company_login_id'] = $companyLoginId;
                }
                if (Schema::hasColumn('user_activities', 'actor_name')) {
                    $data['actor_name'] = $actorName;
                }

                UserActivity::create($data);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log user activity: ' . $e->getMessage());
        }
    }
}

if (!function_exists('isCompanyUser')) {
    function isCompanyUser()
    {
        try {
            if (config()->has('auth.guards.company') && Auth::guard('company')->check()) {
                return true;
            }
        } catch (\Throwable $e) {}

        if (Auth::guard('web')->check() && !empty(Auth::guard('web')->user()->company_id)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('getAuthCompanyId')) {
    function getAuthCompanyId()
    {
        try {
            if (config()->has('auth.guards.company') && Auth::guard('company')->check()) {
                return Auth::guard('company')->user()->company_id;
            }
        } catch (\Throwable $e) {}

        if (Auth::guard('web')->check() && !empty(Auth::guard('web')->user()->company_id)) {
            return Auth::guard('web')->user()->company_id;
        }
        return null;
    }
}

if (!function_exists('getAuthCompany')) {
    function getAuthCompany()
    {
        try {
            if (config()->has('auth.guards.company') && Auth::guard('company')->check()) {
                return Auth::guard('company')->user()->company;
            }
        } catch (\Throwable $e) {}

        if (Auth::guard('web')->check() && !empty(Auth::guard('web')->user()->company_id)) {
            return Auth::guard('web')->user()->company;
        }
        return null;
    }
}

if (!function_exists('sendAppNotification')) {
    function sendAppNotification(
        string $title,
        string $message,
        ?string $link = null,
        string $type = 'info',
        string $icon = 'mdi-bell-ring-outline',
        bool $forAdmin = false,
        ?int $companyId = null,
        ?int $userId = null
    ) {
        try {
            if (class_exists(\App\Models\AppNotification::class) && Schema::hasTable('app_notifications')) {
                return \App\Models\AppNotification::create([
                    'user_id'    => $userId,
                    'company_id' => $companyId,
                    'for_admin'  => $forAdmin,
                    'title'      => $title,
                    'message'    => $message,
                    'type'       => $type,
                    'icon'       => $icon,
                    'link'       => $link,
                    'is_read'    => false,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send app notification: ' . $e->getMessage());
        }
        return null;
    }
}

if (!function_exists('uploadFileToPublic')) {
    function uploadFileToPublic($file, string $subFolder = 'uploads'): ?string
    {
        if (!$file || !($file instanceof \Illuminate\Http\UploadedFile)) {
            return null;
        }

        $cleanSubFolder = trim(str_replace('\\', '/', $subFolder), '/');
        $targetDir = public_path('assets/uploads/' . $cleanSubFolder);

        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $filename = time() . '_' . uniqid() . '.' . $extension;

        $file->move($targetDir, $filename);

        return 'assets/uploads/' . $cleanSubFolder . '/' . $filename;
    }
}

if (!function_exists('deletePublicFile')) {
    function deletePublicFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        $cleanPath = trim(str_replace('\\', '/', $relativePath), '/');
        $filesToCheck = [
            public_path($cleanPath),
            public_path('assets/uploads/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
        ];
        foreach ($filesToCheck as $file) {
            if (file_exists($file) && !is_dir($file)) {
                @unlink($file);
            }
        }
    }
}

if (!function_exists('resolveFilePath')) {
    function resolveFilePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $clean = trim(str_replace('\\', '/', $path), '/');

        if (file_exists(public_path($clean))) {
            return public_path($clean);
        }

        if (file_exists(public_path('assets/uploads/' . $clean))) {
            return public_path('assets/uploads/' . $clean);
        }

        if (file_exists(public_path('storage/' . $clean))) {
            return public_path('storage/' . $clean);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($clean)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->path($clean);
        }

        return null;
    }
}

if (!function_exists('getMediaUrl')) {
    function getMediaUrl(?string $path, string $fallback = 'assets/images/no-image.png'): string
    {
        if (!$path) {
            return asset($fallback);
        }

        $clean = trim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }

        if (str_starts_with($clean, 'public/')) {
            $clean = substr($clean, 7);
        }

        if (file_exists(public_path($clean))) {
            return asset($clean);
        }

        if (str_starts_with($clean, 'assets/')) {
            return asset($clean);
        }

        if (str_starts_with($clean, 'uploads/')) {
            return asset('assets/' . $clean);
        }

        if (file_exists(public_path('assets/uploads/' . $clean))) {
            return asset('assets/uploads/' . $clean);
        }

        if (file_exists(public_path('storage/' . $clean))) {
            return asset('storage/' . $clean);
        }

        if (str_starts_with($clean, 'storage/')) {
            return asset($clean);
        }

        return asset('assets/uploads/' . $clean);
    }
}