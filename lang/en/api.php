<?php

return [
    'success' => 'Success',
    'ok' => 'OK',
    'error' => 'Error',
    'something_went_wrong' => 'Something went wrong',
    'not_found' => 'Resource not found',
    'internal_server_error' => 'Internal server error',
    'too_many_requests' => 'Too many requests',
    'validation_failed' => 'Validation failed',

    // Auth
    'auth' => [
        'register_success' => 'Registration successful. Please verify with OTP (use 1111 for now).',
        'stadium_register_success' => 'Stadium owner registration successful. Please verify with OTP (use 1111 for now).',
        'login_success' => 'Logged in successfully.',
        'stadium_login_success' => 'Logged in successfully.',
        'verify_success' => 'Verified successfully.',
        'stadium_verify_success' => 'Verified successfully.',
        'logout_success' => 'Logged out.',
        'profile_updated' => 'Profile updated successfully.',
        'requires_otp' => 'Please verify your phone with OTP first.',
        'credentials_incorrect' => 'The provided credentials are incorrect.',
        'user_not_found' => 'User not found.',
        'invalid_otp' => 'Invalid or expired OTP. Use 1111 for testing.',
        'use_stadium_login' => 'This account is for a stadium owner. Use the stadium owner login.',
        'use_stadium_verify' => 'This account is for a stadium owner. Use the stadium owner OTP verification.',
        'use_player_verify' => 'This is not a stadium owner account. Use the player OTP verification.',
        'stadium_owner_only' => 'Only stadium owner accounts can sign in here.',
        'stadium_owner_required' => 'This action is only available to stadium owners.',
        'player_route_only' => 'This action is not available to stadium owner accounts. Use the stadium owner app.',
    ],

    // HTTP status names (optional)
    'unauthorized' => 'Unauthorized',
    'forbidden' => 'Forbidden',
    'invalid_api_key' => 'Invalid or missing API key.',

    // Friendship
    'friendship' => [
        'request_sent' => 'Friend request sent.',
        'request_accepted' => 'Friend request accepted.',
        'request_rejected' => 'Friend request rejected.',
        'deleted' => 'Friend removed.',
        'cannot_friend_self' => 'You cannot send a friend request to yourself.',
        'request_already_pending' => 'There is already a pending friend request between you two.',
        'already_friends' => 'You are already friends.',
        'cannot_request' => 'You cannot send a friend request right now.',
        'not_pending' => 'This friend request is not pending.',
        'cannot_accept_own_request' => 'You cannot accept your own friend request.',
        'not_allowed' => 'You are not allowed to perform this action.',
    ],

    // Area
    'area' => [
        'created' => 'Area created successfully.',
        'updated' => 'Area updated successfully.',
        'deleted' => 'Area deleted successfully.',
    ],

    // Config
    'config' => [
        'created' => 'Config created successfully.',
        'updated' => 'Config updated successfully.',
        'deleted' => 'Config deleted successfully.',
    ],

    // Division
    'division' => [
        'created' => 'Division created successfully.',
        'updated' => 'Division updated successfully.',
        'deleted' => 'Division deleted successfully.',
    ],

    // Club
    'club' => [
        'not_a_member' => 'You are not an active member of this club.',
        'not_captain' => 'Only the club captain can manage this club.',
        'max_players_reached' => 'Club has reached the maximum number of players (including pending invites).',
        'invites_sent' => 'Club invitations sent.',
        'invite_accepted' => 'You have joined the club.',
        'invite_rejected' => 'You have declined the club invitation.',
        'invite_not_for_user' => 'This invitation does not belong to you.',
        'cannot_reject_active' => 'You cannot reject an active membership.',
        'created' => 'Club created successfully.',
        'updated' => 'Club updated successfully.',
        'deleted' => 'Club deleted successfully.',
    ],

    // Stadium
    'stadium' => [
        'created' => 'Stadium created successfully.',
        'updated' => 'Stadium updated successfully.',
        'deleted' => 'Stadium deleted successfully.',
    ],

    // Match schedule requests
    'match_schedule_request' => [
        'created' => 'Schedule request created. Please complete payment to confirm.',
        'not_allowed' => 'You are not allowed to view this request.',
        'invalid_team_source' => 'Invalid team source.',
        'max_players' => 'You can select up to 4 teammates.',
        'slots_required' => 'Please select at least one schedule slot.',
        'slot_end_after_start' => 'End time must be after start time.',
        'players_not_in_club' => 'One or more selected players are not active members of the club.',
        'players_not_friends' => 'One or more selected players are not your friends.',
        'area_required' => 'Area is required (set it in your profile or choose a club with an area).',
        'slot_not_found' => 'Selected slot was not found on this request.',
        'cannot_join_own' => 'You cannot join your own match request.',
        'already_has_opponent' => 'This request already has an opponent.',
        'not_stadium_owner' => 'Only stadium owners can accept matches for a stadium.',
        'cannot_accept_by_stadium' => 'This match request cannot be accepted by a stadium (check opponent and slot).',
        'joined' => 'You joined the match request as opponent team.',
        'accepted_by_stadium' => 'Stadium accepted and match created.',
    ],

    // Match results / EXP
    'match_result' => [
        'recorded' => 'Match result recorded and EXP updated.',
        'not_stadium_owner' => 'Only the stadium owner can record this match result.',
        'not_for_this_stadium' => 'This match does not belong to your stadium.',
        'already_completed' => 'Match result has already been recorded.',
        'winner_score_mismatch' => 'Winner does not match the provided scores.',
        'draw_score_mismatch' => 'Draw result requires equal scores.',
    ],
];
