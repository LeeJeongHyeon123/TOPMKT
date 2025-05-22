<?php
/**
 * 추천 리더 목록을 가져옵니다.
 * 점수 산정 방식:
 * - 게시글 작성: 10점
 * - 댓글 작성: 1점
 * - 좋아요 받기: 1점
 */
function getRecommendedLeaders() {
    try {
        $db = Database::getInstance();
        
        $query = "SELECT 
                    u.id,
                    u.nickname,
                    u.profile_image,
                    u.company,
                    u.introduction as intro,
                    (COUNT(DISTINCT p.id) * 10 + 
                     COUNT(DISTINCT c.id) + 
                     COUNT(DISTINCT l.id)) as score
                  FROM TOPMKT.users u
                  LEFT JOIN TOPMKT.posts p ON u.id = p.user_id
                  LEFT JOIN TOPMKT.comments c ON u.id = c.user_id
                  LEFT JOIN TOPMKT.likes l ON u.id = l.user_id
                  WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY u.id
                  ORDER BY score DESC
                  LIMIT 100";
                  
        return $db->fetchAll($query);
    } catch (Exception $e) {
        error_log("[Functions] 추천 리더 조회 실패: " . $e->getMessage());
        return getDummyRecommendedLeaders();
    }
}

/**
 * 최신 비전 소개글을 가져옵니다.
 */
function getLatestVisionPosts() {
    try {
        $db = Database::getInstance();
        
        $query = "SELECT 
                    p.id,
                    p.title,
                    p.content,
                    p.created_at,
                    p.views,
                    COUNT(l.id) as likes,
                    u.nickname as author
                  FROM TOPMKT.posts p
                  JOIN TOPMKT.users u ON p.user_id = u.id
                  LEFT JOIN TOPMKT.likes l ON p.id = l.post_id
                  WHERE p.board_type = 'vision'
                  GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT 6";
                  
        $posts = $db->fetchAll($query);
        
        // 내용 요약 생성
        foreach ($posts as &$post) {
            $post['excerpt'] = mb_substr(strip_tags($post['content']), 0, 100) . '...';
        }
        
        return $posts;
    } catch (Exception $e) {
        error_log("[Functions] 최신 비전 소개글 조회 실패: " . $e->getMessage());
        return getDummyLatestVisionPosts();
    }
}

/**
 * 인기 커뮤니티 글을 가져옵니다.
 */
function getPopularCommunityPosts() {
    try {
        $db = Database::getInstance();
        
        $query = "SELECT 
                    p.id,
                    p.title,
                    p.content,
                    p.views,
                    COUNT(l.id) as likes
                  FROM TOPMKT.posts p
                  LEFT JOIN TOPMKT.likes l ON p.id = l.post_id
                  WHERE p.board_type = 'community'
                  GROUP BY p.id
                  ORDER BY (p.views + COUNT(l.id)) DESC
                  LIMIT 6";
                  
        $posts = $db->fetchAll($query);
        
        // 내용 요약 생성
        foreach ($posts as &$post) {
            $post['excerpt'] = mb_substr(strip_tags($post['content']), 0, 100) . '...';
        }
        
        return $posts;
    } catch (Exception $e) {
        error_log("[Functions] 인기 커뮤니티 글 조회 실패: " . $e->getMessage());
        return getDummyPopularCommunityPosts();
    }
}

/**
 * 다가오는 일정을 가져옵니다.
 */
function getUpcomingEvents() {
    try {
        $db = Database::getInstance();
        
        $query = "SELECT 
                    e.id,
                    e.title,
                    e.event_date as date,
                    e.event_time as time,
                    e.location,
                    e.event_type
                  FROM TOPMKT.events e
                  WHERE e.event_date >= CURDATE()
                    AND e.is_deleted = 0
                  ORDER BY e.event_date ASC, e.event_time ASC
                  LIMIT 6";
                  
        return $db->fetchAll($query);
    } catch (Exception $e) {
        error_log("[Functions] 다가오는 일정 조회 실패: " . $e->getMessage());
        return getDummyUpcomingEvents();
    }
}

/**
 * 날짜를 포맷팅합니다.
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return '방금 전';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . '분 전';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . '시간 전';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . '일 전';
    } else {
        return date('Y.m.d', $timestamp);
    }
}

/**
 * 다국어 메시지를 가져옵니다.
 */
