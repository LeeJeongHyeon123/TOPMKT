#!/bin/bash

verify_backup() {
    local backup_dir=$1
    local source_dir="/var/www/html/topmkt"
    
    echo "백업 검증 시작..."
    echo "원본 디렉토리: ${source_dir}"
    echo "백업 디렉토리: ${backup_dir}"
    
    # 제외할 디렉토리/파일 목록
    local exclude_list=(
        ".git"
        "node_modules"
        "vendor"
        "*.log"
        "*.log.*"
        "*.tmp"
        "*.temp"
        "*.swp"
        "*.swo"
    )
    
    # 제외 옵션 생성
    local find_exclude=""
    for item in "${exclude_list[@]}"; do
        find_exclude+="-not -path '*/${item}' "
    done
    
    # 1. 파일 수 검증 (제외 목록 제외)
    local source_count=$(eval "find '$source_dir' -type f ${find_exclude} | wc -l")
    local backup_count=$(eval "find '$backup_dir' -type f ${find_exclude} | wc -l")
    
    echo "원본 파일 수: ${source_count}"
    echo "백업 파일 수: ${backup_count}"
    
    if [ "$source_count" -ne "$backup_count" ]; then
        echo "경고: 파일 수가 일치하지 않습니다"
        echo "차이: $((source_count - backup_count))개"
        return 1
    fi
    
    # 2. 중요 파일 존재 여부 확인
    local critical_files=(
        "config/database.php"
        "config/firebase-credentials.json"
        ".env"
    )
    
    echo "중요 파일 검증 중..."
    for file in "${critical_files[@]}"; do
        if [ ! -f "${backup_dir}/${file}" ]; then
            echo "경고: 중요 파일 ${file}이(가) 백업에 없습니다"
            return 1
        fi
        echo "✓ ${file} 확인 완료"
    done
    
    # 3. 파일 크기 검증 (제외 목록 제외)
    local source_size=$(eval "find '$source_dir' -type f ${find_exclude} -exec du -b {} + | awk '{total += $1} END {print total}'")
    local backup_size=$(eval "find '$backup_dir' -type f ${find_exclude} -exec du -b {} + | awk '{total += $1} END {print total}'")
    
    echo "원본 크기: ${source_size} bytes"
    echo "백업 크기: ${backup_size} bytes"
    
    if [ "$source_size" -ne "$backup_size" ]; then
        echo "경고: 전체 크기가 일치하지 않습니다"
        echo "차이: $((source_size - backup_size)) bytes"
        return 1
    fi
    
    echo "백업 검증 완료: 모든 검증 통과"
    return 0
}

# 검증 실행
if [ -z "$1" ]; then
    echo "사용법: $0 <백업_디렉토리_경로>"
    exit 1
fi

verify_backup "$1" 