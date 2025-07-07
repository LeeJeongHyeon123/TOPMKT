<?php
/**
 * 행사 일정 메인 페이지 (캘린더 뷰)
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/HtmlSanitizerHelper.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();

// 월 이름 배열
$monthNames = [
    1 => '1월', 2 => '2월', 3 => '3월', 4 => '4월', 5 => '5월', 6 => '6월',
    7 => '7월', 8 => '8월', 9 => '9월', 10 => '10월', 11 => '11월', 12 => '12월'
];
?>

<style>
/* 행사 일정 페이지 스타일 (파란색 테마) */
.events-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 30px 15px 20px 15px;
    min-height: calc(100vh - 200px);
    overflow-x: auto;
}

.events-header {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    padding: 40px 20px;
    text-align: center;
    margin-top: 60px;
    margin-bottom: 30px;
    border-radius: 12px;
    max-width: 1600px;
    margin-left: auto;
    margin-right: auto;
}

.events-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.events-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 20px;
}

.events-controls {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 30px;
    align-items: center;
}

.events-navigation {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
}

.month-nav {
    display: flex;
    align-items: center;
    gap: 15px;
    background: white;
    padding: 10px 20px;
    border-radius: 50px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nav-btn {
    background: #4A90E2;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}

.nav-btn:hover {
    background: #357ABD;
}

.current-month {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2E86AB;
    min-width: 120px;
    text-align: center;
}

.view-toggle {
    display: flex;
    background: white;
    border-radius: 50px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.view-btn {
    padding: 10px 20px;
    border: none;
    background: white;
    color: #666;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
}

.view-btn.active {
    background: #4A90E2;
    color: white;
}

.create-event-btn {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 50px;
    text-decoration: none !important;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.create-event-btn:link,
.create-event-btn:visited,
.create-event-btn:focus,
.create-event-btn:active {
    text-decoration: none !important;
}

.create-event-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
    color: white;
    text-decoration: none !important;
}

/* 캘린더 스타일 */
.calendar-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #f1f5f9;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #2E86AB;
    color: white;
}

.calendar-day-header {
    padding: 15px;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.calendar-day {
    background: white;
    min-height: 120px;
    padding: 8px;
    position: relative;
    transition: background 0.2s;
}

.calendar-day:hover {
    background: #f8fafc;
}

.calendar-day.other-month {
    background: #f8fafc;
    color: #94a3b8;
}

.calendar-day.today {
    background: #e0f2fe;
}

.calendar-day-number {
    font-weight: 600;
    margin-bottom: 5px;
    color: #1e293b;
}

.calendar-day.other-month .calendar-day-number {
    color: #94a3b8;
}

.event-item {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    padding: 4px 8px;
    margin-bottom: 2px;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: transform 0.2s;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.event-item:hover {
    transform: scale(1.02);
}


.more-events {
    color: #4A90E2;
    font-size: 0.7rem;
    cursor: pointer;
    text-align: center;
    padding: 2px;
    border-radius: 3px;
    background: #e0f2fe;
}

.more-events:hover {
    background: #b3e5fc;
}

/* 이벤트 모달 */
.event-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.event-modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.event-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
}

.event-modal-title {
    color: #2E86AB;
    font-size: 1.5rem;
    font-weight: 700;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
    padding: 5px;
}

.modal-close:hover {
    color: #2E86AB;
}

.event-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.event-list-item {
    padding: 15px;
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s;
}

.event-list-item:hover {
    background: #f8fafc;
}

.event-list-item:last-child {
    border-bottom: none;
}

.event-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 5px;
}

.event-details {
    color: #64748b;
    font-size: 0.9rem;
    display: flex;
    flex-direction: column;
    gap: 3px;
}


/* 반응형 */
@media (max-width: 768px) {
    .events-container {
        padding: 20px 10px;
    }
    
    .events-header {
        margin-top: 20px;
        padding: 30px 20px;
    }
    
    .events-header h1 {
        font-size: 2rem;
    }
    
    .events-controls {
        flex-direction: column;
        gap: 15px;
    }
    
    .events-navigation {
        flex-direction: column;
        gap: 15px;
    }
    
    .calendar-day {
        min-height: 80px;
        padding: 5px;
    }
    
    .calendar-day-number {
        font-size: 0.9rem;
    }
    
    .event-item {
        font-size: 0.7rem;
        padding: 3px 6px;
    }
}
</style>

<div class="events-container">
    <!-- 헤더 -->
    <div class="events-header">
        <h1>🎉 행사 일정</h1>
        <p>다양한 마케팅 행사와 네트워킹 행사에 참여하세요</p>
    </div>

    <!-- 컨트롤 영역 -->
    <div class="events-controls">
        <div class="events-navigation">
            <!-- 월 네비게이션 -->
            <div class="month-nav">
                <button class="nav-btn" onclick="navigateMonth(<?= $prev_month['year'] ?>, <?= $prev_month['month'] ?>)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="current-month">
                    <?= $year ?>년 <?= $monthNames[$month] ?>
                </div>
                <button class="nav-btn" onclick="navigateMonth(<?= $next_month['year'] ?>, <?= $next_month['month'] ?>)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- 뷰 토글 -->
            <div class="view-toggle">
                <button class="view-btn <?= $view === 'calendar' ? 'active' : '' ?>" 
                        onclick="toggleView('calendar')">
                    <i class="fas fa-calendar-alt"></i> 캘린더
                </button>
                <button class="view-btn <?= $view === 'list' ? 'active' : '' ?>" 
                        onclick="toggleView('list')">
                    <i class="fas fa-list"></i> 목록
                </button>
            </div>
        </div>

        <!-- 행사 등록 버튼 -->
        <?php if ($isLoggedIn): ?>
            <?php 
            // 기업회원 권한 확인
            require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
            $permission = CorporateMiddleware::checkLectureEventPermission();
            
            if ($permission['hasPermission']): ?>
                <a href="/events/create" class="create-event-btn">
                    <i class="fas fa-plus"></i>
                    새 행사 등록
                </a>
            <?php else: ?>
                <a href="/corp/info" class="create-event-btn" style="background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);" 
                   title="<?= htmlspecialchars($permission['message']) ?>">
                    <i class="fas fa-calendar-plus"></i>
                    행사 일정 등록
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="/auth/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="create-event-btn">
                <i class="fas fa-sign-in-alt"></i>
                로그인 후 등록
            </a>
        <?php endif; ?>
    </div>

    <!-- 캘린더 -->
    <div class="calendar-container">
        <!-- 요일 헤더 -->
        <div class="calendar-header">
            <div class="calendar-day-header">일</div>
            <div class="calendar-day-header">월</div>
            <div class="calendar-day-header">화</div>
            <div class="calendar-day-header">수</div>
            <div class="calendar-day-header">목</div>
            <div class="calendar-day-header">금</div>
            <div class="calendar-day-header">토</div>
        </div>

        <!-- 캘린더 그리드 -->
        <div class="calendar-grid">
            <?php foreach ($calendar_data as $week): ?>
                <?php foreach ($week as $day): ?>
                    <div class="calendar-day <?= $day['class'] ?>" data-date="<?= $day['date'] ?>">
                        <div class="calendar-day-number"><?= $day['day'] ?></div>
                        
                        <?php 
                        $dayEvents = array_filter($events, function($event) use ($day) {
                            return $event['start_date'] === $day['date'];
                        });
                        
                        $displayEvents = array_slice($dayEvents, 0, 3);
                        $remainingCount = count($dayEvents) - 3;
                        ?>
                        
                        <?php foreach ($displayEvents as $event): ?>
                            <div class="event-item" 
                                 onclick="showEventDetail(<?= $event['id'] ?>)"
                                 title="<?= htmlspecialchars($event['title']) ?>">
                                <?= htmlspecialchars(mb_substr($event['title'], 0, 15)) ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if ($remainingCount > 0): ?>
                            <div class="more-events" onclick="showDayEvents('<?= $day['date'] ?>')">
                                +<?= $remainingCount ?>개 더보기
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 이벤트 모달 -->
<div id="eventModal" class="event-modal">
    <div class="event-modal-content">
        <div class="event-modal-header">
            <h3 class="event-modal-title" id="modalTitle">행사 목록</h3>
            <button class="modal-close" onclick="closeEventModal()">&times;</button>
        </div>
        <ul id="modalEventList" class="event-list"></ul>
    </div>
</div>

<script>
// 월 네비게이션
function navigateMonth(year, month) {
    window.location.href = `/events?year=${year}&month=${month}&view=<?= $view ?>`;
}

// 뷰 전환
function toggleView(viewType) {
    window.location.href = `/events?year=<?= $year ?>&month=<?= $month ?>&view=${viewType}`;
}

// 행사 상세 보기
function showEventDetail(eventId) {
    window.location.href = `/events/detail?id=${eventId}`;
}

// 특정 날짜의 모든 행사 보기
function showDayEvents(date) {
    const events = <?= json_encode($events) ?>;
    const dayEvents = events.filter(event => event.start_date === date);
    
    if (dayEvents.length === 0) return;
    
    const modal = document.getElementById('eventModal');
    const title = document.getElementById('modalTitle');
    const list = document.getElementById('modalEventList');
    
    title.textContent = `${date} 행사 목록`;
    list.innerHTML = '';
    
    dayEvents.forEach(event => {
        const li = document.createElement('li');
        li.className = 'event-list-item';
        li.style.cursor = 'pointer';
        li.onclick = () => showEventDetail(event.id);
        
        li.innerHTML = `
            <div class="event-title">
                ${event.title}
            </div>
            <div class="event-details">
                <div><i class="fas fa-clock"></i> ${event.start_time}</div>
                <div><i class="fas fa-map-marker-alt"></i> ${event.location_type === 'online' ? '온라인' : event.venue_name || '오프라인'}</div>
                ${event.registration_fee ? `<div><i class="fas fa-won-sign"></i> ${event.registration_fee.toLocaleString()}원</div>` : ''}
            </div>
        `;
        
        list.appendChild(li);
    });
    
    modal.style.display = 'flex';
}

// 모달 닫기
function closeEventModal() {
    document.getElementById('eventModal').style.display = 'none';
}

// 모달 외부 클릭시 닫기
document.getElementById('eventModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEventModal();
    }
});

// 키보드 ESC로 모달 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEventModal();
    }
});
</script>