<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('friendships')->get();

        foreach ($rows as $row) {
            $requestedBy = (int) $row->requested_by_user_id;
            $userId = (int) $row->user_id;
            $friendId = (int) $row->friend_id;

            $otherUserId = $requestedBy === $userId ? $friendId : $userId;

            // Ensure requester -> other row exists
            DB::table('friendships')->updateOrInsert(
                ['user_id' => $requestedBy, 'friend_id' => $otherUserId],
                [
                    'requested_by_user_id' => $requestedBy,
                    'status' => $row->status,
                    'accepted_at' => $row->accepted_at,
                    'rejected_at' => $row->rejected_at,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => now(),
                ]
            );

            // Ensure other -> requester row exists
            DB::table('friendships')->updateOrInsert(
                ['user_id' => $otherUserId, 'friend_id' => $requestedBy],
                [
                    'requested_by_user_id' => $requestedBy,
                    'status' => $row->status,
                    'accepted_at' => $row->accepted_at,
                    'rejected_at' => $row->rejected_at,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // No safe down migration (cannot know which rows were original).
    }
};

