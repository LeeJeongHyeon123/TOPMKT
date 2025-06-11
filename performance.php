<?php
/**
 * 커뮤니티 성능 분석 페이지
 * URL: https://www.topmktx.com/performance.php
 */

// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'topmkt';
$username = 'root';
$password = 'Dnlszkem1!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $db_error = "데이터베이스 연결 실패: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>커뮤니티 성능 분석 - 탑마케팅</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { 
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { 
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .content { padding: 30px; }
        .section { 
            margin-bottom: 30px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        .section h2 { 
            color: #2d3748;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .metric:last-child { border-bottom: none; }
        .metric .label { 
            font-weight: 600;
            color: #4a5568;
        }
        .metric .value { 
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }
        .status-good { color: #38a169; }
        .status-warning { color: #d69e2e; }
        .status-error { color: #e53e3e; }
        .progress-container {
            margin: 20px 0;
            text-align: center;
        }
        .progress-bar { 
            width: 100%;
            height: 30px;
            background: #e2e8f0;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            margin: 10px 0;
        }
        .progress-fill { 
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 2s ease;
        }
        .grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .recommendation {
            background: #fff5f5;
            border: 2px solid #feb2b2;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
        }
        .success {
            background: #f0fff4;
            border: 2px solid #9ae6b4;
        }
        .code {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 10px;
        }
        .badge-good { background: #38a169; color: white; }
        .badge-warning { background: #d69e2e; color: white; }
        .badge-error { background: #e53e3e; color: white; }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 커뮤니티 성능 분석</h1>
            <p>실시간 성능 모니터링 및 최적화 현황</p>
        </div>
        
        <div class="content">
            <?php if (isset($db_error)): ?>
                <div class="recommendation status-error">
                    <h2>❌ 데이터베이스 연결 오류</h2>
                    <p><?= htmlspecialchars($db_error) ?></p>
                </div>
            <?php else: ?>
                <?php
                try {
                    // 1. 기본 통계
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
                    $totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
                    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE profile_image_thumb IS NOT NULL");
                    $usersWithImages = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $imageOptimizationRate = ($usersWithImages / max($totalUsers, 1)) * 100;
                ?>
                
                <div class="section">
                    <h2>📊 데이터 현황</h2>
                    <div class="grid">
                        <div class="card">
                            <div class="metric">
                                <span class="label">📝 총 게시글</span>
                                <span class="value"><?= number_format($totalPosts) ?>개</span>
                            </div>
                            <div class="metric">
                                <span class="label">👥 활성 사용자</span>
                                <span class="value"><?= number_format($totalUsers) ?>명</span>
                            </div>
                        </div>
                        <div class="card">
                            <div class="metric">
                                <span class="label">🖼️ 프로필 이미지 보유자</span>
                                <span class="value"><?= number_format($usersWithImages) ?>명</span>
                            </div>
                            <div class="metric">
                                <span class="label">📈 이미지 최적화율</span>
                                <span class="value <?= $imageOptimizationRate > 50 ? 'status-good' : 'status-warning' ?>">
                                    <?= round($imageOptimizationRate, 1) ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                // 2. 인덱스 확인
                $stmt = $pdo->query("
                    SELECT INDEX_NAME, COLUMN_NAME, CARDINALITY
                    FROM information_schema.STATISTICS 
                    WHERE TABLE_SCHEMA = 'topmkt' 
                      AND TABLE_NAME = 'posts'
                      AND INDEX_NAME != 'PRIMARY'
                    ORDER BY INDEX_NAME
                ");
                $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $indexScore = empty($indexes) ? 0 : 30;
                ?>

				<div class="section">
                    <h2>🔍 데이터베이스 최적화</h2>
                    <?php if (empty($indexes)): ?>
                        <div class="recommendation">
                            <h3>⚠️ 성능 인덱스 부족</h3>
                            <p><span class="badge badge-error">긴급</span> 쿼리 성능 향상을 위해 인덱스 생성이 필요합니다.</p>
                            <div class="code">mysql -u root -pDnlszkem1! topmkt < optimize_community_performance.sql</div>
                        </div>
                    <?php else: ?>
                        <div class="success">
                            <h3>✅ 인덱스 최적화 완료</h3>
                            <p>총 <?= count($indexes) ?>개의 성능 인덱스가 설정되어 있습니다.</p>
                            <?php foreach ($indexes as $index): ?>
                                <div class="metric">
                                    <span class="label"><?= $index['INDEX_NAME'] ?></span>
                                    <span class="value"><?= $index['COLUMN_NAME'] ?> (<?= number_format($index['CARDINALITY']) ?>)</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                // 3. 쿼리 성능 테스트
                $startTime = microtime(true);
                $stmt = $pdo->prepare("
                    SELECT 
                        p.id, p.title, p.created_at,
                        u.nickname,
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
                
                $queryScore = $queryTime < 50 ? 30 : ($queryTime < 100 ? 15 : 0);
                $imageScore = $imageOptimizationRate > 90 ? 40 : ($imageOptimizationRate > 50 ? 20 : 0);
                $totalScore = $indexScore + $imageScore + $queryScore;
                ?>

                <div class="section">
                    <h2>⚡ 실시간 성능 테스트</h2>
                    <div class="grid">
                        <div class="card">
                            <div class="metric">
                                <span class="label">첫 페이지 로딩</span>
                                <span class="value <?= $queryTime < 50 ? 'status-good' : ($queryTime < 100 ? 'status-warning' : 'status-error') ?>">
                                    <?= round($queryTime, 2) ?>ms
                                    <?php if ($queryTime < 50): ?>
                                        <span class="badge badge-good">우수</span>
                                    <?php elseif ($queryTime < 100): ?>
                                        <span class="badge badge-warning">보통</span>
                                    <?php else: ?>
                                        <span class="badge badge-error">개선필요</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="metric">
                                <span class="label">조회된 게시글</span>
                                <span class="value"><?= count($results) ?>개</span>
                            </div>
                        </div>
                        <div class="card">
                            <h3>🎯 성능 기준</h3>
                            <div class="metric">
                                <span class="label">50ms 미만</span>
                                <span class="badge badge-good">우수</span>
                            </div>
                            <div class="metric">
                                <span class="label">50-100ms</span>
                                <span class="badge badge-warning">보통</span>
                            </div>
                            <div class="metric">
                                <span class="label">100ms 이상</span>
                                <span class="badge badge-error">개선필요</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>🏆 종합 성능 평가</h2>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $totalScore ?>%" id="progressBar">
                                <?= $totalScore ?>점
                            </div>
                        </div>
                        <h3>총 최적화 점수: <?= $totalScore ?>/100점</h3>
                    </div>
                    
                    <div class="grid">
                        <div class="card">
                            <h3>📊 점수 상세</h3>
                            <div class="metric">
                                <span class="label">데이터베이스 인덱스</span>
                                <span class="value"><?= $indexScore ?>/30점</span>
                            </div>
                            <div class="metric">
                                <span class="label">이미지 최적화</span>
                                <span class="value"><?= $imageScore ?>/40점</span>
                            </div>
                            <div class="metric">
                                <span class="label">쿼리 성능</span>
                                <span class="value"><?= $queryScore ?>/30점</span>
                            </div>
                        </div>
                        <div class="card">
                            <h3>📋 평가 결과</h3>
                            <?php if ($totalScore >= 80): ?>
                                <div class="success">
                                    <strong>🎉 성능 최적화 상태가 우수합니다!</strong><br>
                                    현재 상태를 유지하며 정기적인 모니터링을 권장합니다.
                                </div>
                            <?php elseif ($totalScore >= 50): ?>
                                <div class="recommendation">
                                    <strong>📈 성능 최적화가 진행 중입니다.</strong><br>
                                    아래 권장사항을 적용하여 추가 개선하세요.
                                </div>
                            <?php else: ?>
                                <div class="recommendation status-error">
                                    <strong>🚨 성능 최적화가 시급히 필요합니다!</strong><br>
                                    사용자 경험 향상을 위해 즉시 최적화 작업을 진행하세요.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>🔧 개선 방안</h2>
                    
                    <?php if ($indexScore == 0): ?>
                        <div class="recommendation">
                            <h3>1. 🚀 데이터베이스 인덱스 추가 (즉시 실행 가능)</h3>
                            <p><strong>예상 성능 향상: 50-70% 로딩 시간 단축</strong></p>
                            <div class="code">
cd /var/www/html/topmkt<br>
mysql -u root -pDnlszkem1! topmkt < optimize_community_performance.sql
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="success">
                            <h3>✅ 데이터베이스 인덱스 최적화 완료</h3>
                            <p>인덱스가 적절히 설정되어 쿼리 성능이 최적화되었습니다.</p>
                        </div>
                    <?php endif; ?>

                    <?php if ($imageScore < 40): ?>
                        <div class="recommendation">
                            <h3>2. 🖼️ 프로필 이미지 최적화</h3>
                            <p><strong>예상 효과: 90% 대역폭 절약, 모바일 로딩 속도 크게 향상</strong></p>
                            <ul>
                                <li>기존 사용자 이미지 3가지 사이즈 재생성</li>
                                <li>프로필 편집 페이지에서 이미지 재업로드 유도</li>
                                <li>이미지 압축 알고리즘 개선</li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="success">
                            <h3>✅ 프로필 이미지 최적화 양호</h3>
                            <p>대부분의 사용자가 최적화된 이미지를 보유하고 있습니다.</p>
                        </div>
                    <?php endif; ?>

                    <div class="success">
                        <h3>✅ 현재 적용된 최적화 기능</h3>
                        <div class="grid">
                            <div>
                                <ul>
                                    <li>✅ Lazy loading 이미지 로딩</li>
                                    <li>✅ 프로필 이미지 3가지 사이즈 생성</li>
                                    <li>✅ COALESCE fallback 이미지</li>
                                </ul>
                            </div>
                            <div>
                                <ul>
                                    <li>✅ 쿼리 실행 시간 모니터링</li>
                                    <li>✅ 성능 디버깅 로그</li>
                                    <li>✅ 실시간 성능 분석</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                } catch (Exception $e) {
                    echo '<div class="recommendation status-error">';
                    echo '<h2>❌ 분석 중 오류 발생</h2>';
                    echo '<p><strong>오류 내용:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                }
                ?>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <strong>🔍 분석 완료!</strong> <?= date('Y-m-d H:i:s') ?><br>
            <small>정기적인 성능 모니터링을 위해 이 페이지를 북마크하세요.</small>
        </div>
    </div>

    <script>
        // 페이지 로드 애니메이션
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.getElementById('progressBar');
            if (progressBar) {
                const targetWidth = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = targetWidth;
                }, 500);
            }
        });
    </script>
</body>
</html>