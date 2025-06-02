<?php
/**
 * 탑마케팅 메인 페이지 - 모던 리디자인
 */
$page_title = '홈';
$page_description = '글로벌 네트워크 마케팅 리더들의 커뮤니티 - 성공을 함께 만들어가세요';
$current_page = 'home';

require_once SRC_PATH . '/views/templates/header.php';
?>

<!-- 1. 히어로 섹션 - 트렌디한 그라디언트 배경 -->
<section class="hero-section modern-hero">
    <div class="hero-background">
        <div class="gradient-overlay"></div>
        <div class="animated-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-icon">🚀</span>
                <span class="badge-text">네트워크 마케팅의 새로운 패러다임</span>
            </div>
            <h1 class="hero-title">
                <span class="gradient-text">글로벌 리더들과 함께</span><br>
                <span class="typing-effect">성공을 만들어가세요</span>
            </h1>
            <p class="hero-description">
                전 세계 네트워크 마케팅 전문가들이 모인 커뮤니티에서<br>
                지식을 공유하고, 인사이트를 얻으며, 함께 성장하세요
            </p>
            <div class="hero-actions">
                <a href="/auth/signup" class="btn btn-primary-gradient">
                    <span>무료로 시작하기</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#features" class="btn btn-ghost">
                    <i class="fas fa-play"></i>
                    <span>둘러보기</span>
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= number_format($totalMembers ?? 0) ?>+</span>
                    <span class="stat-label">활성 멤버</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= number_format($totalPosts ?? 0) ?>+</span>
                    <span class="stat-label">콘텐츠</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= number_format($totalEvents ?? 0) ?>+</span>
                    <span class="stat-label">행사/강의</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. 핵심 기능 섹션 -->
<section id="features" class="features-section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">핵심 기능</span>
            <h2 class="section-title">탑마케팅이 제공하는 가치</h2>
            <p class="section-subtitle">성공적인 네트워크 마케팅을 위한 모든 도구가 여기에</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card featured">
                <div class="feature-icon">
                    <div class="icon-bg">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h3>커뮤니티 네트워킹</h3>
                <p>전 세계 네트워크 마케팅 전문가들과 연결되어 경험과 노하우를 공유하세요</p>
                <a href="/posts" class="feature-link">
                    <span>시작하기</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <div class="icon-bg purple">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <h3>행사 참여</h3>
                <p>다양한 네트워킹 행사와 컨퍼런스에 참여하여 새로운 기회를 만나보세요</p>
                <a href="/events" class="feature-link">
                    <span>둘러보기</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <div class="icon-bg green">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <h3>전문 강의</h3>
                <p>업계 전문가들의 실전 강의를 통해 실무 역량을 키워보세요</p>
                <a href="/lectures" class="feature-link">
                    <span>학습하기</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 3. 실시간 활동 대시보드 -->
<section class="activity-dashboard">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">실시간 업데이트</span>
            <h2 class="section-title">지금 이 순간 일어나는 일들</h2>
        </div>
        
        <div class="dashboard-grid">
            <!-- 인기 게시글 -->
            <div class="dashboard-card trending-posts">
                <div class="card-header">
                    <h3><i class="fas fa-fire"></i> 인기 게시글</h3>
                    <a href="/posts" class="view-all">전체보기</a>
                </div>
                <div class="posts-container">
                    <?php if (!empty($popularPosts)): ?>
                        <?php foreach (array_slice($popularPosts, 0, 3) as $post): ?>
                            <div class="post-item">
                                <div class="post-content">
                                    <h4><a href="/posts/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h4>
                                    <p><?= htmlspecialchars(substr($post['content'], 0, 80)) ?>...</p>
                                    <div class="post-meta">
                                        <span class="author">
                                            <img src="/assets/uploads/profiles/<?= $post['author_image'] ?? 'default.jpg' ?>" alt="">
                                            <?= htmlspecialchars($post['author']) ?>
                                        </span>
                                        <span class="reactions">
                                            <i class="fas fa-heart"></i> <?= $post['likes'] ?? 0 ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <p>아직 게시글이 없습니다</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 활성 멤버 -->
            <div class="dashboard-card active-members">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> 이주의 활성 멤버</h3>
                </div>
                <div class="members-grid">
                    <?php if (!empty($topMembers)): ?>
                        <?php foreach (array_slice($topMembers, 0, 6) as $index => $member): ?>
                            <div class="member-item">
                                <div class="member-rank">#<?= $index + 1 ?></div>
                                <img src="/assets/uploads/profiles/<?= $member['profile_image'] ?? 'default.jpg' ?>" alt="<?= htmlspecialchars($member['name']) ?>">
                                <div class="member-info">
                                    <h5><?= htmlspecialchars($member['name']) ?></h5>
                                    <span class="activity-score"><?= $member['activity_score'] ?? 0 ?>P</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>활성 멤버 정보가 없습니다</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. 행사 일정 섹션 -->
