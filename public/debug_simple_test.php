<?php
/**
 * 🔥 초간단 탭 테스트 페이지
 * debug_fixed.php와 동일한 구조로 탭이 작동하는지 확인
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🧪 간단 탭 테스트</title>
    <style>
        body { font-family: monospace; background: #000; color: #0f0; padding: 20px; margin: 0; }
        .tab { display: inline-block; padding: 10px 20px; background: #333; color: #fff; cursor: pointer; border-radius: 5px; margin: 5px; }
        .tab.active { background: #00ff00; color: #000; }
        .tab-content { display: none; background: #111; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .tab-content.active { display: block; }
        h1 { color: #f60; text-align: center; }
        .success { color: #0f0; }
        .error { color: #f00; }
    </style>
</head>
<body>

<h1>🧪 초간단 탭 테스트</h1>

<p>이 페이지에서 탭이 정상 작동하면 debug_fixed.php도 동일하게 수정할 수 있습니다.</p>

<!-- 탭 버튼들 -->
<div class="tab-container">
    <div class="tab active" onclick="switchTab('tab1')">🖥️ 탭 1</div>
    <div class="tab" onclick="switchTab('tab2')">🛠️ 탭 2</div>
    <div class="tab" onclick="switchTab('tab3')">🗄️ 탭 3</div>
    <div class="tab" onclick="switchTab('tab4')">⚙️ 탭 4</div>
</div>

<!-- 탭 컨텐츠들 -->
<div id="tab1" class="tab-content active">
    <h2>🖥️ 탭 1 컨텐츠</h2>
    <p class="success">✅ 이것은 첫 번째 탭입니다.</p>
    <button onclick="testFunction()">테스트 버튼</button>
</div>

<div id="tab2" class="tab-content">
    <h2>🛠️ 탭 2 컨텐츠</h2>
    <p class="success">✅ 이것은 두 번째 탭입니다.</p>
    <p>PHP 정보:</p>
    <pre><?php echo "PHP 버전: " . PHP_VERSION . "\n"; ?></pre>
</div>

<div id="tab3" class="tab-content">
    <h2>🗄️ 탭 3 컨텐츠</h2>
    <p class="success">✅ 이것은 세 번째 탭입니다.</p>
    <p>데이터베이스 테스트:</p>
    <pre>
<?php
try {
    $mysqli = new mysqli('localhost', 'root', 'Dnlszkem1!', 'topmkt');
    if ($mysqli->connect_error) {
        echo "❌ 연결 실패: " . $mysqli->connect_error;
    } else {
        echo "✅ 데이터베이스 연결 성공";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "❌ 오류: " . $e->getMessage();
}
?>
    </pre>
</div>

<div id="tab4" class="tab-content">
    <h2>⚙️ 탭 4 컨텐츠</h2>
    <p class="success">✅ 이것은 네 번째 탭입니다.</p>
    <p>시스템 정보:</p>
    <pre><?php echo "서버 시간: " . date('Y-m-d H:i:s') . "\n"; ?></pre>
</div>

<!-- JavaScript -->
<script>
console.log('🚀 간단 탭 테스트 JavaScript 시작');

// 탭 전환 함수
function switchTab(tabId) {
    console.log('🔄 탭 전환:', tabId);
    
    // 모든 탭 버튼 비활성화
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // 모든 탭 컨텐츠 숨기기
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.remove('active'));
    
    // 클릭된 탭 버튼 활성화
    event.target.classList.add('active');
    
    // 해당 탭 컨텐츠 표시
    const targetContent = document.getElementById(tabId);
    if (targetContent) {
        targetContent.classList.add('active');
        console.log('✅ 탭 전환 성공:', tabId);
    } else {
        console.error('❌ 탭 컨텐츠 없음:', tabId);
    }
}

// 테스트 함수
function testFunction() {
    alert('테스트 함수가 정상 작동합니다!');
    console.log('✅ 테스트 함수 실행');
}

// 페이지 로드 완료 후 확인
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 페이지 로드 완료');
    
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');
    
    console.log('📊 찾은 탭 버튼:', tabs.length + '개');
    console.log('📊 찾은 탭 컨텐츠:', contents.length + '개');
    
    // 각 탭 ID 확인
    contents.forEach(content => {
        console.log('📋 탭 ID:', content.id);
    });
});

console.log('✅ JavaScript 정의 완료');
</script>

</body>
</html>