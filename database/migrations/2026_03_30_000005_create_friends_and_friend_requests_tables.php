<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('friend_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'friend_id']);
        });

        Schema::create('friend_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->unique(['sender_user_id', 'receiver_user_id']);
        });

        if (Schema::hasTable('friendships')) {
            $acceptedPairs = DB::table('friendships')
                ->where('status', 'accepted')
                ->select('user_id', 'friend_id', DB::raw('MIN(created_at) as created_at'), DB::raw('MAX(updated_at) as updated_at'))
                ->groupBy('user_id', 'friend_id')
                ->get();

            foreach ($acceptedPairs as $pair) {
                DB::table('friends')->updateOrInsert(
                    ['user_id' => $pair->user_id, 'friend_id' => $pair->friend_id],
                    ['created_at' => $pair->created_at ?? now(), 'updated_at' => $pair->updated_at ?? now()]
                );
            }

            $pendingRequests = DB::table('friendships')
                ->where('status', 'pending')
                ->select('requested_by_user_id', 'user_id', 'friend_id', 'created_at', 'updated_at')
                ->orderBy('id')
                ->get();

            foreach ($pendingRequests as $row) {
                $senderId = (int) $row->requested_by_user_id;
                $receiverId = $senderId === (int) $row->user_id ? (int) $row->friend_id : (int) $row->user_id;

                if ($senderId === $receiverId) {
                    continue;
                }

                DB::table('friend_requests')->updateOrInsert(
                    ['sender_user_id' => $senderId, 'receiver_user_id' => $receiverId],
                    [
                        'status' => 'pending',
                        'accepted_at' => null,
                        'rejected_at' => null,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_requests');
        Schema::dropIfExists('friends');
    }
};