function __($key, $replace = [], $lang = null) {
    static $loaded_messages = [];
    static $current_loaded_lang = null;

    $targetLang = $lang ?? (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko');
    // error_log("[Lang Debug] Target Lang: " . $targetLang . ", Key: " . $key); 

    if ($current_loaded_lang !== $targetLang || !isset($loaded_messages[$targetLang])) {
        $langFile = __DIR__ . "/../resources/lang/{$targetLang}/messages.php";
        // error_log("[Lang Debug] Loading Lang File: " . $langFile);

        if (file_exists($langFile)) {
            $messages_from_file = require $langFile;
            if (is_array($messages_from_file)) {
                $loaded_messages[$targetLang] = $messages_from_file;
                $current_loaded_lang = $targetLang;
                // error_log("[Lang Debug] Lang File Loaded Successfully for " . $targetLang);
            } else {
                $loaded_messages[$targetLang] = []; 
                // error_log("[Lang Debug] Lang File NOT an array: " . $langFile);
            }
        } else {
            $loaded_messages[$targetLang] = []; 
            // error_log("[Lang Debug] Lang File NOT found: " . $langFile);
        }
    }

    $message_set = $loaded_messages[$targetLang] ?? [];
    
    // 수정된 로직: 키 전체를 사용하여 메시지 세트에서 직접 값을 찾습니다.
    if (isset($message_set[$key])) {
        $value = $message_set[$key];
    } else {
        // 키를 점(.)으로 분리하여 중첩된 배열에서 값을 찾는 기존 로직 (menu.vision 같은 경우)
        $parts = explode('.', $key);
        $value = $message_set;
        $found = true;
        foreach ($parts as $part) {
            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                // error_log("[Lang Debug] Key PART NOT FOUND: " . $part . " in Key: " . $key);
                $found = false;
                break;
            }
        }
        if (!$found) {
            return $key; // 키를 찾지 못하면 원래 키 반환
        }
    }
    
    if (is_string($value) && !empty($replace)) {
        foreach ($replace as $placeholder => $replacement) {
            $value = str_replace(':' . $placeholder, $replacement, $value);
        }
    }
    
    // error_log("[Lang Debug] Returning Value: " . (is_array($value) ? '[ARRAY]' : $value) . " for Key: " . $key);
    return $value;
}

/**
 * 추천 리더 더미 데이터 생성
 */
function getDummyRecommendedLeaders() {
    return [
        [
            'id' => 1,
            'nickname' => '김성공',
            'company' => '애터미',
            'profile_image' => '/resources/images/profiles/kim_minjun.jpg',
            'introduction' => '10년 경력의 네트워크 마케팅 전문가입니다. 팀원들과 함께 성장하며, 지속 가능한 비즈니스 모델을 구축하는 것을 목표로 합니다. 현재 500명 이상의 팀을 이끌고 있으며, 매월 새로운 성공 사례를 만들어가고 있습니다.'
        ],
        [
            'id' => 2,
            'nickname' => '이비전',
            'company' => '뉴스킨',
            'profile_image' => '/resources/images/profiles/lee_seoyeon.jpg',
            'introduction' => '글로벌 네트워크 마케팅 리더로서, 아시아와 유럽에서 활발히 활동하고 있습니다. 건강과 웰빙에 대한 깊은 이해를 바탕으로, 팀원들의 성공을 위한 체계적인 교육 시스템을 운영하고 있습니다.'
        ],
        [
            'id' => 3,
            'nickname' => '박성장',
            'company' => '헤라라이프',
            'profile_image' => '/resources/images/profiles/park_jihun.jpg',
            'introduction' => '디지털 마케팅과 네트워크 마케팅을 접목한 혁신적인 비즈니스 모델을 추구합니다. 소셜 미디어를 활용한 효과적인 팀 빌딩 전략으로, 젊은 리더들을 양성하는데 주력하고 있습니다.'
        ],
        [
            'id' => 4,
            'nickname' => '최수아',
            'company' => '아모레퍼시픽',
            'profile_image' => '/resources/images/profiles/choi_sua.jpg',
            'introduction' => '뷰티 산업 15년 경력의 전문가입니다. 글로벌 뷰티 트렌드를 선도하며, 혁신적인 마케팅 전략으로 시장을 개척하고 있습니다. 팀원들의 창의성과 전문성을 키우는 교육 프로그램을 운영하고 있습니다.'
        ],
        [
            'id' => 5,
            'nickname' => '정도윤',
            'company' => 'LG생활건강',
            'profile_image' => '/resources/images/profiles/jung_doyun.jpg',
            'introduction' => '생활용품 분야에서 12년간 활동한 베테랑입니다. 제품 기획부터 마케팅까지 전 과정을 아우르는 전문성을 바탕으로, 지속 가능한 비즈니스 모델을 구축하고 있습니다. 신규 팀원 양성에 큰 열정을 가지고 있습니다.'
        ],
        [
            'id' => 6,
            'nickname' => '한미나',
            'company' => '코리아나',
            'profile_image' => '/resources/images/profiles/han_mina.jpg',
            'introduction' => '화장품 산업에서 8년간의 경력을 쌓은 전문가입니다. 디지털 마케팅과 오프라인 판매를 효과적으로 결합한 하이브리드 마케팅 전략으로 성공적인 성과를 만들어내고 있습니다. 팀원들의 성장을 최우선으로 생각합니다.'
        ]
    ];
}

