<?php
// 이미지 디렉토리 설정
$baseDir = __DIR__ . '/../public/assets/images';
$directories = [
    'profiles',
    'vision',
    'knowhow',
    'recruiting',
    'events',
    'lectures'
];

// 각 디렉토리 생성
foreach ($directories as $dir) {
    $path = $baseDir . '/' . $dir;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
}

// 더미 이미지 생성 함수
function createDummyImage($width, $height, $text, $filename) {
    $image = imagecreatetruecolor($width, $height);
    
    // 배경색 설정 (랜덤)
    $bgColor = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
    imagefill($image, 0, 0, $bgColor);
    
    // 텍스트 색상 설정
    $textColor = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
    
    // 텍스트 추가
    $fontSize = 5;
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, $fontSize, $x, $y, $text, $textColor);
    
    // 이미지 저장
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
}

// 프로필 이미지 생성 (1-6)
for ($i = 1; $i <= 6; $i++) {
    createDummyImage(300, 300, "Profile $i", $baseDir . "/profiles/$i.jpg");
}

// 비전 이미지 생성 (1-3)
for ($i = 1; $i <= 3; $i++) {
    createDummyImage(800, 400, "Vision $i", $baseDir . "/vision/$i.jpg");
}

// 노하우 이미지 생성 (1-3)
for ($i = 1; $i <= 3; $i++) {
    createDummyImage(800, 400, "Knowhow $i", $baseDir . "/knowhow/$i.jpg");
}

// 채용 이미지 생성 (1-3)
for ($i = 1; $i <= 3; $i++) {
    createDummyImage(800, 400, "Recruiting $i", $baseDir . "/recruiting/$i.jpg");
}

// 이벤트 이미지 생성 (1-3)
for ($i = 1; $i <= 3; $i++) {
    createDummyImage(800, 400, "Event $i", $baseDir . "/events/$i.jpg");
}

// 강의 이미지 생성 (1-3)
for ($i = 1; $i <= 3; $i++) {
    createDummyImage(800, 400, "Lecture $i", $baseDir . "/lectures/$i.jpg");
}

// 로고 이미지 생성
createDummyImage(200, 50, "TOP MKT", $baseDir . "/logo.png");

echo "더미 이미지 생성이 완료되었습니다.\n"; 