<section class="events-section modern-section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge events">🎪 행사</span>
            <h2 class="section-title">다가오는 네트워킹 행사</h2>
            <p class="section-subtitle">업계 리더들과 만날 수 있는 특별한 기회들</p>
        </div>
        
        <div class="events-grid">
            <?php if (!empty($upcomingEvents)): ?>
                <?php foreach (array_slice($upcomingEvents, 0, 3) as $event): ?>
                    <div class="event-card modern-card">
                        <?php if (!empty($event['image'])): ?>
                            <div class="event-image">
                                <img src="/assets/uploads/events/<?= $event['image'] ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                                <div class="image-overlay">
                                    <span class="event-type">네트워킹 행사</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="event-image placeholder">
                                <div class="placeholder-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="image-overlay">
                                    <span class="event-type">네트워킹 행사</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="event-content">
                            <div class="event-date-badge">
                                <span class="month"><?= date('M', strtotime($event['date'])) ?></span>
                                <span class="day"><?= date('d', strtotime($event['date'])) ?></span>
                            </div>
                            
                            <div class="event-details">
                                <h3><?= htmlspecialchars($event['title']) ?></h3>
                                <p class="event-description"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
                                
                                <div class="event-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?= htmlspecialchars($event['location']) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?= date('H:i', strtotime($event['time'])) ?></span>
                                    </div>
                                    <?php if (isset($event['organizer'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-user-tie"></i>
                                            <span><?= htmlspecialchars($event['organizer']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="event-actions">
                                    <a href="/events/<?= $event['id'] ?>" class="btn btn-primary-outline">
                                        <span>자세히 보기</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <?php if (isset($event['participants_count'])): ?>
                                        <span class="participants">
                                            <i class="fas fa-users"></i>
                                            <?= $event['participants_count'] ?>명 참여
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state-card">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h3>예정된 행사가 없습니다</h3>
                    <p>곧 멋진 네트워킹 행사들이 준비될 예정입니다</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-footer">
            <a href="/events" class="btn btn-secondary-outline">
                <span>모든 행사 보기</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- 5. 강의 일정 섹션 -->
<section class="lectures-section modern-section alt-bg">
    <div class="container">
        <div class="section-header">
            <span class="section-badge lectures">📚 강의</span>
            <h2 class="section-title">전문가 강의 프로그램</h2>
            <p class="section-subtitle">실무 전문가들의 생생한 노하우를 배워보세요</p>
        </div>
        
        <div class="lectures-grid">
            <?php if (!empty($upcomingLectures)): ?>
                <?php foreach (array_slice($upcomingLectures, 0, 3) as $lecture): ?>
                    <div class="lecture-card modern-card">
                        <?php if (!empty($lecture['image'])): ?>
                            <div class="lecture-image">
                                <img src="/assets/uploads/lectures/<?= $lecture['image'] ?>" alt="<?= htmlspecialchars($lecture['title']) ?>">
                                <div class="image-overlay">
                                    <span class="lecture-type">전문 강의</span>
                                    <?php if (isset($lecture['level'])): ?>
                                        <span class="lecture-level"><?= htmlspecialchars($lecture['level']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="lecture-image placeholder">
                                <div class="placeholder-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="image-overlay">
                                    <span class="lecture-type">전문 강의</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="lecture-content">
                            <div class="lecture-date-badge">
                                <span class="month"><?= date('M', strtotime($lecture['date'])) ?></span>
                                <span class="day"><?= date('d', strtotime($lecture['date'])) ?></span>
                            </div>
                            
                            <div class="lecture-details">
                                <h3><?= htmlspecialchars($lecture['title']) ?></h3>
                                
                                <div class="lecturer-info">
                                    <div class="lecturer-avatar">
                                        <?php if (!empty($lecture['lecturer_image'])): ?>
                                            <img src="/assets/uploads/profiles/<?= $lecture['lecturer_image'] ?>" alt="<?= htmlspecialchars($lecture['lecturer']) ?>">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lecturer-details">
                                        <span class="lecturer-name"><?= htmlspecialchars($lecture['lecturer']) ?></span>
                                        <?php if (isset($lecture['lecturer_title'])): ?>
                                            <span class="lecturer-title"><?= htmlspecialchars($lecture['lecturer_title']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <p class="lecture-description"><?= htmlspecialchars(substr($lecture['description'], 0, 100)) ?>...</p>
                                
                                <div class="lecture-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?= htmlspecialchars($lecture['location']) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?= date('H:i', strtotime($lecture['time'])) ?></span>
                                    </div>
                                    <?php if (isset($lecture['duration'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-hourglass-half"></i>
                                            <span><?= $lecture['duration'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="lecture-footer">
                                    <div class="price-info">
                                        <?php if (isset($lecture['price']) && $lecture['price'] > 0): ?>
                                            <span class="price">
                                                <i class="fas fa-tag"></i>
                                                <?= number_format($lecture['price']) ?>원
                                            </span>
                                        <?php else: ?>
                                            <span class="price free">
                                                <i class="fas fa-gift"></i>
                                                무료
                                            </span>
                                        <?php endif; ?>
                                        <?php if (isset($lecture['participants_count'])): ?>
                                            <span class="participants">
                                                <i class="fas fa-users"></i>
                                                <?= $lecture['participants_count'] ?>명 수강
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <a href="/lectures/<?= $lecture['id'] ?>" class="btn btn-primary-gradient">
                                        <span>수강 신청</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state-card">
                    <div class="empty-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>예정된 강의가 없습니다</h3>
                    <p>전문가들의 유익한 강의들이 곧 준비될 예정입니다</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-footer">
            <a href="/lectures" class="btn btn-secondary-outline">
                <span>모든 강의 보기</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- 6. CTA 섹션 -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <h2>성공의 여정을 함께 시작하세요</h2>
                <p>전 세계 네트워크 마케팅 리더들과 연결되어 새로운 기회를 발견하고 성공을 만들어가세요</p>
                <ul class="cta-benefits">
                    <li><i class="fas fa-check"></i> 무료 회원가입 및 기본 기능 이용</li>
                    <li><i class="fas fa-check"></i> 전문가 네트워크 액세스</li>
                    <li><i class="fas fa-check"></i> 독점 행사 및 강의 참여</li>
                </ul>
            </div>
            <div class="cta-actions">
                <a href="/auth/signup" class="btn btn-primary-gradient btn-large">
                    <span>지금 시작하기</span>
                    <i class="fas fa-rocket"></i>
                </a>
                <p class="cta-note">가입은 무료이며, 언제든지 탈퇴 가능합니다</p>
            </div>
        </div>
    </div>
</section>

<?php require_once SRC_PATH . '/views/templates/footer.php'; ?> 