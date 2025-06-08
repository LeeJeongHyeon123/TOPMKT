/**
 * 🚀 탑마케팅 로딩 UI 시스템
 * 화려하고 동적인 우주선 테마 로딩 애니메이션
 */

class TopMarketingLoader {
    constructor() {
        // 중복 실행 방지: 이미 실행 중인 인스턴스가 있으면 종료
        if (window.topMarketingLoaderActive) {
            console.log('🚀 로딩 시스템이 이미 실행 중입니다. 중복 실행을 방지합니다.');
            // 기존 로더가 있으면 완전히 무효화된 객체 반환
            return Object.create(null);
        }
        window.topMarketingLoaderActive = true;
        
        // 하루 첫 방문 확인
        this.shouldShowLoading = this.getTodayFirstVisit();
        
        this.progress = 0;
        this.isLoading = false;
        this.loadingStages = [
            '우주선 엔진 점검 중...',
            '연료 주입 중...',
            '항로 계산 중...',
            '통신 시스템 연결 중...',
            '발사 준비 완료!',
            '우주로 출발! 🚀'
        ];
        this.currentStage = 0;
        this.loadingOverlay = null;
        this.progressBar = null;
        this.loadingText = null;
        this.loadingStageElement = null;
        
        this.init();
    }
    
    init() {
        console.log('🚀 TopMarketingLoader 초기화');
        this.createLoadingHTML();
        this.bindEvents();
    }
    
