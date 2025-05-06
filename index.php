<?php
/**
 * 탑마케팅(TOPMKT) 애플리케이션 메인 진입점
 * 
 * 이 파일은 모든 요청을 처리하는 프론트 컨트롤러입니다.
 * 보안상의 이유로 실제 처리는 public/index.php로 위임합니다.
 */

// 애플리케이션 루트 경로 정의
define('APP_ROOT', __DIR__);

// public/index.php로 요청 전달
require __DIR__ . '/public/index.php'; 