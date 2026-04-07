<?php

return [
    'success' => 'نجاح',
    'ok' => 'موافق',
    'error' => 'خطأ',
    'something_went_wrong' => 'حدث خطأ ما',
    'not_found' => 'العنصر غير موجود',
    'internal_server_error' => 'خطأ داخلي في الخادم',
    'too_many_requests' => 'طلبات كثيرة جداً',
    'validation_failed' => 'فشل التحقق',

    // Auth
    'auth' => [
        'register_success' => 'تم التسجيل بنجاح. يرجى التحقق برمز OTP (استخدم 1111 للاختبار).',
        'login_success' => 'تم تسجيل الدخول بنجاح.',
        'verify_success' => 'تم التحقق بنجاح.',
        'logout_success' => 'تم تسجيل الخروج.',
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح.',
        'requires_otp' => 'يرجى التحقق من رقم الهاتف برمز OTP أولاً.',
        'credentials_incorrect' => 'بيانات الاعتماد غير صحيحة.',
        'user_not_found' => 'المستخدم غير موجود.',
        'invalid_otp' => 'رمز OTP غير صالح أو منتهي. استخدم 1111 للاختبار.',
    ],

    'unauthorized' => 'غير مصرح',
    'forbidden' => 'ممنوع',
    'invalid_api_key' => 'مفتاح API غير صالح أو مفقود.',

    // Friendship
    'friendship' => [
        'request_sent' => 'تم إرسال طلب الصداقة.',
        'request_accepted' => 'تم قبول طلب الصداقة.',
        'request_rejected' => 'تم رفض طلب الصداقة.',
        'deleted' => 'تم حذف الصديق.',
        'cannot_friend_self' => 'لا يمكنك إرسال طلب صداقة لنفسك.',
        'request_already_pending' => 'يوجد طلب صداقة معلق بالفعل بينكما.',
        'already_friends' => 'أنتما بالفعل أصدقاء.',
        'cannot_request' => 'لا يمكنك إرسال طلب صداقة الآن.',
        'not_pending' => 'طلب الصداقة ليس معلقاً.',
        'cannot_accept_own_request' => 'لا يمكنك قبول طلب الصداقة الذي أرسلته أنت.',
        'not_allowed' => 'غير مسموح لك تنفيذ هذا الإجراء.',
    ],

    // Area
    'area' => [
        'created' => 'تم إنشاء المنطقة بنجاح.',
        'updated' => 'تم تحديث المنطقة بنجاح.',
        'deleted' => 'تم حذف المنطقة بنجاح.',
    ],

    // Config
    'config' => [
        'created' => 'تم إنشاء الإعدادات بنجاح.',
        'updated' => 'تم تحديث الإعدادات بنجاح.',
        'deleted' => 'تم حذف الإعدادات بنجاح.',
    ],

    // Division
    'division' => [
        'created' => 'تم إنشاء القسم بنجاح.',
        'updated' => 'تم تحديث القسم بنجاح.',
        'deleted' => 'تم حذف القسم بنجاح.',
    ],

    // Club
    'club' => [
        'not_a_member' => 'أنت لست عضواً نشطاً في هذا النادي.',
        'not_captain' => 'فقط كابتن النادي يمكنه إدارة هذا النادي.',
        'max_players_reached' => 'وصل النادي إلى الحد الأقصى لعدد اللاعبين (بما في ذلك الدعوات المعلقة).',
        'invites_sent' => 'تم إرسال دعوات النادي.',
        'invite_accepted' => 'لقد انضممت إلى النادي.',
        'invite_rejected' => 'لقد رفضت دعوة النادي.',
        'invite_not_for_user' => 'هذه الدعوة ليست لك.',
        'cannot_reject_active' => 'لا يمكنك رفض عضوية نشطة.',
        'created' => 'تم إنشاء النادي بنجاح.',
        'updated' => 'تم تحديث النادي بنجاح.',
        'deleted' => 'تم حذف النادي بنجاح.',
    ],

    // Stadium
    'stadium' => [
        'created' => 'تم إنشاء الملعب بنجاح.',
        'updated' => 'تم تحديث الملعب بنجاح.',
        'deleted' => 'تم حذف الملعب بنجاح.',
    ],

    // Match schedule requests
    'match_schedule_request' => [
        'created' => 'تم إنشاء طلب الجدولة. يرجى إتمام الدفع للتأكيد.',
        'not_allowed' => 'غير مسموح لك عرض هذا الطلب.',
        'invalid_team_source' => 'مصدر الفريق غير صالح.',
        'max_players' => 'يمكنك اختيار حتى 4 لاعبين.',
        'slots_required' => 'يرجى اختيار موعد واحد على الأقل.',
        'slot_end_after_start' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        'players_not_in_club' => 'لاعب أو أكثر ليسوا أعضاء نشطين في النادي.',
        'players_not_friends' => 'لاعب أو أكثر ليسوا من أصدقائك.',
        'area_required' => 'المنطقة مطلوبة (حددها في ملفك الشخصي أو اختر ناديًا لديه منطقة).',
        'slot_not_found' => 'الوقت المحدد غير موجود في هذا الطلب.',
        'cannot_join_own' => 'لا يمكنك الانضمام إلى طلب المباراة الخاص بك.',
        'already_has_opponent' => 'هذا الطلب لديه خصم بالفعل.',
        'not_stadium_owner' => 'فقط مالك الملعب يمكنه قبول المباراة.',
        'cannot_accept_by_stadium' => 'لا يمكن لهذا الطلب أن يُقبل من الملعب (تحقق من الخصم والوقت).',
        'joined' => 'لقد انضممت إلى طلب المباراة كفريق خصم.',
        'accepted_by_stadium' => 'تم قبول المباراة من الملعب وتم إنشاؤها.',
    ],

    // Match results / EXP
    'match_result' => [
        'recorded' => 'تم تسجيل نتيجة المباراة وتحديث نقاط الخبرة.',
        'not_stadium_owner' => 'فقط مالك الملعب يمكنه تسجيل نتيجة هذه المباراة.',
        'not_for_this_stadium' => 'هذه المباراة ليست تابعة لملعبك.',
        'already_completed' => 'تم تسجيل نتيجة هذه المباراة من قبل.',
        'winner_score_mismatch' => 'الفائز لا يتطابق مع الأهداف المدخلة.',
        'draw_score_mismatch' => 'نتيجة التعادل تتطلب تساوي الأهداف.',
    ],
];
