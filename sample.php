<?php
/**
 * Git 테스트를 위한 샘플 PHP 파일
 * 
 * @author JH Lee
 * @date <?php echo date('Y-m-d'); ?>
 */

// 간단한 함수 예제
function sayHello($name) {
    return "안녕하세요, {$name}님!";
}

// 현재 시간 출력
$current_time = date('Y-m-d H:i:s');
echo "현재 시간: {$current_time}";

// 테스트 메시지
echo "<h1>Git 테스트 파일입니다</h1>";
echo "<p>" . sayHello("사용자") . "</p>";
?> 