/**
 * 인기 비전 소개글 더미 데이터 생성
 */
function getDummyLatestVisionPosts() {
    return [
        [
            'id' => 1,
            'title' => '디지털 마케팅의 새로운 패러다임',
            'excerpt' => 'AI와 빅데이터를 활용한 마케팅 전략의 혁신적인 변화에 대해 알아봅니다.',
            'author' => '김마케터',
            'created_at' => '2024-03-15',
            'views' => 3250,
            'likes' => 289
        ],
        [
            'id' => 2,
            'title' => '브랜드 아이덴티티 구축의 중요성',
            'excerpt' => '성공적인 브랜드 구축을 위한 핵심 전략과 실제 사례를 분석합니다.',
            'author' => '이브랜드',
            'created_at' => '2024-03-14',
            'views' => 2890,
            'likes' => 256
        ],
        [
            'id' => 3,
            'title' => '소셜 미디어 마케팅의 미래',
            'excerpt' => '메타버스와 소셜 커머스의 등장으로 변화하는 소셜 미디어 마케팅 전략을 살펴봅니다.',
            'author' => '박디지털',
            'created_at' => '2024-03-13',
            'views' => 2560,
            'likes' => 234
        ]
    ];
}

/**
 * 인기 커뮤니티 게시글 더미 데이터 생성
 */
function getDummyPopularCommunityPosts() {
    return [
        [
            'id' => 1,
            'title' => '초보 마케터를 위한 필수 도구 추천',
            'excerpt' => '마케팅 업무 효율을 높여주는 무료/유료 도구들을 소개합니다.',
            'views' => 1250,
            'likes' => 89
        ],
        [
            'id' => 2,
            'title' => '성공적인 콘텐츠 마케팅 사례 분석',
            'excerpt' => '국내외 성공적인 콘텐츠 마케팅 사례와 그 성공 요인을 분석합니다.',
            'views' => 980,
            'likes' => 76
        ],
        [
            'id' => 3,
            'title' => '마케팅 예산 효율적으로 사용하기',
            'excerpt' => '제한된 예산으로 최대의 효과를 내는 마케팅 전략을 공유합니다.',
            'views' => 850,
            'likes' => 65
        ]
    ];
}

/**
 * 다가오는 일정 더미 데이터 생성
 */
function getDummyUpcomingEvents() {
    return [
        [
            'id' => 1,
            'title' => '2024 디지털 마케팅 컨퍼런스',
            'date' => '2024-04-15',
            'time' => '09:30-13:30',
            'location' => '코엑스 그랜드볼룸'
        ],
        [
            'id' => 2,
            'title' => '브랜드 스토리텔링 워크샵',
            'date' => '2024-04-20',
            'time' => '14:00-17:00',
            'location' => '강남 마케팅 아카데미'
        ],
        [
            'id' => 3,
            'title' => '소셜 미디어 마케팅 세미나',
            'date' => '2024-04-25',
            'time' => '10:00-12:00',
            'location' => '온라인 ZOOM'
        ]
    ];
}

