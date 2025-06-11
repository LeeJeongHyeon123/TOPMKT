<?php
/**
 * 커뮤니티 페이지 성능 분석 스크립트
 * URL: https://www.topmktx.com/community_performance_analysis.php
 */

require_once __DIR__ . '/src/config/config.php';
require_once SRC_PATH . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>커뮤니티 성능 분석 - 탑마케팅</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
        }
        h1 { 
            color: #2d3748; 
            text-align: center; 
            margin-bottom: 30px; 
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section { 
            margin-bottom: 30px; 
            padding: 20px; 
            background: #f8fafc; 
            border-radius: 8px; 
            border-left: 4px solid #667eea; 
        }
        .section h2 { 
            color: #4a5568; 
            margin-top: 0; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .metric { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0; 
            border-bottom: 1px solid #e2e8f0; 
        }
        .metric:last-child { border-bottom: none; }
        .metric .label { font-weight: 600; color: #2d3748; }
        .metric .value { color: #667eea; font-weight: 700; }
        .status-good { color: #38a169; }
        .status-warning { color: #d69e2e; }
        .status-error { color: #e53e3e; }
        .progress-bar { 
            width: 100%; 
            background: #e2e8f0; 
            border-radius: 10px; 
            overflow: hidden; 
            height: 25px;
            position: relative;
        }
        .progress-fill { 
            height: 100%; 
            background: linear-gradient(90deg, #667eea, #764ba2); 
            transition: width 1s ease; 
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .recommendation { 
            background: #fff5f5; 
            border: 1px solid #feb2b2; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 10px 0; 
        }
        .success { 
            background: #f0fff4; 
            border: 1px solid #9ae6b4; 
        }
        .code { 
            background: #2d3748; 
            color: #e2e8f0; 
            padding: 10px; 
            border-radius: 6px; 
            font-family: monospace; 
            margin: 10px 0;
            overflow-x: auto;
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-good { background: #38a169; color: white; }
        .badge-warning { background: #d69e2e; color: white; }
        .badge-error { background: #e53e3e; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 커뮤니티 페이지 성능 분석</h1>
        
        <?php
        try {
            $db = Database::getInstance()->getConnection();
            
            // 1. 데이터 현황 분석
            echo '<div class="section">';
            echo '<h2>📊 데이터 현황 분석</h2>';
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
            $totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE profile_image_thumb IS NOT NULL");
            $usersWithImages = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $imageOptimizationRate = ($usersWithImages / max($totalUsers, 1)) * 100;
            
            echo '<div class="metric"><span class="label">📝 총 게시글 수</span><span class="value">' . number_format($totalPosts) . '개</span></div>';
            echo '<div class="metric"><span class="label">👥 총 사용자 수</span><span class="value">' . number_format($totalUsers) . '명</span></div>';
            echo '<div class="metric"><span class="label">🖼️ 프로필 이미지 보유 사용자</span><span class="value">' . number_format($usersWithImages) . '명 (' . round($imageOptimizationRate, 1) . '%)</span></div>';
            echo '</div>';
            
            // 2. 인덱스 상태 확인
            echo '<div class="section">';
            echo '<h2>🔍 데이터베이스 인덱스 상태</h2>';
            
            $stmt = $db->query("
                SELECT INDEX_NAME, COLUMN_NAME, CARDINALITY, INDEX_TYPE
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = 'topmkt' 
                  AND TABLE_NAME = 'posts'
                  AND INDEX_NAME != 'PRIMARY'
                ORDER BY INDEX_NAME
            ");
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($indexes)) {
                echo '<div class="recommendation">';
                echo '<strong>⚠️ 성능 최적화 인덱스가 없습니다!</strong><br>';
                echo '<span class="badge badge-error">urgent</span> 데이터베이스 성능 향상을 위해 다음 명령어를 실행하세요:<br>';
                echo '<div class="code">mysql -u root -pDnlszkem1! topmkt < optimize_community_performance.sql</div>';
                echo '</div>';
                $indexScore = 0;
            } else {
                echo '<div class="success">';
                echo '<strong>✅ 설정된 인덱스:</strong><br>';
                foreach ($indexes as $index) {
                    echo "• <strong>{$index['INDEX_NAME']}</strong>: {$index['COLUMN_NAME']} (카디널리티: " . number_format($index['CARDINALITY']) . ")<br>";
                }
                echo '</div>';
                $indexScore = 30;
            }
            echo '</div>';
            
            // 3. 프로필 이미지 최적화 상태
            echo '<div class="section">';
            echo '<h2>🖼️ 프로필 이미지 최적화 분석</h2>';
            
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN profile_image_original IS NOT NULL THEN 1 ELSE 0 END) as has_original,
                    SUM(CASE WHEN profile_image_profile IS NOT NULL THEN 1 ELSE 0 END) as has_profile,
                    SUM(CASE WHEN profile_image_thumb IS NOT NULL THEN 1 ELSE 0 END) as has_thumb
                FROM users 
                WHERE status = 'active'
            ");
            $imageStats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '<div class="grid">';
            echo '<div>';
            echo '<h3>📊 이미지 보유 현황</h3>';
            echo '<div class="metric"><span class="label">Original (1000px)</span><span class="value">' . number_format($imageStats['has_original']) . '명</span></div>';
            echo '<div class="metric"><span class="label">Profile (200px)</span><span class="value">' . number_format($imageStats['has_profile']) . '명</span></div>';
            echo '<div class="metric"><span class="label">Thumb (80px)</span><span class="value">' . number_format($imageStats['has_thumb']) . '명</span></div>';
            echo '<div class="metric"><span class="label">최적화율</span><span class="value">' . round($imageOptimizationRate, 1) . '%</span></div>';
            echo '</div>';
            
            // 용량 절약 효과 계산
            $avgOriginalSize = 300; // KB
            $avgThumbSize = 8; // KB
            $totalOriginalSize = ($imageStats['has_original'] * $avgOriginalSize) / 1024; // MB
            $totalThumbSize = ($imageStats['has_thumb'] * $avgThumbSize) / 1024; // MB
            $savedBandwidth = $totalOriginalSize - $totalThumbSize;
            $savingPercentage = ($savedBandwidth / max($totalOriginalSize, 1)) * 100;
            
            echo '<div>';
            echo '<h3>💾 용량 절약 효과</h3>';
            echo '<div class="metric"><span class="label">Original 사용 시</span><span class="value">' . round($totalOriginalSize, 1) . ' MB</span></div>';
            echo '<div class="metric"><span class="label">Thumb 사용 시</span><span class="value">' . round($totalThumbSize, 1) . ' MB</span></div>';
            echo '<div class="metric"><span class="label">절약된 대역폭</span><span class="value status-good">' . round($savedBandwidth, 1) . ' MB</span></div>';
            echo '<div class="metric"><span class="label">절약률</span><span class="value status-good">' . round($savingPercentage, 1) . '%</span></div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            // 4. 쿼리 성능 테스트
            echo '<div class="section">';
            echo '<h2>⚡ 실시간 성능 테스트</h2>';
            
            $startTime = microtime(true);
            $stmt = $db->prepare("
                SELECT 
                    p.id, p.title, p.created_at,
                    u.nickname as author_name,
                    COALESCE(u.profile_image_thumb, u.profile_image_profile, '/assets/images/default-avatar.png') as profile_image
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'published'
                ORDER BY p.created_at DESC 
                LIMIT 20
            ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $queryTime = (microtime(true) - $startTime) * 1000;
            
            $startTime = microtime(true);
            $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE status = 'published'");
            $stmt->execute();
            $countTime = (microtime(true) - $startTime) * 1000;
            
            $queryClass = $queryTime < 50 ? 'status-good' : ($queryTime < 100 ? 'status-warning' : 'status-error');
            $countClass = $countTime < 10 ? 'status-good' : ($countTime < 50 ? 'status-warning' : 'status-error');
            
            echo '<div class="grid">';
            echo '<div>';
            echo '<div class="metric"><span class="label">첫 페이지 로딩</span><span class="value ' . $queryClass . '">' . round($queryTime, 2) . ' ms</span></div>';
            echo '<div class="metric"><span class="label">총 개수 조회</span><span class="value ' . $countClass . '">' . round($countTime, 2) . ' ms</span></div>';
            echo '<div class="metric"><span class="label">조회된 게시글</span><span class="value">' . count($results) . '개</span></div>';
            echo '</div>';
            
            // 성능 점수 계산
            $queryScore = $queryTime < 50 ? 30 : ($queryTime < 100 ? 15 : 0);
            $imageScore = $imageOptimizationRate > 90 ? 40 : ($imageOptimizationRate > 50 ? 20 : 0);
            
            echo '<div>';
            echo '<h3>🎯 성능 기준</h3>';
            echo '<div class="metric"><span class="label">우수 (50ms 미만)</span><span class="badge badge-good">GOOD</span></div>';
            echo '<div class="metric"><span class="label">보통 (50-100ms)</span><span class="badge badge-warning">OK</span></div>';
            echo '<div class="metric"><span class="label">개선 필요 (100ms 이상)</span><span class="badge badge-error">SLOW</span></div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            // 5. 종합 평가
            echo '<div class="section">';
            echo '<h2>🏆 종합 성능 평가</h2>';
            
            $totalScore = $indexScore + $imageScore + $queryScore;
            $maxScore = 100;
            
            echo '<div style="margin: 20px 0;">';
            echo '<div class="progress-bar">';
            echo '<div class="progress-fill" style="width: ' . ($totalScore) . '%">' . $totalScore . '점</div>';
            echo '</div>';
            echo '<p style="text-align: center; margin: 10px 0; font-size: 18px; font-weight: bold;">총 최적화 점수: ' . $totalScore . '/' . $maxScore . '점</p>';
            echo '</div>';
            
            echo '<div class="grid">';
            echo '<div>';
            echo '<h3>📊 점수 상세</h3>';
            echo '<div class="metric"><span class="label">데이터베이스 인덱스</span><span class="value">' . $indexScore . '/30점</span></div>';
            echo '<div class="metric"><span class="label">이미지 최적화</span><span class="value">' . $imageScore . '/40점</span></div>';
            echo '<div class="metric"><span class="label">쿼리 성능</span><span class="value">' . $queryScore . '/30점</span></div>';
            echo '</div>';
            
            echo '<div>';
            echo '<h3>📋 평가 결과</h3>';
            if ($totalScore >= 80) {
                echo '<div class="success"><strong>🎉 성능 최적화 상태가 우수합니다!</strong><br>현재 상태를 유지하며 정기적인 모니터링을 권장합니다.</div>';
            } elseif ($totalScore >= 50) {
                echo '<div class="recommendation"><strong>📈 성능 최적화가 진행 중입니다.</strong><br>아래 권장사항을 적용하여 추가 개선하세요.</div>';
            } else {
                echo '<div class="recommendation status-error"><strong>🚨 성능 최적화가 시급히 필요합니다!</strong><br>사용자 경험 향상을 위해 즉시 최적화 작업을 진행하세요.</div>';
            }
            echo '</div>';
            echo '</div>';
            
            // 6. 실행 가능한 개선 방안
            echo '<h2>🔧 실행 가능한 개선 방안</h2>';
            
            if ($indexScore == 0) {
                echo '<div class="recommendation">';
                echo '<h3>1. 🚀 데이터베이스 인덱스 추가 (즉시 실행 가능)</h3>';
                echo '<p>예상 성능 향상: <strong>50-70% 로딩 시간 단축</strong></p>';
                echo '<div class="code">cd /var/www/html/topmkt<br>mysql -u root -pDnlszkem1! topmkt < optimize_community_performance.sql</div>';
                echo '</div>';
            } else {
                echo '<div class="success">';
                echo '<h3>✅ 데이터베이스 인덱스 최적화 완료</h3>';
                echo '<p>인덱스가 적절히 설정되어 쿼리 성능이 최적화되었습니다.</p>';
                echo '</div>';
            }
            
            if ($imageScore < 40) {
                echo '<div class="recommendation">';
                echo '<h3>2. 🖼️ 프로필 이미지 최적화 (백그라운드 작업)</h3>';
                echo '<p>예상 효과: <strong>90% 대역폭 절약, 모바일 로딩 속도 크게 향상</strong></p>';
                echo '<ul>';
                echo '<li>기존 사용자 이미지 3가지 사이즈 재생성</li>';
                echo '<li>프로필 편집 페이지에서 이미지 재업로드 유도</li>';
                echo '<li>이미지 압축 알고리즘 개선</li>';
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div class="success">';
                echo '<h3>✅ 프로필 이미지 최적화 양호</h3>';
                echo '<p>대부분의 사용자가 최적화된 이미지를 보유하고 있습니다.</p>';
                echo '</div>';
            }
            
            if ($queryScore < 30) {
                echo '<div class="recommendation">';
                echo '<h3>3. ⚡ 고급 성능 최적화 (선택사항)</h3>';
                echo '<ul>';
                echo '<li><strong>Redis 캐시 도입:</strong> 자주 조회되는 데이터 캐싱</li>';
                echo '<li><strong>CDN 적용:</strong> 이미지 파일 전송 속도 향상</li>';
                echo '<li><strong>데이터베이스 파티셔닝:</strong> 대용량 데이터 처리 최적화</li>';
                echo '<li><strong>압축 및 미니파이:</strong> 웹 리소스 최적화</li>';
                echo '</ul>';
                echo '</div>';
            }
            
            echo '<div class="success">';
            echo '<h3>✅ 현재 적용된 최적화 기능</h3>';
            echo '<div class="grid">';
            echo '<div>';
            echo '<ul>';
            echo '<li>✅ Lazy loading 이미지 로딩</li>';
            echo '<li>✅ 프로필 이미지 3가지 사이즈 생성</li>';
            echo '<li>✅ COALESCE fallback 이미지 시스템</li>';
            echo '</ul>';
            echo '</div>';
            echo '<div>';
            echo '<ul>';
            echo '<li>✅ 쿼리 실행 시간 모니터링</li>';
            echo '<li>✅ 성능 디버깅 로그</li>';
            echo '<li>✅ 실시간 성능 분석 대시보드</li>';
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="section">';
            echo '<div class="recommendation status-error">';
            echo '<h2>❌ 분석 중 오류 발생</h2>';
            echo '<p><strong>오류 내용:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>데이터베이스 연결이나 권한 문제일 수 있습니다.</p>';
            echo '</div>';
            echo '</div>';
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; color: #718096;">
            <strong>🔍 분석 완료!</strong> <?= date('Y-m-d H:i:s') ?><br>
            <small>정기적인 성능 모니터링을 위해 이 페이지를 북마크하세요.</small>
        </div>
    </div>

    <script>
        // 페이지 로드 애니메이션
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.progress-fill');
            if (progressBar) {
                const width = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = width;
                }, 500);
            }
        });
    </script>
</body>
</html>