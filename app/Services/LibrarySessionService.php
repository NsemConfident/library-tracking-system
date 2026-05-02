<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\LibrarySession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LibrarySessionService
{
    public function startSession(User $user): LibrarySession
    {
        if ($this->getActiveSession($user)) {
            throw new \Exception("{$user->name} already has an active session.");
        }

        return DB::transaction(function () use ($user) {
            $session = LibrarySession::create([
                'user_id' => $user->id,
                'check_in_time' => Carbon::now(),
                'status' => 'active',
            ]);

            AuditLog::log(
                'student_check_in',
                'LibrarySession',
                $session->id,
                "Student checked in: {$user->name}",
                ['student_id' => $user->id]
            );

            return $session->load('user');
        });
    }

    public function endSession(User $user): LibrarySession
    {
        $session = $this->getActiveSession($user);

        if (!$session) {
            throw new \Exception("{$user->name} has no active session to close.");
        }

        return DB::transaction(function () use ($session, $user) {
            $session->update([
                'check_out_time' => Carbon::now(),
                'status' => 'closed',
            ]);

            AuditLog::log(
                'student_check_out',
                'LibrarySession',
                $session->id,
                "Student checked out: {$user->name}",
                ['student_id' => $user->id]
            );

            return $session->refresh()->load('user');
        });
    }

    public function getActiveSession(User $user): ?LibrarySession
    {
        return LibrarySession::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('check_in_time')
            ->first();
    }

    public function validateSession(?LibrarySession $session): void
    {
        if (!$session || $session->status !== 'active') {
            throw new \Exception('No active student session. Scan a student card first.');
        }
    }
}
