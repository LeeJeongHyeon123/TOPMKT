<?php
/**
 * 기업 신청 관리 대시보드 메인 페이지
 */
?>

<style>
/* 대시보드 전용 스타일 */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.dashboard-header {
    margin: 80px 0 40px 0;
    text-align: center;
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}

.dashboard-subtitle {
    font-size: 1.1rem;
    color: #718096;
    margin-bottom: 32px;
}

/* 통계 카드 */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-icon {
    font-size: 2rem;
    padding: 12px;
    border-radius: 12px;
    color: white;
}

.stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.success { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
.stat-icon.warning { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
.stat-icon.danger { background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); }

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.9rem;
    color: #718096;
    font-weight: 500;
}

/* 강의 목록 섹션 */
.section {
    margin-bottom: 40px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 12px;
}

.lecture-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.lecture-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.lecture-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.lecture-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 24px;
}

.lecture-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 8px;
    line-height: 1.3;
}

.lecture-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.lecture-body {
    padding: 24px;
}

.lecture-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.lecture-stat {
    text-align: center;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
}

.lecture-stat-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 4px;
}

.lecture-stat-label {
    font-size: 0.8rem;
    color: #718096;
}

.lecture-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
    transform: translateY(-1px);
}

.btn-outline {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #4a5568;
}

.btn-outline:hover {
    background: #f8fafc;
    border-color: #cbd5e0;
}

/* 최근 신청 목록 */
.registrations-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: #f8fafc;
}

.table th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
    border-bottom: 1px solid #e2e8f0;
}

.table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
}

.table tbody tr:hover {
    background: #f8fafc;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    min-width: 80px;
    display: inline-block;
}

.status-pending { background: #fed7d7; color: #c53030; }
.status-approved { background: #c6f6d5; color: #2d3748; }
.status-rejected { background: #fed7d7; color: #c53030; }
.status-waiting { background: #bee3f8; color: #2b6cb0; }

/* 반응형 디자인 */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .lecture-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .lecture-stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .lecture-actions {
        flex-direction: column;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}
</style>

<div class="dashboard-container">
    <!-- 대시보드 헤더 -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            📊 신청 관리 대시보드
        </h1>
        <p class="dashboard-subtitle">
            강의 신청 현황을 한눈에 확인하고 효율적으로 관리하세요
        </p>
    </div>
    
    <!-- 통계 카드 -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">📚</div>
            </div>
            <div class="stat-value"><?= number_format($stats['total_lectures']) ?></div>
            <div class="stat-label">등록된 강의</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">✅</div>
            </div>
            <div class="stat-value"><?= number_format($stats['approved_applications']) ?></div>
            <div class="stat-label">승인된 신청</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon warning">⏳</div>
            </div>
            <div class="stat-value"><?= number_format($stats['pending_applications']) ?></div>
            <div class="stat-label">대기중인 신청</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">📊</div>
            </div>
            <div class="stat-value"><?= number_format($stats['total_applications']) ?></div>
            <div class="stat-label">전체 신청</div>
        </div>
    </div>
    
    <!-- 내 강의 목록 -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">
                🎯 최근 강의 목록
            </h2>
            <a href="/lectures" class="btn btn-outline">
                📚 모든 강의 보기
            </a>
        </div>
        
        <?php if (empty($lectures)): ?>
            <div style="text-align: center; padding: 60px 20px; color: #718096;">
                <div style="font-size: 3rem; margin-bottom: 16px;">📚</div>
                <h3 style="margin-bottom: 8px;">등록된 강의가 없습니다</h3>
                <p>새로운 강의를 등록하여 참가자들을 모집해보세요!</p>
                <a href="/lectures/create" class="btn btn-primary" style="margin-top: 20px;">
                    ➕ 강의 등록하기
                </a>
            </div>
        <?php else: ?>
            <div class="lecture-grid">
                <?php foreach ($lectures as $lecture): ?>
                    <div class="lecture-card">
                        <div class="lecture-header">
                            <div class="lecture-title">
                                <?= htmlspecialchars($lecture['title']) ?>
                            </div>
                            <div class="lecture-meta">
                                <span>📅 <?= date('Y-m-d H:i', strtotime($lecture['start_date'] . ' ' . $lecture['start_time'])) ?></span>
                                <?php if ($lecture['max_participants']): ?>
                                    <span>👥 <?= $lecture['current_participants'] ?>/<?= $lecture['max_participants'] ?>명</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="lecture-body">
                            <div class="lecture-stats">
                                <div class="lecture-stat">
                                    <div class="lecture-stat-value"><?= number_format($lecture['total_applications'] ?? 0) ?></div>
                                    <div class="lecture-stat-label">전체 신청</div>
                                </div>
                                <div class="lecture-stat">
                                    <div class="lecture-stat-value"><?= number_format($lecture['pending_count'] ?? 0) ?></div>
                                    <div class="lecture-stat-label">대기중</div>
                                </div>
                                <div class="lecture-stat">
                                    <div class="lecture-stat-value"><?= number_format($lecture['approved_count'] ?? 0) ?></div>
                                    <div class="lecture-stat-label">승인됨</div>
                                </div>
                            </div>
                            
                            <div class="lecture-actions">
                                <a href="/registrations/lectures/<?= $lecture['id'] ?>" class="btn btn-primary">
                                    👥 신청자 관리
                                </a>
                                <a href="/lectures/<?= $lecture['id'] ?>" class="btn btn-outline">
                                    📋 강의 상세
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- 최근 신청 목록 -->
    <?php if (!empty($recentRegistrations)): ?>
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">
                    ⏰ 최근 신청 현황
                </h2>
            </div>
            
            <div class="registrations-table">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>신청자</th>
                                <th>강의명</th>
                                <th>상태</th>
                                <th>신청일</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRegistrations as $registration): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($registration['participant_name']) ?></strong>
                                            <div style="font-size: 0.8rem; color: #718096;">
                                                <?= htmlspecialchars($registration['participant_email']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/lectures/<?= $registration['lecture_id'] ?>" 
                                           style="color: #667eea; text-decoration: none;">
                                            <?= htmlspecialchars($registration['lecture_title']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $registration['status'] ?>">
                                            <?= [
                                                'pending' => '⏳ 대기중',
                                                'approved' => '✅ 승인됨',
                                                'rejected' => '❌ 거절됨',
                                                'waiting' => '⏰ 대기자'
                                            ][$registration['status']] ?? $registration['status'] ?>
                                        </span>
                                        <?php if ($registration['is_waiting_list']): ?>
                                            <small style="color: #718096;">(<?= $registration['waiting_order'] ?>번째)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('Y-m-d H:i', strtotime($registration['created_at'])) ?>
                                    </td>
                                    <td>
                                        <a href="/registrations/lectures/<?= $registration['lecture_id'] ?>" 
                                           class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">
                                            관리
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>