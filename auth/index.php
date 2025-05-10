<?php
/**
 * 인증 페이지 리다이렉트
 * 
 * 이 파일은 /auth/ 경로로 접근했을 때 루트의 auth.php 파일로 리다이렉트합니다.
 * 중복 코드를 방지하기 위해 하나의 인증 페이지로 통합합니다.
 * 
 * @version 1.0.0
 * @author TOPMKT Development Team
 */

// URL 쿼리 파라미터 유지를 위해 현재 URL에서 쿼리 문자열 추출
$query = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';

// 리다이렉트
header('Location: /auth.php' . $query);
exit; 