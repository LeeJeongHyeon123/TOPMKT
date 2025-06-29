#!/bin/bash

# 자동 Git 푸시 스크립트
# 작성일: 2025-06-15
# 설명: Claude Code가 커밋한 내용을 자동으로 GitHub에 푸시

# 변수 설정
PROJECT_DIR="/var/www/html/topmkt"
LOG_FILE="/var/www/html/topmkt/logs/auto-git-push.log"
MAX_LOG_SIZE=10485760  # 10MB

# 로그 로테이션 함수
rotate_log() {
    if [[ -f "$LOG_FILE" && $(stat -c%s "$LOG_FILE") -gt $MAX_LOG_SIZE ]]; then
        mv "$LOG_FILE" "${LOG_FILE}.old"
        echo "$(date '+%Y-%m-%d %H:%M:%S') - Log rotated" > "$LOG_FILE"
    fi
}

# 로그 함수
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

# 메인 실행부
main() {
    # 로그 로테이션 체크
    rotate_log
    
    # 프로젝트 디렉토리로 이동
    if [[ ! -d "$PROJECT_DIR" ]]; then
        log_message "ERROR: Project directory not found: $PROJECT_DIR"
        exit 1
    fi
    
    cd "$PROJECT_DIR" || {
        log_message "ERROR: Cannot change to project directory"
        exit 1
    }
    
    # Git 상태 확인
    if ! git status &>/dev/null; then
        log_message "ERROR: Not a git repository or git not available"
        exit 1
    fi
    
    # 원격 저장소와의 차이 확인
    LOCAL_COMMITS=$(git rev-list --count HEAD)
    
    # fetch를 통해 원격 상태 확인 (조용히)
    if ! git fetch origin master &>/dev/null; then
        log_message "WARNING: Cannot fetch from remote repository"
    fi
    
    REMOTE_COMMITS=$(git rev-list --count origin/master 2>/dev/null || echo "0")
    
    # 로컬이 원격보다 앞서 있는지 확인
    if [[ $LOCAL_COMMITS -gt $REMOTE_COMMITS ]]; then
        COMMITS_AHEAD=$((LOCAL_COMMITS - REMOTE_COMMITS))
        log_message "INFO: Local repository is $COMMITS_AHEAD commits ahead. Pushing to GitHub..."
        
        # Git 푸시 실행
        if git push origin master --tags &>/dev/null; then
            log_message "SUCCESS: Successfully pushed $COMMITS_AHEAD commits to GitHub"
            
            # 최신 커밋 정보 로깅
            LATEST_COMMIT=$(git log -1 --oneline)
            log_message "INFO: Latest commit - $LATEST_COMMIT"
            
        else
            log_message "ERROR: Failed to push to GitHub"
            
            # 에러 세부 정보 로깅
            ERROR_OUTPUT=$(git push origin master --tags 2>&1)
            log_message "ERROR_DETAILS: $ERROR_OUTPUT"
        fi
        
    else
        log_message "INFO: Repository is up to date (Local: $LOCAL_COMMITS, Remote: $REMOTE_COMMITS)"
    fi
}

# 스크립트 실행
main "$@"