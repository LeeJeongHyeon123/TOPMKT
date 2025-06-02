        <!-- 성공/에러 메시지 표시 -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <div class="container">
                    <div class="message-content">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                        <button class="close-message" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- 히어로 섹션 -->
        <div class="hero-content">
            <div class="container">
                <div class="hero-text">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- 로그인된 사용자 환영 메시지 -->
                        <div class="welcome-back">
                            <div class="welcome-icon">
                                <i class="fas fa-hand-peace"></i>
                            </div>
                            <h1 class="hero-title">안녕하세요, <?= htmlspecialchars($_SESSION['username'] ?? '회원') ?>님!</h1>
                            <p class="hero-subtitle">
                                오늘도 탑마케팅과 함께 성공적인 하루를 만들어가세요.<br>
                                새로운 기회와 인사이트가 여러분을 기다리고 있습니다.
                            </p>
                            
                            <div class="user-quick-stats">
                                <div class="quick-stat">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number">
                                            <?php 
                                            $memberDays = isset($_SESSION['user_id']) ? 
                                                ceil((time() - strtotime('2024-01-01')) / 86400) : 0; // 임시 계산
                                            echo $memberDays;
                                            ?>
                                        </div>
                                        <div class="stat-label">일째 함께</div>
                                    </div>
                                </div>
                                <div class="quick-stat">
                                    <div class="stat-icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number"><?= ucfirst(strtolower($_SESSION['user_role'] ?? 'GENERAL')) ?></div>
                                        <div class="stat-label">등급</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hero-actions">
                                <a href="/community" class="btn btn-primary-gradient">
                                    <i class="fas fa-comments"></i>
                                    커뮤니티 참여하기
                                </a>
                                <a href="/profile" class="btn btn-outline-white">
                                    <i class="fas fa-user-cog"></i>
                                    프로필 관리
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- 비로그인 사용자 -->
                        <h1 class="hero-title">글로벌 네트워크 마케팅의 새로운 시작</h1>
                        <p class="hero-subtitle">
                            전 세계 마케팅 전문가들과 함께 성장하고, 무한한 가능성을 발견하세요.<br>
                            탑마케팅과 함께라면 성공은 선택이 아닌 필연입니다.
                        </p>
                        
                        <div class="hero-actions">
                            <a href="/auth/signup" class="btn btn-primary-gradient">
                                <i class="fas fa-rocket"></i>
                                무료로 시작하기
                            </a>
                            <a href="/auth/login" class="btn btn-outline-white">
                                <i class="fas fa-sign-in-alt"></i>
                                로그인
                            </a>
                        </div>
                        
                        <div class="hero-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>무료 가입</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>전문가 네트워킹</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>실시간 교육</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="hero-visual">
                    <div class="floating-cards">
                        <div class="card card-1">
                            <i class="fas fa-chart-line"></i>
                            <span>성장 분석</span>
                        </div>
                        <div class="card card-2">
                            <i class="fas fa-users"></i>
                            <span>네트워킹</span>
                        </div>
                        <div class="card card-3">
                            <i class="fas fa-graduation-cap"></i>
                            <span>전문 교육</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- 성공/에러 메시지 스타일 -->
    <style>
        .success-message {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 0;
            position: relative;
            animation: slideInDown 0.5s ease-out;
        }
        
        .message-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
        }
        
        .close-message {
            position: absolute;
            right: 0;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .close-message:hover {
            opacity: 1;
        }
        
        /* 환영 메시지 스타일 */
        .welcome-back {
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .welcome-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            animation: wave 2s ease-in-out infinite;
        }
        
        .user-quick-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .quick-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-icon {
            font-size: 1.5rem;
            color: #fbbf24;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* 히어로 특징 */
        .hero-features {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }
        
        .feature-item i {
            color: #10b981;
            font-size: 1.1rem;
        }
        
        /* 애니메이션 */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 40px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        @keyframes wave {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-10deg);
            }
            75% {
                transform: rotate(10deg);
            }
        }
        
        /* 반응형 */
        @media (max-width: 768px) {
            .user-quick-stats {
                gap: 20px;
            }
            
            .quick-stat {
                padding: 12px 16px;
            }
            
            .hero-features {
                gap: 20px;
            }
            
            .message-content {
                flex-direction: column;
                text-align: center;
            }
            
            .close-message {
                position: static;
                margin-top: 10px;
            }
        }
    </style> 