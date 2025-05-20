<?php
return [
    // 공통
    'common.welcome' => '환영합니다',
    'common.error' => '오류가 발생했습니다',
    'common.success' => '성공적으로 처리되었습니다',
    
    // 인증
    'auth.login' => '로그인',
    'auth.register' => '회원가입',
    'auth.logout' => '로그아웃',
    'auth.phone_verification' => '휴대폰 인증',
    'auth.send_code' => '인증번호 받기',
    'auth.verify_code' => '인증번호 확인',
    'auth.code_sent' => '인증번호가 전송되었습니다',
    'auth.code_verified' => '인증이 완료되었습니다',
    'auth.code_invalid' => '잘못된 인증번호입니다',
    'auth.code_expired' => '인증번호가 만료되었습니다',
    'auth.too_many_attempts' => '인증 시도 횟수를 초과했습니다',
    
    // 에러
    'error.not_found' => '페이지를 찾을 수 없습니다',
    'error.server_error' => '서버 오류가 발생했습니다',
    'error.forbidden' => '접근이 거부되었습니다',
    'error.unauthorized' => '로그인이 필요합니다',
    
    // 메뉴
    'menu' => [
        'vision' => '회사/비전 소개',
        'knowhow' => '노하우 공유',
        'recruiting' => '팀 리쿠르팅 모집',
        'events' => '행사 일정',
        'lecture' => '강의 일정',
        'community' => '자유 커뮤니티',
        'notice' => '공지사항',
        'mypage' => '마이페이지',
        'messages' => '메시지',
        'profile' => '내 프로필',
        'logout' => '로그아웃',
        'login' => '로그인'
    ],
    
    // 언어
    'lang.ko' => '한국어',
    'lang.en' => 'English',
    'lang.zh' => '简体中文',
    'lang.zh-tw' => '繁體中文',
    'lang.ja' => '日本語',
    'main' => [
        'title' => '탑마케팅',
        'recommended_leaders' => '추천 리더',
        'latest_vision' => '최신 비전 소개',
        'popular_community' => '인기 커뮤니티',
        'upcoming_events' => '다가오는 일정',
        'my_profile' => '내 프로필',
        'chat' => '채팅',
        'logout' => '로그아웃',
        'login' => '로그인',
        'all_rights_reserved' => '모든 권리 보유'
    ],
    'auth' => [
        'login' => [
            'title' => '로그인',
            'subtitle' => '휴대폰 번호로 간편하게 로그인하세요',
            'phone_placeholder' => '휴대폰 번호를 입력하세요',
            'verification_placeholder' => '인증번호 6자리',
            'send_code' => '인증번호 받기',
            'login_button' => '로그인',
            'no_account' => '아직 계정이 없으신가요?',
            'register' => '회원가입'
        ],
        'register' => [
            'title' => '회원가입',
            'subtitle' => '휴대폰 번호로 간편하게 가입하세요',
            'phone_placeholder' => '휴대폰 번호를 입력하세요',
            'verification_placeholder' => '인증번호 6자리',
            'send_code' => '인증번호 받기',
            'nickname_placeholder' => '닉네임을 입력하세요',
            'register_button' => '회원가입',
            'has_account' => '이미 계정이 있으신가요?',
            'login' => '로그인'
        ],
        'errors' => [
            'verification_failed' => '인증번호 확인에 실패했습니다.',
            'send_failed' => '인증번호 전송에 실패했습니다.',
            'recaptcha_failed' => 'reCAPTCHA 검증에 실패했습니다.'
        ]
    ]
]; 