// 강의 일정 더미 데이터
function getDummyUpcomingLectures() {
    return [
        [
            'id' => 1,
            'title' => '성공적인 팀 빌딩 전략',
            'date' => date('Y-m-d', strtotime('+5 days')),
            'time' => '13:00-15:00',
            'location' => '서울 강남구 세미나룸',
            'speaker' => '김성공 리더'
        ],
        [
            'id' => 2,
            'title' => '디지털 마케팅의 이해',
            'date' => date('Y-m-d', strtotime('+8 days')),
            'time' => '15:30-17:30',
            'location' => '부산 해운대 컨벤션센터',
            'speaker' => '이마케팅 전문가'
        ],
        [
            'id' => 3,
            'title' => '글로벌 비즈니스 전략',
            'date' => date('Y-m-d', strtotime('+12 days')),
            'time' => '11:00-13:00',
            'location' => '인천 송도 글로벌캠퍼스',
            'speaker' => '박글로벌 컨설턴트'
        ]
    ];
}

// 노하우 공유 더미 데이터
function getDummyKnowhowPosts() {
    return [
        [
            'id' => 1,
            'title' => '초보 마케터를 위한 필수 마케팅 도구 10선',
            'excerpt' => '마케팅 업무 효율을 높여주는 무료/유료 도구들을 소개합니다.',
            'author' => '김마케터',
            'created_at' => '2024-03-15',
            'views' => 2150,
            'likes' => 189,
            'file_type' => 'pdf'
        ],
        [
            'id' => 2,
            'title' => '성공적인 콘텐츠 마케팅 전략 가이드',
            'excerpt' => '국내외 성공적인 콘텐츠 마케팅 사례와 그 성공 요인을 분석합니다.',
            'author' => '이콘텐츠',
            'created_at' => '2024-03-14',
            'views' => 1980,
            'likes' => 176,
            'file_type' => 'pdf'
        ],
        [
            'id' => 3,
            'title' => '마케팅 예산 효율적으로 사용하기',
            'excerpt' => '제한된 예산으로 최대의 효과를 내는 마케팅 전략을 공유합니다.',
            'author' => '박전략',
            'created_at' => '2024-03-13',
            'views' => 1850,
            'likes' => 165,
            'file_type' => 'pdf'
        ]
    ];
}

/**
 * 팀 리쿠르팅/모집 더미 데이터 생성
 */
function getDummyRecruitingPosts() {
    return [
        [
            'id' => 1,
            'title' => '서울 강남구 팀원 모집',
            'excerpt' => '애터미 서울 강남구 팀원 모집합니다. 경력 무관, 열정 있는 분 환영합니다.',
            'author' => '김리더',
            'created_at' => '2024-03-15',
            'views' => 1250,
            'likes' => 89,
            'location' => '서울 강남구',
            'company' => '애터미',
            'position' => '판매원'
        ],
        [
            'id' => 2,
            'title' => '부산 해운대구 리더 모집',
            'excerpt' => '뉴스킨 부산 해운대구 리더급 인재 모집합니다. 마케팅 경험자 우대.',
            'author' => '이매니저',
            'created_at' => '2024-03-14',
            'views' => 980,
            'likes' => 76,
            'location' => '부산 해운대구',
            'company' => '뉴스킨',
            'position' => '리더'
        ],
        [
            'id' => 3,
            'title' => '인천 송도 팀원 모집',
            'excerpt' => '헤라 인천 송도 신규 팀원 모집합니다. 초보자도 환영합니다.',
            'author' => '박팀장',
            'created_at' => '2024-03-13',
            'views' => 850,
            'likes' => 65,
            'location' => '인천 송도',
            'company' => '헤라',
            'position' => '판매원'
        ]
    ];
}

/**
 * 공지사항 더미 데이터 생성
 */
function getDummyNoticePosts() {
    return [
        [
            'id' => 1,
            'title' => '서비스 이용약관 개정 안내',
            'excerpt' => '2024년 4월 1일부터 적용되는 새로운 이용약관에 대해 안내드립니다.',
            'created_at' => '2024-03-15',
            'views' => 3250,
            'is_important' => true
        ],
        [
            'id' => 2,
            'title' => '시스템 점검 안내',
            'excerpt' => '2024년 3월 20일 02:00 ~ 04:00 동안 시스템 점검이 진행됩니다.',
            'created_at' => '2024-03-14',
            'views' => 2890,
            'is_important' => true
        ],
        [
            'id' => 3,
            'title' => '신규 기능 업데이트 안내',
            'excerpt' => '채팅 기능 개선 및 실시간 알림 기능이 추가되었습니다.',
            'created_at' => '2024-03-13',
            'views' => 2560,
            'is_important' => false
        ]
    ];
} 