    createLoadingHTML() {
        // 페이지 리다이렉트 감지 (302 상태로 인한 중복 로딩 방지)
        if (window.location.href.includes('/community/write') && !window.location.href.includes('?')) {
            console.log('🚀 리다이렉트 페이지 감지 - 로딩 UI 비활성화');
            return;
        }
        
        // 로딩 오버레이가 이미 존재하면 제거 (중복 방지)
        const existingOverlay = document.getElementById('topMarketing-loading-overlay');
        if (existingOverlay) {
            console.log('🚀 기존 로딩 오버레이 제거');
            existingOverlay.remove();
            // 기존 플래그도 해제
            window.topMarketingLoaderActive = false;
        }
        
        // 재방문 시에는 HTML 생성하지 않음
        if (!this.shouldShowLoading) {
            console.log('🚀 재방문으로 로딩 HTML 생성 건너뛰기');
            return;
        }
        
        const loadingHTML = `
            <div id="topMarketing-loading-overlay" class="loading-overlay">
                <!-- 배경 별들 -->
                <div class="stars-container">
                    <div class="star"></div>
                    <div class="star"></div>
                    <div class="star"></div>
                    <div class="star"></div>
                    <div class="star"></div>
                    <div class="star"></div>
                    <div class="star"></div>
                    <div class="star"></div>
                </div>
                
                <!-- 궤도 링들 -->
                <div class="orbit-ring">
                    <div class="satellite"></div>
                    <div class="satellite"></div>
                </div>
                <div class="orbit-ring"></div>
                <div class="orbit-ring"></div>
                
                <!-- 메인 로딩 컨테이너 -->
                <div class="loading-container">
                    <!-- 로켓 애니메이션 -->
                    <div class="rocket-loader">
                        <div class="rocket-particles">
                            <div class="particle"></div>
                            <div class="particle"></div>
                            <div class="particle"></div>
                            <div class="particle"></div>
                        </div>
                        <div class="rocket-main">🚀</div>
                        <div class="rocket-trail"></div>
                    </div>
                    
                    <!-- 로딩 텍스트 -->
                    <div class="loading-text">탑마케팅에 오신 것을 환영합니다!</div>
                    
                    <!-- 진행률 바 -->
                    <div class="progress-container">
                        <div class="progress-bar" id="loading-progress-bar"></div>
                    </div>
                    
                    <!-- 로딩 단계 -->
                    <div class="loading-stage" id="loading-stage">시스템 초기화 중...</div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', loadingHTML);
        
        // 요소 참조 저장
        this.loadingOverlay = document.getElementById('topMarketing-loading-overlay');
        this.progressBar = document.getElementById('loading-progress-bar');
        this.loadingStageElement = document.getElementById('loading-stage');
    }
    
    bindEvents() {
        // 페이지 로드 완료 시 자동으로 로딩 숨김
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.simulateLoading();
            });
        } else {
            // 이미 로드된 경우 즉시 시뮬레이션 시작
            setTimeout(() => this.simulateLoading(), 100);
        }
    }
    
    /**
     * 하루 첫 방문 확인 및 업데이트
     */
    getTodayFirstVisit() {
        try {
            const today = new Date().toDateString(); // "Mon Dec 25 2023" 형식
            const lastVisitDate = localStorage.getItem('topMarketing_lastVisitDate');
            
            // 오늘 첫 방문인지 확인
            const isFirstVisitToday = lastVisitDate !== today;
            
            if (isFirstVisitToday) {
                // 오늘 첫 방문: 날짜 업데이트
                localStorage.setItem('topMarketing_lastVisitDate', today);
                console.log(`🚀 오늘 첫 방문: ${today}`);
                return true;
            } else {
                // 오늘 이미 방문함
                console.log(`🚀 오늘 재방문: ${today} (로딩 UI 건너뛰기)`);
                return false;
            }
        } catch (error) {
            console.warn('🚀 localStorage 접근 불가, 첫 방문으로 처리:', error);
            return true; // localStorage 사용 불가 시 첫 방문으로 처리
        }
    }
    
    /**
     * 하루 첫 방문 시만 로딩 설정 반환
     */
    getLoadingConfig() {
        // 하루 첫 방문 시에만 로딩 표시
        return {
            duration: 3000,        // 3초
            interval: [300, 500],  // 300-500ms 간격
            progressStep: [5, 20], // 5-20% 씩 증가
            description: '오늘 첫 방문 환영 로딩'
        };
    }
    
    show() {
        console.log('🚀 로딩 화면 표시');
        this.isLoading = true;
        this.progress = 0;
        this.currentStage = 0;
        
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.remove('hide');
            this.loadingOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        this.updateProgress(0);
        this.updateStage(0);
    }
    
    hide() {
        console.log('🚀 로딩 화면 숨김');
        this.isLoading = false;
        
        if (this.loadingOverlay) {
            // 완료 애니메이션 적용
            this.showSuccess();
            
            setTimeout(() => {
                this.loadingOverlay.classList.add('hide');
                document.body.style.overflow = '';
                
                // 완전히 숨겨진 후 제거
                setTimeout(() => {
                    if (this.loadingOverlay && this.loadingOverlay.parentNode) {
                        this.loadingOverlay.remove();
                    }
                    // 로딩 완료 시 플래그 해제
                    window.topMarketingLoaderActive = false;
                    // 커서 스타일도 정상화
                    document.body.style.cursor = '';
                    document.documentElement.style.cursor = '';
                    console.log('🚀 로딩 시스템 완료 - 플래그 해제');
                }, 500);
            }, 1000);
        }
    }
    
    updateProgress(percent) {
        this.progress = Math.min(100, Math.max(0, percent));
        
        if (this.progressBar) {
            this.progressBar.style.width = this.progress + '%';
        }
        
        console.log(`🚀 로딩 진행률: ${this.progress}%`);
    }
    
    updateStage(stageIndex) {
        this.currentStage = stageIndex;
        
        if (this.loadingStageElement && this.loadingStages[stageIndex]) {
            this.loadingStageElement.textContent = this.loadingStages[stageIndex];
            
            // 단계 변경 애니메이션
            this.loadingStageElement.style.opacity = '0';
            setTimeout(() => {
                this.loadingStageElement.style.opacity = '1';
            }, 200);
        }
        
        console.log(`🚀 로딩 단계: ${this.loadingStages[stageIndex]}`);
    }
    
    simulateLoading() {
        if (!this.shouldShowLoading) {
            // 오늘 재방문: 로딩 UI 완전 건너뛰기
            console.log('🚀 오늘 재방문으로 로딩 UI 건너뛰기');
            this.skipLoading();
            return;
        }
        
        // 오늘 첫 방문: 로딩 UI 표시
        const config = this.getLoadingConfig();
        console.log(`🚀 로딩 시뮬레이션 시작 (${config.description})`);
        console.log(`🚀 로딩 설정: 소요시간 ${config.duration}ms, 진행 간격 ${config.interval[0]}-${config.interval[1]}ms`);
        
        this.show();
        
        let progress = 0;
        let stageIndex = 0;
        
        const progressInterval = setInterval(() => {
            // 방문 횟수에 따른 진행률 증가량 조정
            const progressIncrease = Math.random() * (config.progressStep[1] - config.progressStep[0]) + config.progressStep[0];
            progress += progressIncrease;
            
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);
                
                setTimeout(() => {
                    this.hide();
                }, 500); // 첫 방문 시 완료 대기
            }
            
            this.updateProgress(progress);
            
            // 단계 업데이트
            const newStageIndex = Math.min(
                Math.floor((progress / 100) * this.loadingStages.length),
                this.loadingStages.length - 1
            );
            
            if (newStageIndex > stageIndex) {
                stageIndex = newStageIndex;
                this.updateStage(stageIndex);
            }
            
        }, config.interval[0] + Math.random() * (config.interval[1] - config.interval[0])); // 동적 간격
    }
    
    /**
     * 로딩 UI 건너뛰기 (재방문 시)
     */
    skipLoading() {
        console.log('🚀 로딩 UI 건너뛰기 - 즉시 페이지 표시');
        
        // 플래그 해제
        window.topMarketingLoaderActive = false;
        
        // 페이지가 이미 로드된 상태이므로 추가 작업 없음
        // body의 overflow 속성도 정상 상태 유지
        document.body.style.overflow = '';
        // 커서 스타일도 정상화
        document.body.style.cursor = '';
        document.documentElement.style.cursor = '';
    }
    
    showSuccess() {
        console.log('🚀 우주선 발사 시퀀스 시작!');
        
        // 1. 발사 준비 단계
        if (this.loadingStageElement) {
            this.loadingStageElement.innerHTML = '🔥 엔진 점화 중...';
        }
        
        // 2. 화면 진동 효과 (발사 임팩트)
        document.body.classList.add('launch-vibration');
        setTimeout(() => {
            document.body.classList.remove('launch-vibration');
        }, 500);
        
        // 3. 로켓 발사 애니메이션 시작
        const rocketMain = document.querySelector('.rocket-main');
        const rocketTrail = document.querySelector('.rocket-trail');
        
        if (rocketMain) {
            // 1. 모든 기존 애니메이션과 클래스 완전 제거
            rocketMain.className = '';
            rocketMain.style.animation = 'none';
            rocketMain.style.transform = 'translateY(-8px) rotate(0deg)';
            rocketMain.style.position = 'relative';
            
            // 2. 강제로 정지 상태 스타일 적용
            rocketMain.classList.add('rocket-ready');
            
            // 3. 리다이렉트 페이지에서는 발사 애니메이션 건너뛰기
            if (window.location.href.includes('/community/write')) {
                console.log('🚀 리다이렉트 페이지에서 발사 애니메이션 생략');
                return;
            }
            
            // 4. 일반 페이지에서만 발사 애니메이션 실행
            setTimeout(() => {
                if (rocketMain && !window.location.href.includes('/community/write')) {
                    rocketMain.classList.remove('rocket-ready');
                    rocketMain.classList.add('rocket-launch-sequence');
                }
            }, 300);
            
            // 발사 트레일 효과 추가
            if (rocketTrail) {
                rocketTrail.classList.add('rocket-launch-trail');
            }
            
            // 에너지 웨이브 생성
            this.createEnergyWaves();
            
            // 파티클 폭발 효과
            setTimeout(() => {
                this.createCompletionBurst();
            }, 1000);
        }
        
        // 4. 단계별 메시지 업데이트
        setTimeout(() => {
            if (this.loadingStageElement) {
                this.loadingStageElement.innerHTML = '🚀 이륙 중...';
            }
        }, 800);
        
        setTimeout(() => {
            if (this.loadingStageElement) {
                this.loadingStageElement.innerHTML = '🌌 대기권 돌파!';
            }
        }, 1600);
        
        setTimeout(() => {
            if (this.loadingStageElement) {
                this.loadingStageElement.innerHTML = '⭐ 우주 진입!';
            }
        }, 2400);
        
        setTimeout(() => {
            if (this.loadingStageElement) {
                this.loadingStageElement.innerHTML = '✨ 발사 성공!';
                this.loadingStageElement.classList.add('success-hologram');
            }
        }, 3200);
    }
    
    createEnergyWaves() {
        const container = document.querySelector('.loading-container');
        if (!container) return;
        
        // 에너지 웨이브 3개 생성
        for (let i = 0; i < 3; i++) {
            const wave = document.createElement('div');
            wave.className = 'energy-wave';
            wave.style.animationDelay = `${0.3 + i * 0.1}s`;
            container.appendChild(wave);
            
            // 애니메이션 완료 후 제거
            setTimeout(() => {
                if (wave.parentNode) {
                    wave.remove();
                }
            }, 2000);
        }
    }
    
    createCompletionBurst() {
        const container = document.querySelector('.loading-container');
        if (!container) return;
        
        // 파티클 폭발 컨테이너 생성
        const burstContainer = document.createElement('div');
        burstContainer.className = 'completion-burst';
        
        // 8개의 파티클 생성 (8방향)
        for (let i = 0; i < 8; i++) {
            const particle = document.createElement('div');
            particle.className = 'burst-particle';
            particle.style.transform = `rotate(${i * 45}deg) translateX(0)`;
            burstContainer.appendChild(particle);
        }
        
        container.appendChild(burstContainer);
        
        // 애니메이션 완료 후 제거
        setTimeout(() => {
            if (burstContainer.parentNode) {
                burstContainer.remove();
            }
        }, 1500);
    }
    
    // 수동 로딩 제어 메서드들
    setProgress(percent) {
        this.updateProgress(percent);
    }
    
    setStage(message) {
        if (this.loadingStageElement) {
            this.loadingStageElement.textContent = message;
        }
    }
    
    // 커스텀 로딩 시작
    startCustomLoading(options = {}) {
        if (!this.shouldShowLoading) {
            console.log('🚀 오늘 재방문으로 커스텀 로딩 건너뛰기');
            return;
        }
        
        const config = this.getLoadingConfig();
        const {
            stages = this.loadingStages,
            duration = config.duration,
            autoHide = true
        } = options;
        
        console.log(`🚀 커스텀 로딩 시작 (${config.description})`);
        
        this.loadingStages = stages;
        this.show();
        
        if (autoHide) {
            setTimeout(() => {
                this.hide();
            }, duration);
        }
    }
    
    /**
     * 오늘 방문 기록 초기화 (디버깅용)
     */
    resetTodayVisit() {
        try {
            localStorage.removeItem('topMarketing_lastVisitDate');
            console.log('🚀 오늘 방문 기록 초기화 완료');
        } catch (error) {
            console.warn('🚀 오늘 방문 기록 초기화 실패:', error);
        }
    }
    
    /**
     * 현재 방문 통계 조회
     */
    getVisitStats() {
        try {
            const today = new Date().toDateString();
            const lastVisitDate = localStorage.getItem('topMarketing_lastVisitDate');
            const isFirstVisitToday = lastVisitDate !== today;
            
            return {
                today: today,
                lastVisitDate: lastVisitDate || '방문 기록 없음',
                isFirstVisitToday: isFirstVisitToday,
                loadingStatus: isFirstVisitToday ? '로딩 UI 표시' : '로딩 UI 건너뛰기'
            };
        } catch (error) {
            return {
                today: new Date().toDateString(),
                lastVisitDate: '확인 불가',
                isFirstVisitToday: true,
                loadingStatus: '로딩 UI 표시',
                error: error.message
            };
        }
    }
}

// 전역 인스턴스 생성
let topMarketingLoader;

// DOM 로드 완료 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM 로드 완료 - 로딩 시스템 초기화');
    
    // 특정 리다이렉트 페이지에서는 로딩 시스템 완전 비활성화
    if (window.location.href.includes('/community/write') && !window.location.search) {
        console.log('🚀 리다이렉트 페이지 감지 - 로딩 시스템 비활성화');
        window.topMarketingLoaderActive = true; // 플래그 설정하여 다른 인스턴스 방지
        return;
    }
    
    topMarketingLoader = new TopMarketingLoader();
});

// 페이지 로드 이벤트 처리
window.addEventListener('load', function() {
    console.log('🚀 페이지 로드 완료');
});

// AJAX 요청 시 로딩 표시를 위한 헬퍼 함수들
window.TopMarketingLoading = {
    show: () => {
        if (topMarketingLoader) {
            topMarketingLoader.show();
        }
    },
    hide: () => {
        if (topMarketingLoader) {
            topMarketingLoader.hide();
        }
    },
    setProgress: (percent) => {
        if (topMarketingLoader) {
            topMarketingLoader.setProgress(percent);
        }
    },
    setStage: (message) => {
        if (topMarketingLoader) {
            topMarketingLoader.setStage(message);
        }
    },
    custom: (options) => {
        if (topMarketingLoader) {
            topMarketingLoader.startCustomLoading(options);
        }
    },
    // 새로 추가된 함수들
    getVisitStats: () => {
        if (topMarketingLoader) {
            return topMarketingLoader.getVisitStats();
        }
        return null;
    },
    resetTodayVisit: () => {
        if (topMarketingLoader) {
            topMarketingLoader.resetTodayVisit();
        }
    },
    // 하루 첫 방문에 따른 적응형 로딩
    smartLoading: (options = {}) => {
        if (topMarketingLoader) {
            const isFirstVisitToday = topMarketingLoader.getTodayFirstVisit();
            
            if (!isFirstVisitToday) {
                console.log('🚀 오늘 재방문으로 스마트 로딩 건너뛰기');
                return;
            }
            
            const config = topMarketingLoader.getLoadingConfig();
            const smartOptions = {
                ...options,
                duration: config.duration,
                stages: options.stages || [`${config.description} 진행 중...`, '완료!']
            };
            
            topMarketingLoader.startCustomLoading(smartOptions);
        }
    }
};

// AJAX 요청 인터셉터 (jQuery가 있는 경우)
if (typeof $ !== 'undefined') {
    $(document).ajaxStart(function() {
        console.log('🚀 AJAX 요청 시작 - 로딩 표시');
        window.TopMarketingLoading.show();
    });
    
    $(document).ajaxStop(function() {
        console.log('🚀 AJAX 요청 완료 - 로딩 숨김');
        setTimeout(() => {
            window.TopMarketingLoading.hide();
        }, 500);
    });
}

// Fetch API 인터셉터
if (window.fetch) {
    const originalFetch = window.fetch;
    let activeRequests = 0;
    
    window.fetch = function(...args) {
        activeRequests++;
        if (activeRequests === 1) {
            console.log('🚀 Fetch 요청 시작 - 로딩 표시');
            window.TopMarketingLoading.show();
        }
        
        return originalFetch.apply(this, args)
            .finally(() => {
                activeRequests--;
                if (activeRequests === 0) {
                    console.log('🚀 Fetch 요청 완료 - 로딩 숨김');
                    setTimeout(() => {
                        window.TopMarketingLoading.hide();
                    }, 500);
                }
            });
    };
}

console.log('🚀 TopMarketing 로딩 시스템 스크립트 로드 완료');