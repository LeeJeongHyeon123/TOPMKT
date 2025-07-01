<?php
/**
 * 강의별 신청자 관리 페이지
 */
?>

<style>
/* 강의 신청자 관리 페이지 스타일 */
.management-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.page-header {
    margin: 80px 0 40px 0;
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.breadcrumb {
    font-size: 0.9rem;
    color: #718096;
    margin-bottom: 16px;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
}

.page-meta {
    display: flex;
    align-items: center;
    gap: 24px;
    font-size: 0.9rem;
    color: #718096;
    margin-bottom: 24px;
}

.lecture-stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
}

.summary-stat {
    text-align: center;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
}

.summary-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 4px;
}

.summary-stat-label {
    font-size: 0.8rem;
    color: #718096;
}

/* 필터 및 검색 */
.controls-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.controls-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.controls-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d3748;
}

.controls-grid {
    display: grid;
    grid-template-columns: 200px 1fr auto;
    gap: 16px;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #4a5568;
}

.form-group select,
.form-group input {
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* 신청자 목록 테이블 */
.registrations-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.section-header {
    padding: 24px 28px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.table-container {
    overflow-x: auto;
}

.registrations-table {
    width: 100%;
    border-collapse: collapse;
}

.registrations-table th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.registrations-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
    vertical-align: top;
}

.registrations-table tbody tr:hover {
    background: #f8fafc;
}

.participant-info {
    min-width: 200px;
}

.participant-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.participant-contact {
    font-size: 0.8rem;
    color: #718096;
    margin-bottom: 2px;
}

.participant-company {
    font-size: 0.8rem;
    color: #4a5568;
    font-style: italic;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    min-width: 90px;
    display: inline-block;
}

.status-pending { background: #fed7d7; color: #c53030; }
.status-approved { background: #c6f6d5; color: #25543e; }
.status-rejected { background: #fed7d7; color: #c53030; }
.status-waiting { background: #bee3f8; color: #2b6cb0; }

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-approve {
    background: #48bb78;
    color: white;
}

.btn-approve:hover {
    background: #38a169;
}

.btn-reject {
    background: #f56565;
    color: white;
}

.btn-reject:hover {
    background: #e53e3e;
}

.btn-info {
    background: #4299e1;
    color: white;
}

.btn-info:hover {
    background: #3182ce;
}

/* 페이징 */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 24px;
    background: #f8fafc;
}

.pagination a,
.pagination span {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    text-decoration: none;
    color: #4a5568;
    font-size: 0.9rem;
}

.pagination a:hover {
    background: #f1f5f9;
}

.pagination .current {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

/* 모달 스타일 */
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 24px 28px 20px;
    border-bottom: 1px solid #e2e8f0;
}

.modal-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 24px;
    background: none;
    border: none;
    font-size: 24px;
    color: #718096;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.modal-close:hover {
    background: #f1f5f9;
    color: #2d3748;
}

.modal-body {
    padding: 24px 28px;
}

.modal-footer {
    padding: 16px 28px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    .management-container {
        padding: 16px;
    }
    
    .page-header {
        padding: 24px 20px;
    }
    
    .page-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .controls-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .registrations-table th,
    .registrations-table td {
        padding: 12px 16px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="management-container">
    <!-- 페이지 헤더 -->
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/registrations">📊 대시보드</a> / 강의 신청자 관리
        </div>
        
        <h1 class="page-title"><?= htmlspecialchars($lecture['title']) ?></h1>
        
        <div class="page-meta">
            <span>📅 <?= date('Y-m-d H:i', strtotime($lecture['start_date'] . ' ' . $lecture['start_time'])) ?></span>
            <span>⏰ <?= date('Y-m-d H:i', strtotime($lecture['end_date'] . ' ' . $lecture['end_time'])) ?></span>
            <?php if ($lecture['max_participants']): ?>
                <span>👥 정원 <?= $lecture['max_participants'] ?>명</span>
            <?php endif; ?>
            <span>⚙️ <?= $lecture['auto_approval'] ? '자동 승인' : '수동 승인' ?></span>
        </div>
        
        <?php if ($lectureStats): ?>
            <div class="lecture-stats-summary">
                <div class="summary-stat">
                    <div class="summary-stat-value"><?= number_format($lectureStats['total_applications']) ?></div>
                    <div class="summary-stat-label">전체 신청</div>
                </div>
                <div class="summary-stat">
                    <div class="summary-stat-value"><?= number_format($lectureStats['pending_count']) ?></div>
                    <div class="summary-stat-label">대기중</div>
                </div>
                <div class="summary-stat">
                    <div class="summary-stat-value"><?= number_format($lectureStats['approved_count']) ?></div>
                    <div class="summary-stat-label">승인됨</div>
                </div>
                <div class="summary-stat">
                    <div class="summary-stat-value"><?= number_format($lectureStats['rejected_count']) ?></div>
                    <div class="summary-stat-label">거절됨</div>
                </div>
                <?php if ($lectureStats['waiting_count'] > 0): ?>
                    <div class="summary-stat">
                        <div class="summary-stat-value"><?= number_format($lectureStats['waiting_count']) ?></div>
                        <div class="summary-stat-label">대기자</div>
                    </div>
                <?php endif; ?>
                <?php if ($lecture['max_participants']): ?>
                    <div class="summary-stat">
                        <div class="summary-stat-value"><?= number_format($lectureStats['capacity_percentage']) ?>%</div>
                        <div class="summary-stat-label">정원 비율</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- 필터 및 검색 -->
    <div class="controls-section">
        <div class="controls-header">
            <h2 class="controls-title">🔍 신청자 필터링</h2>
        </div>
        
        <form method="GET" action="">
            <div class="controls-grid">
                <div class="form-group">
                    <label for="status">상태</label>
                    <select name="status" id="status">
                        <option value="">전체</option>
                        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>⏳ 대기중</option>
                        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>✅ 승인됨</option>
                        <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>❌ 거절됨</option>
                        <option value="waiting" <?= $statusFilter === 'waiting' ? 'selected' : '' ?>>⏰ 대기자</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="search">검색</label>
                    <input type="text" name="search" id="search" 
                           value="<?= htmlspecialchars($searchQuery) ?>"
                           placeholder="이름, 이메일, 회사명으로 검색">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-approve">🔍 검색</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 신청자 목록 -->
    <div class="registrations-section">
        <div class="section-header">
            <h2 class="section-title">
                👥 신청자 목록 (총 <?= number_format($totalCount) ?>명)
            </h2>
        </div>
        
        <?php if (empty($registrations)): ?>
            <div style="text-align: center; padding: 60px 20px; color: #718096;">
                <div style="font-size: 3rem; margin-bottom: 16px;">📋</div>
                <h3 style="margin-bottom: 8px;">신청자가 없습니다</h3>
                <p>아직 이 강의에 신청한 사람이 없습니다.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="registrations-table">
                    <thead>
                        <tr>
                            <th>신청자 정보</th>
                            <th>소속</th>
                            <th>상태</th>
                            <th>신청일</th>
                            <th>처리일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $registration): ?>
                            <tr>
                                <td class="participant-info">
                                    <div class="participant-name">
                                        <?= htmlspecialchars($registration['participant_name']) ?>
                                    </div>
                                    <div class="participant-contact">
                                        📧 <?= htmlspecialchars($registration['participant_email']) ?>
                                    </div>
                                    <div class="participant-contact">
                                        📞 <?= htmlspecialchars($registration['participant_phone']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($registration['company_name']): ?>
                                        <div class="participant-company">
                                            🏢 <?= htmlspecialchars($registration['company_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($registration['position']): ?>
                                        <div class="participant-company">
                                            💼 <?= htmlspecialchars($registration['position']) ?>
                                        </div>
                                    <?php endif; ?>
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
                                        <br><small style="color: #718096;"><?= $registration['waiting_order'] ?>번째 대기</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('Y-m-d H:i', strtotime($registration['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($registration['processed_at']): ?>
                                        <?= date('Y-m-d H:i', strtotime($registration['processed_at'])) ?>
                                        <?php if ($registration['processed_by_name']): ?>
                                            <br><small style="color: #718096;">
                                                by <?= htmlspecialchars($registration['processed_by_name']) ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #718096;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($registration['status'] === 'pending' || $registration['status'] === 'waiting'): ?>
                                            <button class="btn btn-approve" 
                                                    onclick="showStatusModal(<?= $registration['id'] ?>, 'approved')">
                                                ✅ 승인
                                            </button>
                                            <button class="btn btn-reject" 
                                                    onclick="showStatusModal(<?= $registration['id'] ?>, 'rejected')">
                                                ❌ 거절
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-info" 
                                                onclick="showDetailModal(<?= htmlspecialchars(json_encode($registration)) ?>)">
                                            📋 상세
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 페이징 -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($searchQuery) ?>">
                            ⬅️ 이전
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($searchQuery) ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($searchQuery) ?>">
                            다음 ➡️
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- 상태 변경 모달 -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="statusModalTitle">신청 처리</h3>
            <button class="modal-close" onclick="closeStatusModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="statusForm">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="adminNotes">처리 사유 (선택사항)</label>
                    <textarea id="adminNotes" name="admin_notes" rows="4" 
                              style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; resize: vertical;"
                              placeholder="승인 또는 거절 사유를 입력해주세요..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closeStatusModal()" 
                    style="background: #e2e8f0; color: #4a5568;">
                취소
            </button>
            <button type="button" id="confirmStatusBtn" class="btn">
                확인
            </button>
        </div>
    </div>
</div>

<!-- 상세 정보 모달 -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">신청자 상세 정보</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="modal-body" id="detailModalBody">
            <!-- 동적으로 채워짐 -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closeDetailModal()" 
                    style="background: #e2e8f0; color: #4a5568;">
                닫기
            </button>
        </div>
    </div>
</div>

<script>
let currentRegistrationId = null;
let currentStatus = null;

// 상태 변경 모달 표시
function showStatusModal(registrationId, status) {
    currentRegistrationId = registrationId;
    currentStatus = status;
    
    const modal = document.getElementById('statusModal');
    const title = document.getElementById('statusModalTitle');
    const confirmBtn = document.getElementById('confirmStatusBtn');
    
    if (status === 'approved') {
        title.textContent = '신청 승인';
        confirmBtn.textContent = '✅ 승인하기';
        confirmBtn.className = 'btn btn-approve';
    } else {
        title.textContent = '신청 거절';
        confirmBtn.textContent = '❌ 거절하기';
        confirmBtn.className = 'btn btn-reject';
    }
    
    document.getElementById('adminNotes').value = '';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// 상태 변경 모달 닫기
function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    currentRegistrationId = null;
    currentStatus = null;
}

// 상세 정보 모달 표시
function showDetailModal(registration) {
    const modal = document.getElementById('detailModal');
    const body = document.getElementById('detailModalBody');
    
    body.innerHTML = `
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 12px;">👤 기본 정보</h4>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
                <p><strong>이름:</strong> ${escapeHtml(registration.participant_name)}</p>
                <p><strong>이메일:</strong> ${escapeHtml(registration.participant_email)}</p>
                <p><strong>연락처:</strong> ${escapeHtml(registration.participant_phone)}</p>
            </div>
        </div>
        
        ${registration.company_name || registration.position ? `
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 12px;">🏢 소속 정보</h4>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
                ${registration.company_name ? `<p><strong>회사명:</strong> ${escapeHtml(registration.company_name)}</p>` : ''}
                ${registration.position ? `<p><strong>직책:</strong> ${escapeHtml(registration.position)}</p>` : ''}
            </div>
        </div>
        ` : ''}
        
        ${registration.motivation ? `
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 12px;">💭 참가 동기</h4>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
                <p>${escapeHtml(registration.motivation).replace(/\n/g, '<br>')}</p>
            </div>
        </div>
        ` : ''}
        
        ${registration.special_requests ? `
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 12px;">📝 특별 요청사항</h4>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
                <p>${escapeHtml(registration.special_requests).replace(/\n/g, '<br>')}</p>
            </div>
        </div>
        ` : ''}
        
        ${registration.admin_notes ? `
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 12px;">📋 관리자 메모</h4>
            <div style="background: #fed7d7; padding: 16px; border-radius: 8px;">
                <p>${escapeHtml(registration.admin_notes).replace(/\n/g, '<br>')}</p>
            </div>
        </div>
        ` : ''}
    `;
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// 상세 정보 모달 닫기
function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// 상태 변경 확인
document.getElementById('confirmStatusBtn').addEventListener('click', async function() {
    if (!currentRegistrationId || !currentStatus) return;
    
    const adminNotes = document.getElementById('adminNotes').value.trim();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // 버튼 비활성화
    const button = this;
    const originalText = button.textContent;
    button.textContent = '🔄 처리 중...';
    button.disabled = true;
    
    try {
        const response = await fetch(`/api/registrations/${currentRegistrationId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: currentStatus,
                admin_notes: adminNotes,
                csrf_token: csrfToken
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            alert('✅ ' + result.message);
            location.reload(); // 페이지 새로고침
        } else {
            alert('❌ ' + result.message);
        }
        
    } catch (error) {
        console.error('상태 변경 오류:', error);
        alert('❌ 처리 중 오류가 발생했습니다.');
    } finally {
        button.textContent = originalText;
        button.disabled = false;
        closeStatusModal();
    }
});

// HTML 이스케이프 함수
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// 모달 외부 클릭 시 닫기
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusModal();
        closeDetailModal();
    }
});
</script>