<?php
/**
 * 강의 일정 컨트롤러
 * 강의/행사 일정 관리 기능
 */

require_once SRC_PATH . '/config/database.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/helpers/ValidationHelper.php';
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';

class LectureController {
    private $db;
    private $userModel;
    
    public function __construct() {
        try {
            $this->db = Database::getInstance();
            $this->userModel = new User();
        } catch (Exception $e) {
            error_log("LectureController 초기화 오류: " . $e->getMessage());
            // 오류 발생 시 기본 페이지로 리다이렉트
            header('Location: /?error=db_connection');
            exit;
        }
    }
    
    /**
     * 강의 일정 메인 페이지 (캘린더 뷰)
     */
    public function index() {
        try {
            // 데이터베이스 테이블 존재 확인
            if (!$this->checkLectureTablesExist()) {
                $this->showSetupPage();
                return;
            }
            
            // 현재 월/년도 파라미터 처리
            $year = $_GET['year'] ?? date('Y');
            $month = $_GET['month'] ?? date('m');
            $view = $_GET['view'] ?? 'calendar'; // calendar, list
            
            // 유효성 검사
            $year = intval($year);
            $month = intval($month);
            
            if ($year < 2020 || $year > 2030) $year = date('Y');
            if ($month < 1 || $month > 12) $month = date('m');
            
            // 해당 월의 강의 목록 조회
            $lectures = $this->getLecturesByMonth($year, $month);
            
            // 카테고리 목록 조회  
            $categories = $this->getCategories();
            
            // 뷰 데이터 준비
            $viewData = [
                'lectures' => $lectures,
                'categories' => $categories,
                'currentYear' => $year,
                'currentMonth' => $month,
                'view' => $view,
                'calendarData' => $this->generateCalendarData($year, $month, $lectures),
                'todayLectures' => $this->getTodayLectures(),
                'upcomingLectures' => $this->getUpcomingLectures(5)
            ];
            
            // 헤더 데이터
            $headerData = [
                'title' => '강의 일정 - 탑마케팅',
                'description' => '다양한 마케팅 강의와 세미나 일정을 확인하고 신청하세요',
                'pageSection' => 'lectures'
            ];
            
            $this->renderView('lectures/index', $viewData, $headerData);
            
        } catch (Exception $e) {
            error_log("강의 목록 조회 오류: " . $e->getMessage());
            $this->showErrorPage('강의 목록을 불러오는 중 오류가 발생했습니다.', $e->getMessage());
        }
    }
    
    /**
     * 강의 상세 페이지
     */
    public function show($id) {
        try {
            $lectureId = intval($id);
            
            // 강의 정보 조회 및 조회수 증가
            $lecture = $this->getLectureById($lectureId, true);
            
            if (!$lecture) {
                header("HTTP/1.0 404 Not Found");
                $this->renderView('templates/404');
                return;
            }
            
            // 현재 사용자의 신청 상태 확인
            $userRegistration = null;
            if (isset($_SESSION['user_id'])) {
                $userRegistration = $this->getUserRegistration($lectureId, $_SESSION['user_id']);
            }
            
            // 신청자 목록 (일부만)
            $registrations = $this->getLectureRegistrations($lectureId, 5);
            
            // 관련 강의 추천 (같은 기업의 강의만)
            $relatedLectures = $this->getRelatedLectures($lecture['category'], $lectureId, $lecture['user_id'], 3);
            
            // 강의 이미지 조회
            $lectureImages = $this->getLectureImages($lectureId);
            $lecture['images'] = $lectureImages;
            

            $viewData = [
                'lecture' => $lecture,
                'userRegistration' => $userRegistration,
                'registrations' => $registrations,
                'relatedLectures' => $relatedLectures,
                'canEdit' => $this->canEditLecture($lecture),
                'canRegister' => $this->canRegisterLecture($lecture),
                'iCalUrl' => $this->generateICalUrl($lectureId)
            ];
            
            $headerData = [
                'title' => htmlspecialchars($lecture['title']) . ' - 강의 일정',
                'page_title' => htmlspecialchars($lecture['title']),
                'page_description' => htmlspecialchars(substr($lecture['description'], 0, 150)),
                'description' => htmlspecialchars(substr($lecture['description'], 0, 150)),
                'pageSection' => 'lectures',
                'og_type' => 'article',
                'og_title' => htmlspecialchars($lecture['title']) . ' - 탑마케팅 강의',
                'og_description' => htmlspecialchars($lecture['instructor_name']) . ' 강사님과 함께하는 ' . htmlspecialchars($lecture['title']) . '. ' . htmlspecialchars(substr($lecture['description'], 0, 100)),
                'og_image' => !empty($lectureImages) ? 'https://' . $_SERVER['HTTP_HOST'] . $lectureImages[0]['url'] : 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/topmkt-logo-og.svg',
                'keywords' => '마케팅 강의, ' . htmlspecialchars($lecture['instructor_name']) . ', 세미나, 워크샵, ' . htmlspecialchars($lecture['title'])
            ];
            
            $this->renderView('lectures/detail', $viewData, $headerData);
            
        } catch (Exception $e) {
            error_log("강의 상세 조회 오류: " . $e->getMessage());
            
            // AJAX 요청인지 확인
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                ResponseHelper::error('강의 정보를 불러오는 중 오류가 발생했습니다.');
            } else {
                // 일반 페이지 요청의 경우 오류 페이지 표시
                $this->showErrorPage('강의 정보를 불러오는 중 오류가 발생했습니다.', $e->getMessage());
            }
        }
    }
    
    /**
     * 강의 작성 폼
     */
    public function create() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login?redirect=' . urlencode('/lectures/create'));
            exit;
        }
        
        // 기업회원 권한 확인
        require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
        $permission = CorporateMiddleware::checkLectureEventPermission();
        
        if (!$permission['hasPermission']) {
            $_SESSION['error_message'] = $permission['message'];
            header('Location: /corp/info');
            exit;
        }
        
        try {
            $categories = $this->getCategories();
            
            // 현재 사용자의 임시저장된 강의 조회
            $draftLecture = $this->getLatestDraftLecture($_SESSION['user_id']);
            
            // 임시저장 데이터 디버깅
            if ($draftLecture) {
                error_log("=== 강의 생성 페이지 로딩 ===");
                error_log("임시저장 강의 발견: ID=" . $draftLecture['id']);
                error_log("임시저장 강의 이미지 데이터: " . (is_array($draftLecture['lecture_images']) ? 'ARRAY[' . count($draftLecture['lecture_images']) . ']' : ($draftLecture['lecture_images'] ?? 'NULL')));
                if (!empty($draftLecture['lecture_images'])) {
                    if (is_array($draftLecture['lecture_images'])) {
                        $imageArray = $draftLecture['lecture_images'];
                    } else {
                        $imageArray = json_decode($draftLecture['lecture_images'], true);
                    }
                    error_log("강의 이미지 배열 개수: " . (is_array($imageArray) ? count($imageArray) : 'NOT_ARRAY'));
                }
            } else {
                error_log("=== 강의 생성 페이지 로딩 ===");
                error_log("임시저장 강의 없음");
            }
            
            $viewData = [
                'categories' => $categories,
                'draftLecture' => $draftLecture,
                'defaultData' => [
                    'location_type' => 'offline',
                    'category' => 'seminar',
                    'difficulty_level' => 'all',
                    'timezone' => 'Asia/Seoul'
                ]
            ];
            
            $headerData = [
                'page_title' => '강의 등록 - 탑마케팅',
                'page_description' => '새로운 강의나 세미나를 등록하세요',
                'pageSection' => 'lectures'
            ];
            
            $this->renderView('lectures/create', $viewData, $headerData);
            
        } catch (Exception $e) {
            error_log("강의 작성 폼 오류: " . $e->getMessage());
            ResponseHelper::error('페이지를 불러오는 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 강의 등록 처리
     */
    public function store() {
        // 즉시 로그 파일에 기록 (권한 문제 해결)
        file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "=== STORE METHOD 진입 - " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
        
        // 오류 출력 방지 (JSON 응답 오염 방지)
        ini_set('display_errors', 0);
        error_reporting(0);
        
        // 새로운 로그 파일 설정 
        ini_set('log_errors', 1);
        ini_set('error_log', '/var/www/html/topmkt/logs/topmkt_errors.log');
        
        try {
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "=== TRY 블록 진입 - " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
            error_log("=== LectureController::store() 시작 ===");
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "LectureController::store() 호출됨 - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            
            // 로그인 확인
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "로그인 확인 중... session user_id: " . ($_SESSION['user_id'] ?? 'NULL') . "\n", FILE_APPEND);
            if (!isset($_SESSION['user_id'])) {
                file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "로그인 안됨 - 세션에 user_id 없음\n", FILE_APPEND);
                ResponseHelper::error('로그인이 필요합니다.', 401);
                return;
            }
            
            // 기업회원 권한 확인
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "권한 확인 시작\n", FILE_APPEND);
            require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
            $permission = CorporateMiddleware::checkLectureEventPermission();
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "권한 확인 결과: " . json_encode($permission) . "\n", FILE_APPEND);
            
            if (!$permission['hasPermission']) {
                file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "권한 없음으로 종료\n", FILE_APPEND);
                ResponseHelper::error($permission['message'], 403);
                return;
            }
            
            // CSRF 토큰 검증
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "CSRF 토큰 검증 시작\n", FILE_APPEND);
            if (!$this->validateCsrfToken()) {
                file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "CSRF 토큰 검증 실패\n", FILE_APPEND);
                ResponseHelper::error('보안 토큰이 유효하지 않습니다.', 403);
                return;
            }
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "CSRF 토큰 검증 통과\n", FILE_APPEND);
            
            // 임시저장 여부 확인
            $isDraft = isset($_POST['status']) && $_POST['status'] === 'draft';
            
            // 상세 디버깅 로그
            $debugLog = '/var/www/html/topmkt/debug_store_flow.log';
            file_put_contents($debugLog, "=== store() 메서드 시작 - " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
            file_put_contents($debugLog, "isDraft: " . ($isDraft ? 'YES' : 'NO') . "\n", FILE_APPEND);
            file_put_contents($debugLog, "POST keys: " . implode(', ', array_keys($_POST)) . "\n", FILE_APPEND);
            file_put_contents($debugLog, "FILES keys: " . implode(', ', array_keys($_FILES)) . "\n", FILE_APPEND);
            file_put_contents($debugLog, "existing_lecture_images in POST: " . (isset($_POST['existing_lecture_images']) ? 'YES - ' . strlen($_POST['existing_lecture_images']) . ' chars' : 'NO') . "\n", FILE_APPEND);
            
            // 강의 이미지 상세 로그
            if (isset($_POST['existing_lecture_images'])) {
                file_put_contents($debugLog, "existing_lecture_images 내용: " . $_POST['existing_lecture_images'] . "\n", FILE_APPEND);
            }
            if (isset($_FILES['lecture_images'])) {
                file_put_contents($debugLog, "lecture_images FILES: " . json_encode($_FILES['lecture_images']) . "\n", FILE_APPEND);
            }
            
            // 직접 로그 파일에 기록
            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => 'store_request',
                'method' => $_SERVER['REQUEST_METHOD'],
                'isDraft' => $isDraft,
                'registration_deadline' => $_POST['registration_deadline'] ?? 'NOT_SET',
                'youtube_video' => $_POST['youtube_video'] ?? 'NOT_SET',
                'status' => $_POST['status'] ?? 'NOT_SET',
                'title' => $_POST['title'] ?? 'NOT_SET'
            ];
            file_put_contents('/var/www/html/topmkt/public/debug.log', json_encode($logData) . "\n", FILE_APPEND);
            
            // 먼저 파일 업로드 처리
            error_log("=== 파일 업로드 처리 시작 ===");
            error_log("전체 FILES 데이터: " . json_encode(array_keys($_FILES)));
            error_log("POST existing_lecture_images 확인: " . (isset($_POST['existing_lecture_images']) ? 'YES - ' . strlen($_POST['existing_lecture_images']) . ' chars' : 'NO'));
            file_put_contents('/workspace/debug_post_data.log', "=== POST 데이터 확인 - " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
            file_put_contents('/workspace/debug_post_data.log', "existing_lecture_images 존재: " . (isset($_POST['existing_lecture_images']) ? 'YES' : 'NO') . "\n", FILE_APPEND);
            $uploadedImages = $this->handleImageUploads($_FILES);
            error_log("강의 이미지 처리 완료: " . json_encode($uploadedImages));
            $instructorImages = $this->handleInstructorImageUploads($_FILES);
            error_log("강사 이미지 처리 함수 호출 완료");
            
            // 강의 이미지 정보를 POST 데이터에 추가 (기존 이미지와 병합)
            $finalLectureImages = [];
            $existingImages = [];
            
            file_put_contents($debugLog, "\n=== 강의 이미지 병합 시작 ===\n", FILE_APPEND);
            file_put_contents($debugLog, "새로 업로드된 이미지: " . json_encode($uploadedImages) . "\n", FILE_APPEND);
            
            // Check if ordered_lecture_images parameter exists (drag & drop reordering)
            if (isset($_POST['ordered_lecture_images']) && !empty($_POST['ordered_lecture_images'])) {
                file_put_contents($debugLog, "=== 순서 정렬된 이미지 데이터 처리 ===\n", FILE_APPEND);
                file_put_contents($debugLog, "ordered_lecture_images JSON 길이: " . strlen($_POST['ordered_lecture_images']) . "\n", FILE_APPEND);
                
                try {
                    $orderedImages = json_decode($_POST['ordered_lecture_images'], true);
                    file_put_contents($debugLog, "순서 정렬 JSON 디코드 결과: " . var_export($orderedImages, true) . "\n", FILE_APPEND);
                    
                    if (is_array($orderedImages)) {
                        // Process ordered images based on display_order
                        $imagesByOrder = [];
                        $newImagesByName = [];
                        
                        // Create lookup array for new uploaded images
                        foreach ($uploadedImages as $newImage) {
                            if (isset($newImage['file_name'])) {
                                $newImagesByName[$newImage['file_name']] = $newImage;
                            }
                        }
                        
                        // Process each ordered image item
                        foreach ($orderedImages as $orderedItem) {
                            if (isset($orderedItem['display_order'])) {
                                $order = (int)$orderedItem['display_order'];
                                
                                // Check if this is a new uploaded image (매칭 개선)
                                $matchedImageData = null;
                                
                                // 1. file_name으로 직접 매칭 시도
                                if (isset($orderedItem['file_name']) && isset($newImagesByName[$orderedItem['file_name']])) {
                                    $matchedImageData = $newImagesByName[$orderedItem['file_name']];
                                } 
                                // 2. 파일 크기로 매칭 시도 (더 안전한 방식)
                                else if (isset($orderedItem['file_size']) && isset($orderedItem['is_new'])) {
                                    $targetSize = (int)$orderedItem['file_size'];
                                    foreach ($uploadedImages as $uploadedImage) {
                                        if (isset($uploadedImage['file_size']) && (int)$uploadedImage['file_size'] === $targetSize) {
                                            $matchedImageData = $uploadedImage;
                                            file_put_contents($debugLog, "파일 크기 매칭 성공: 크기 {$targetSize} -> {$matchedImageData['file_name']}\n", FILE_APPEND);
                                            break;
                                        }
                                    }
                                }
                                // 3. temp_index로 매칭 시도 (fallback)
                                else if (isset($orderedItem['temp_index']) && isset($orderedItem['is_new'])) {
                                    $tempIndex = (int)$orderedItem['temp_index'];
                                    if (isset($uploadedImages[$tempIndex])) {
                                        $matchedImageData = $uploadedImages[$tempIndex];
                                        file_put_contents($debugLog, "temp_index 매칭 성공: 인덱스 {$tempIndex} -> {$matchedImageData['file_name']}\n", FILE_APPEND);
                                    }
                                }
                                // 4. original_name으로 매칭 시도 (호환성)
                                else if (isset($orderedItem['file_name'])) {
                                    foreach ($newImagesByName as $uploadedImage) {
                                        if (isset($uploadedImage['original_name']) && $uploadedImage['original_name'] === $orderedItem['file_name']) {
                                            $matchedImageData = $uploadedImage;
                                            file_put_contents($debugLog, "원본명 매칭 성공: {$orderedItem['file_name']} -> {$uploadedImage['file_name']}\n", FILE_APPEND);
                                            break;
                                        }
                                    }
                                }
                                
                                if ($matchedImageData) {
                                    // Use matched uploaded image data with the specified order
                                    $imageData = $matchedImageData;
                                    $imageData['display_order'] = $order;
                                    
                                    // Ensure file_path is always set for new uploaded images
                                    if (!isset($imageData['file_path']) && isset($imageData['file_name'])) {
                                        $imageData['file_path'] = '/assets/uploads/lectures/' . $imageData['file_name'];
                                        file_put_contents($debugLog, "file_path 누락으로 생성됨: " . $imageData['file_path'] . "\n", FILE_APPEND);
                                    }
                                    
                                    $imagesByOrder[$order] = $imageData;
                                    file_put_contents($debugLog, "새 이미지 순서 적용: " . $imageData['file_name'] . " (순서: $order, file_path: " . ($imageData['file_path'] ?? 'MISSING') . ")\n", FILE_APPEND);
                                } else {
                                    // Use existing image data with updated order
                                    $orderedItem['display_order'] = $order;
                                    
                                    // Ensure file_path is set for existing images that might be missing it
                                    if (!isset($orderedItem['file_path']) && isset($orderedItem['file_name'])) {
                                        $orderedItem['file_path'] = '/assets/uploads/lectures/' . $orderedItem['file_name'];
                                        file_put_contents($debugLog, "기존 이미지 file_path 누락으로 생성됨: " . $orderedItem['file_path'] . "\n", FILE_APPEND);
                                    }
                                    
                                    $imagesByOrder[$order] = $orderedItem;
                                    file_put_contents($debugLog, "기존 이미지 순서 적용: " . ($orderedItem['file_name'] ?? 'UNKNOWN') . " (순서: $order, file_path: " . ($orderedItem['file_path'] ?? 'MISSING') . ")\n", FILE_APPEND);
                                }
                            }
                        }
                        
                        // Sort by display_order and create final array (드래그&드롭 순서 유지)
                        ksort($imagesByOrder);
                        $finalLectureImages = array_values($imagesByOrder);
                        
                        // display_order는 사용자가 드래그&드롭으로 설정한 순서를 유지
                        
                        file_put_contents($debugLog, "display_order 재설정 완료: " . json_encode(array_column($finalLectureImages, 'display_order')) . "\n", FILE_APPEND);
                        
                        file_put_contents($debugLog, "순서 정렬된 최종 이미지: " . count($finalLectureImages) . "개\n", FILE_APPEND);
                        error_log("순서 정렬된 강의 이미지 처리 완료: " . count($finalLectureImages) . "개");
                    } else {
                        file_put_contents($debugLog, "경고: 순서 정렬 데이터가 배열이 아님\n", FILE_APPEND);
                        // Fall back to existing logic
                        $this->processLegacyImageMerging($debugLog, $existingImages, $uploadedImages, $finalLectureImages);
                    }
                } catch (Exception $e) {
                    file_put_contents($debugLog, "순서 정렬 이미지 파싱 오류: " . $e->getMessage() . "\n", FILE_APPEND);
                    error_log("순서 정렬 이미지 파싱 오류: " . $e->getMessage());
                    // Fall back to existing logic
                    $this->processLegacyImageMerging($debugLog, $existingImages, $uploadedImages, $finalLectureImages);
                }
            } else {
                // Use existing logic when ordered_lecture_images is not provided
                file_put_contents($debugLog, "기존 이미지 병합 로직 사용\n", FILE_APPEND);
                $this->processLegacyImageMerging($debugLog, $existingImages, $uploadedImages, $finalLectureImages);
            }
            
            // 3. 최종 이미지 데이터 저장 전 file_path 검증
            if (!empty($finalLectureImages)) {
                // Final validation: ensure all images have file_path
                foreach ($finalLectureImages as &$finalImage) {
                    if (!isset($finalImage['file_path']) && isset($finalImage['file_name'])) {
                        $finalImage['file_path'] = '/assets/uploads/lectures/' . $finalImage['file_name'];
                        file_put_contents($debugLog, "최종 검증: file_path 누락으로 생성됨: " . $finalImage['file_path'] . "\n", FILE_APPEND);
                        error_log("최종 검증: file_path 누락으로 생성됨: " . $finalImage['file_path']);
                    }
                }
                unset($finalImage); // Clean up reference
                
                $_POST['lecture_images_data'] = $finalLectureImages;
                file_put_contents($debugLog, "최종 이미지 POST에 저장: " . json_encode($finalLectureImages) . "\n", FILE_APPEND);
                error_log("최종 강의 이미지 POST에 추가됨: " . count($finalLectureImages) . "개 (기존:" . count($existingImages) . ", 새:" . count($uploadedImages) . ")");
                
                // Log file_path status for each final image
                foreach ($finalLectureImages as $idx => $img) {
                    file_put_contents($debugLog, "최종 이미지 {$idx}: file_name=" . ($img['file_name'] ?? 'MISSING') . ", file_path=" . ($img['file_path'] ?? 'MISSING') . "\n", FILE_APPEND);
                }
            } else {
                file_put_contents($debugLog, "최종 이미지 없음 - POST에 저장하지 않음\n", FILE_APPEND);
            }
            
            // 강사 이미지 정보를 POST 데이터에 추가
            if (!empty($instructorImages)) {
                error_log("강사 이미지 발견됨: " . json_encode($instructorImages));
                foreach ($instructorImages as $index => $imagePath) {
                    if (isset($_POST['instructors'][$index])) {
                        $_POST['instructors'][$index]['image'] = $imagePath;
                        error_log("강사 {$index}에 이미지 추가: {$imagePath}");
                    } else {
                        error_log("경고: 강사 {$index} 데이터가 POST에 없음");
                    }
                }
                error_log("강사 이미지 처리 완료 - POST 업데이트됨");
            } else {
                error_log("강사 이미지 없음 - FILES에서 찾지 못함");
            }
            
            // 입력 데이터 검증 (임시저장 여부 전달)
            error_log("=== 데이터 검증 시작 ===");
            error_log("POST에 existing_lecture_images 포함: " . (isset($_POST['existing_lecture_images']) ? 'YES' : 'NO'));
            error_log("검증 전 주요 필드 상태:");
            error_log("- title: " . (isset($_POST['title']) ? $_POST['title'] : 'MISSING'));
            error_log("- start_date: " . (isset($_POST['start_date']) ? $_POST['start_date'] : 'MISSING'));
            error_log("- end_date: " . (isset($_POST['end_date']) ? $_POST['end_date'] : 'MISSING'));
            error_log("- start_time: " . (isset($_POST['start_time']) ? $_POST['start_time'] : 'MISSING'));
            error_log("- end_time: " . (isset($_POST['end_time']) ? $_POST['end_time'] : 'MISSING'));
            error_log("- location_type: " . (isset($_POST['location_type']) ? $_POST['location_type'] : 'MISSING'));
            error_log("- isDraft: " . ($isDraft ? 'TRUE' : 'FALSE'));
            error_log("- status: " . (isset($_POST['status']) ? $_POST['status'] : 'MISSING'));
            
            $validationResult = $this->validateLectureData($_POST, $isDraft);
            
            error_log("=== 검증 결과 ===");
            error_log("검증 성공 여부: " . ($validationResult['valid'] ? 'SUCCESS' : 'FAILED'));
            if (!$validationResult['valid']) {
                error_log("검증 실패 메시지: " . $validationResult['message']);
                error_log("검증 실패 오류 목록: " . json_encode($validationResult['errors'] ?? []));
                ResponseHelper::error($validationResult['message'], 400, $validationResult['errors'] ?? []);
                return;
            }
            
            // 검증 후 데이터 상태 로깅
            error_log("=== 검증 완료 후 데이터 상태 ===");
            error_log("검증된 registration_deadline: " . ($validationResult['data']['registration_deadline'] ?? 'NULL'));
            error_log("검증된 youtube_video: " . ($validationResult['data']['youtube_video'] ?? 'NULL'));
            error_log("검증된 status: " . ($validationResult['data']['status'] ?? 'NULL'));
            error_log("검증된 강사 정보: " . json_encode($validationResult['data']['instructors'] ?? []));
            
            // 강의 이미지 처리는 validateLectureData에서 이미 완료됨 (기존+새 이미지 병합)
            // 덮어쓰기 방지를 위해 이 부분 제거
            error_log("이미지 병합 처리 완료 - validateLectureData에서 처리됨");
            
            // 강의 데이터 저장 (검증된 데이터 사용)
            try {
                error_log("=== 강의 저장 프로세스 시작 ===");
                
                // 임시저장된 강의가 있으면 UPDATE, 없으면 INSERT
                $draftLecture = $this->getLatestDraftLecture($_SESSION['user_id']);
                error_log("현재 사용자 ID: " . $_SESSION['user_id']);
                error_log("기존 임시저장 강의: " . ($draftLecture ? 'ID=' . $draftLecture['id'] . ', user_id=' . $draftLecture['user_id'] : 'NONE'));
                error_log("요청 상태: " . ($validationResult['data']['status'] ?? 'NULL'));
                
                // 저장 전 마지막 데이터 확인
                error_log("=== 저장 직전 최종 데이터 확인 ===");
                error_log("최종 registration_deadline: " . ($validationResult['data']['registration_deadline'] ?? 'NULL'));
                error_log("최종 youtube_video: " . ($validationResult['data']['youtube_video'] ?? 'NULL'));
                
                // 분기 결정 로깅
                $branchData = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'action' => 'branch_decision',
                    'user_id' => $_SESSION['user_id'],
                    'draftLecture_exists' => $draftLecture ? true : false,
                    'draftLecture_id' => $draftLecture ? $draftLecture['id'] : null,
                    'status_is_draft' => ($validationResult['data']['status'] === 'draft'),
                    'final_registration_deadline' => $validationResult['data']['registration_deadline'] ?? 'NULL',
                    'final_youtube_video' => $validationResult['data']['youtube_video'] ?? 'NULL'
                ];
                file_put_contents('/var/www/html/topmkt/public/debug.log', json_encode($branchData) . "\n", FILE_APPEND);
                
                if ($draftLecture && $validationResult['data']['status'] === 'draft') {
                    file_put_contents('/var/www/html/topmkt/public/debug.log', json_encode(['timestamp' => date('Y-m-d H:i:s'), 'action' => 'calling_updateLecture', 'lecture_id' => $draftLecture['id']]) . "\n", FILE_APPEND);
                    $lectureId = $this->updateLecture($draftLecture['id'], $validationResult['data'], $_SESSION['user_id']);
                } else {
                    file_put_contents('/var/www/html/topmkt/public/debug.log', json_encode(['timestamp' => date('Y-m-d H:i:s'), 'action' => 'calling_createLecture']) . "\n", FILE_APPEND);
                    $lectureId = $this->createLecture($validationResult['data'], $_SESSION['user_id']);
                }
                
                if ($lectureId) {
                    $status = $validationResult['data']['status'] ?? 'draft';
                    
                    if ($status === 'published') {
                        // 정식 등록인 경우 강의 상세 페이지로 이동
                        $message = '강의가 성공적으로 등록되었습니다.';
                        ResponseHelper::json([
                            'success' => true,
                            'message' => $message,
                            'redirectUrl' => '/lectures/' . $lectureId
                        ]);
                    } else {
                        // 임시저장인 경우 현재 페이지에 머물기
                        $message = '강의가 임시저장되었습니다.';
                        
                        // 응답 전 finalLectureImages 상태 확인
                        error_log("=== 응답 직전 finalLectureImages 상태 ===");
                        error_log("finalLectureImages 변수 정의됨: " . (isset($finalLectureImages) ? 'YES' : 'NO'));
                        if (isset($finalLectureImages)) {
                            error_log("finalLectureImages 개수: " . count($finalLectureImages));
                            error_log("finalLectureImages 내용: " . json_encode($finalLectureImages));
                        } else {
                            error_log("finalLectureImages가 정의되지 않음");
                        }
                        
                        // 디버깅을 위한 임시 응답 (나중에 제거)
                        ResponseHelper::json([
                            'success' => true,
                            'message' => $message,
                            'isDraft' => true,
                            'lectureId' => $lectureId,
                            'debug' => [
                                'post_registration_deadline' => $_POST['registration_deadline'] ?? 'NOT_SET',
                                'post_youtube_video' => $_POST['youtube_video'] ?? 'NOT_SET',
                                'validated_registration_deadline' => $validationResult['data']['registration_deadline'] ?? 'NOT_SET',
                                'validated_youtube_video' => $validationResult['data']['youtube_video'] ?? 'NOT_SET',
                                'user_id' => $_SESSION['user_id'],
                                'draft_lecture_found' => $draftLecture ? $draftLecture['id'] : 'NONE',
                                'method_called' => $draftLecture && $validationResult['data']['status'] === 'draft' ? 'updateLecture' : 'createLecture',
                                'sql_result' => $GLOBALS['debug_sql_result'] ?? 'NOT_SET',
                                'update_binding' => [
                                    'params' => isset($finalLectureImages) ? $finalLectureImages : [],
                                    'variable_status' => isset($finalLectureImages) ? 'DEFINED' : 'UNDEFINED',
                                    'image_count' => isset($finalLectureImages) ? count($finalLectureImages) : 0
                                ],
                                'last_binding' => $GLOBALS['debug_last_binding'] ?? 'NOT_SET',
                                'before_execute' => $GLOBALS['debug_before_execute'] ?? 'NOT_SET'
                            ]
                        ]);
                    }
                } else {
                    error_log("강의 생성 실패 - lectureId가 null입니다.");
                    ResponseHelper::error('강의 등록 중 오류가 발생했습니다.', 500);
                }
            } catch (Exception $createException) {
                error_log("강의 생성 중 예외 발생: " . $createException->getMessage());
                error_log("스택 추적: " . $createException->getTraceAsString());
                ResponseHelper::error('강의 등록 중 오류가 발생했습니다: ' . $createException->getMessage(), 500);
            }
            
        } catch (Exception $e) {
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "=== EXCEPTION CAUGHT ===\n", FILE_APPEND);
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "File: " . $e->getFile() . "\n", FILE_APPEND);
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Line: " . $e->getLine() . "\n", FILE_APPEND);
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
            error_log("강의 등록 오류: " . $e->getMessage());
            ResponseHelper::error('강의 등록 중 오류가 발생했습니다: ' . $e->getMessage(), 500);
        }
    }
    
    // === Private Methods ===
    
    /**
     * 월별 강의 목록 조회
     */
    private function getLecturesByMonth($year, $month) {
        try {
            $sql = "
                SELECT 
                    l.*,
                    u.nickname as organizer_name,
                    CASE WHEN l.max_participants IS NULL THEN '무제한' 
                         ELSE CONCAT(l.registration_count, '/', l.max_participants) 
                    END as capacity_info,
                    CASE WHEN l.registration_deadline IS NULL OR l.registration_deadline > NOW() THEN 1 ELSE 0 END as can_register
                FROM lectures l
                JOIN users u ON l.user_id = u.id
                WHERE l.status = 'published'
                AND YEAR(l.start_date) = :year
                AND MONTH(l.start_date) = :month
                ORDER BY l.start_date ASC, l.start_time ASC
            ";
            
            return $this->db->fetchAll($sql, [
                ':year' => $year,
                ':month' => $month
            ]);
        } catch (Exception $e) {
            // 데이터베이스 오류 시 임시 데이터 반환
            return $this->getDemoLectureData($year, $month);
        }
    }
    
    /**
     * 데모 강의 데이터 생성 (데이터베이스 연결 실패 시)
     */
    private function getDemoLectureData($year, $month) {
        // 현재 월에 해당하는 샘플 강의 데이터
        $currentDate = sprintf('%04d-%02d', $year, $month);
        
        return [
            [
                'id' => 1,
                'title' => '디지털 마케팅 전략 세미나',
                'description' => '2025년 최신 디지털 마케팅 트렌드와 실전 전략을 배우는 세미나입니다.',
                'instructor_name' => '김마케팅',
                'start_date' => $currentDate . '-15',
                'end_date' => $currentDate . '-15',
                'start_time' => '14:00:00',
                'end_time' => '17:00:00',
                'location_type' => 'offline',
                'venue_name' => '서울 강남구 세미나실',
                'category' => 'seminar',
                'status' => 'published',
                'organizer_name' => '김마케팅',
                'capacity_info' => '15/30',
                'can_register' => 1,
                'registration_count' => 15,
                'max_participants' => 30,
                'view_count' => 127
            ],
            [
                'id' => 2,
                'title' => '온라인 SNS 마케팅 워크샵',
                'description' => '인스타그램, 페이스북 등 SNS를 활용한 마케팅 실무 워크샵입니다.',
                'instructor_name' => '박소셜',
                'start_date' => $currentDate . '-22',
                'end_date' => $currentDate . '-22',
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'location_type' => 'online',
                'venue_name' => null,
                'online_link' => 'https://zoom.us/j/123456789',
                'category' => 'workshop',
                'status' => 'published',
                'organizer_name' => '박소셜',
                'capacity_info' => '무제한',
                'can_register' => 1,
                'registration_count' => 42,
                'max_participants' => null,
                'view_count' => 89
            ]
        ];
    }
    
    /**
     * 강의 ID로 상세 정보 조회
     */
    private function getLectureById($id, $incrementView = false) {
        if ($incrementView) {
            // 조회수 증가
            $this->db->execute("UPDATE lectures SET view_count = view_count + 1 WHERE id = :id", [':id' => $id]);
        }
        
        $sql = "
            SELECT 
                l.*,
                u.nickname as organizer_name,
                u.nickname as author_name,
                u.email as organizer_email,
                u.profile_image_original,
                u.profile_image_profile,
                COALESCE(u.profile_image_thumb, u.profile_image_profile, '/assets/images/default-avatar.png') as profile_image,
                u.bio as author_bio,
                CASE WHEN l.max_participants IS NULL THEN '무제한' 
                     ELSE CONCAT(l.registration_count, '/', l.max_participants) 
                END as capacity_info,
                CASE WHEN l.registration_deadline IS NULL OR l.registration_deadline > NOW() THEN 1 ELSE 0 END as can_register
            FROM lectures l
            JOIN users u ON l.user_id = u.id
            WHERE l.id = :id
        ";
        
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    /**
     * 카테고리 목록 조회
     */
    private function getCategories() {
        try {
            return $this->db->fetchAll("
                SELECT * FROM lecture_categories 
                WHERE is_active = 1 
                ORDER BY sort_order ASC
            ");
        } catch (Exception $e) {
            // 데이터베이스 오류 시 기본 카테고리 반환
            return [
                ['id' => 1, 'name' => '세미나', 'color_code' => '#007bff', 'icon' => 'fas fa-microphone'],
                ['id' => 2, 'name' => '워크샵', 'color_code' => '#28a745', 'icon' => 'fas fa-tools'],
                ['id' => 3, 'name' => '컨퍼런스', 'color_code' => '#dc3545', 'icon' => 'fas fa-users'],
                ['id' => 4, 'name' => '웨비나', 'color_code' => '#6f42c1', 'icon' => 'fas fa-video'],
                ['id' => 5, 'name' => '교육과정', 'color_code' => '#fd7e14', 'icon' => 'fas fa-graduation-cap']
            ];
        }
    }
    
    /**
     * 캘린더 데이터 생성
     */
    private function generateCalendarData($year, $month, $lectures) {
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $dayOfWeek = date('w', $firstDay);
        
        $calendar = [];
        $lecturesByDate = [];
        
        // 날짜별 강의 분류
        foreach ($lectures as $lecture) {
            $date = date('j', strtotime($lecture['start_date']));
            if (!isset($lecturesByDate[$date])) {
                $lecturesByDate[$date] = [];
            }
            $lecturesByDate[$date][] = $lecture;
        }
        
        // 캘린더 구조 생성
        $currentWeek = [];
        
        // 이전 달 빈 칸
        for ($i = 0; $i < $dayOfWeek; $i++) {
            $currentWeek[] = null;
        }
        
        // 현재 달 날짜
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentWeek[] = [
                'day' => $day,
                'lectures' => $lecturesByDate[$day] ?? [],
                'isToday' => (date('Y-m-d') === sprintf('%04d-%02d-%02d', $year, $month, $day))
            ];
            
            if (count($currentWeek) === 7) {
                $calendar[] = $currentWeek;
                $currentWeek = [];
            }
        }
        
        // 마지막 주 빈 칸 채우기
        while (count($currentWeek) < 7) {
            $currentWeek[] = null;
        }
        if (count($currentWeek) > 0) {
            $calendar[] = $currentWeek;
        }
        
        return $calendar;
    }
    
    /**
     * 오늘의 강의
     */
    private function getTodayLectures() {
        try {
            $sql = "
                SELECT l.*, u.nickname as organizer_name
                FROM lectures l
                JOIN users u ON l.user_id = u.id
                WHERE l.status = 'published'
                AND DATE(l.start_date) = CURDATE()
                ORDER BY l.start_time ASC
                LIMIT 5
            ";
            
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            // 오늘 날짜와 일치하는 데모 데이터 반환
            $today = date('Y-m-d');
            $demoData = $this->getDemoLectureData(date('Y'), date('n'));
            
            return array_filter($demoData, function($lecture) use ($today) {
                return $lecture['start_date'] === $today;
            });
        }
    }
    
    /**
     * 다가오는 강의
     */
    private function getUpcomingLectures($limit = 5) {
        try {
            $sql = "
                SELECT l.*, u.nickname as organizer_name
                FROM lectures l
                JOIN users u ON l.user_id = u.id
                WHERE l.status = 'published'
                AND l.start_date > CURDATE()
                ORDER BY l.start_date ASC, l.start_time ASC
                LIMIT :limit
            ";
            
            return $this->db->fetchAll($sql, [':limit' => $limit]);
        } catch (Exception $e) {
            // 미래 날짜의 데모 데이터 반환
            $today = date('Y-m-d');
            $demoData = $this->getDemoLectureData(date('Y'), date('n'));
            
            $upcoming = array_filter($demoData, function($lecture) use ($today) {
                return $lecture['start_date'] > $today;
            });
            
            return array_slice($upcoming, 0, $limit);
        }
    }
    
    /**
     * 강의 신청 정보 조회
     */
    private function getUserRegistration($lectureId, $userId) {
        return $this->db->fetch("
            SELECT * FROM lecture_registrations
            WHERE lecture_id = :lecture_id AND user_id = :user_id
        ", [
            ':lecture_id' => $lectureId,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * 강의 신청자 목록
     */
    private function getLectureRegistrations($lectureId, $limit = null) {
        $sql = "
            SELECT lr.*, u.nickname
            FROM lecture_registrations lr
            JOIN users u ON lr.user_id = u.id
            WHERE lr.lecture_id = :lecture_id
            AND lr.status IN ('confirmed', 'pending')
            ORDER BY lr.registration_date ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            return $this->db->fetchAll($sql, [':lecture_id' => $lectureId, ':limit' => $limit]);
        }
        
        return $this->db->fetchAll($sql, [':lecture_id' => $lectureId]);
    }
    
    /**
     * 관련 강의 추천 (같은 기업의 강의만)
     */
    private function getRelatedLectures($category, $excludeId, $userId, $limit = 3) {
        return $this->db->fetchAll("
            SELECT l.*, u.nickname as organizer_name
            FROM lectures l
            JOIN users u ON l.user_id = u.id
            WHERE l.status = 'published'
            AND l.category = :category
            AND l.id != :exclude_id
            AND l.user_id = :user_id
            AND l.start_date >= CURDATE()
            ORDER BY l.start_date ASC
            LIMIT :limit
        ", [
            ':category' => $category,
            ':exclude_id' => $excludeId,
            ':user_id' => $userId,
            ':limit' => $limit
        ]);
    }
    
    /**
     * 강의 수정 권한 확인
     */
    private function canEditLecture($lecture) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // 작성자 본인 또는 관리자
        return $_SESSION['user_id'] == $lecture['user_id'] || 
               in_array($_SESSION['user_role'] ?? '', ['ADMIN', 'SUPER_ADMIN']);
    }
    
    /**
     * 강의 신청 가능 여부 확인
     */
    private function canRegisterLecture($lecture) {
        // 발행된 상태이고 등록 마감일이 지나지 않았으며 정원이 남아있는 경우
        if ($lecture['status'] !== 'published') return false;
        if ($lecture['registration_deadline'] && strtotime($lecture['registration_deadline']) < time()) return false;
        if ($lecture['max_participants'] && $lecture['registration_count'] >= $lecture['max_participants']) return false;
        
        return true;
    }
    
    /**
     * 강의 생성 권한 확인
     */
    private function canCreateLecture() {
        require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
        $permission = CorporateMiddleware::checkLectureEventPermission();
        return $permission['hasPermission'];
    }
    
    /**
     * 강의 데이터 검증
     */
    private function validateLectureData($data, $isDraft = false) {
        $errors = [];
        
        // 임시저장이 아닌 경우에만 필수 필드 검증
        if (!$isDraft) {
            error_log("=== PUBLISHED 상태 필수 필드 검증 시작 ===");
            if (empty($data['title'])) {
                $errors[] = '강의 제목을 입력해주세요.';
                error_log("검증 실패: title 누락");
            } else {
                error_log("검증 성공: title = " . $data['title']);
            }
            if (empty($data['description'])) {
                $errors[] = '강의 설명을 입력해주세요.';
                error_log("검증 실패: description 누락");
            } else {
                error_log("검증 성공: description 길이 = " . strlen($data['description']));
            }
            if (empty($data['start_date'])) {
                $errors[] = '시작 날짜를 입력해주세요.';
                error_log("검증 실패: start_date 누락");
            } else {
                error_log("검증 성공: start_date = " . $data['start_date']);
            }
            if (empty($data['end_date'])) {
                $errors[] = '종료 날짜를 입력해주세요.';
                error_log("검증 실패: end_date 누락");
            } else {
                error_log("검증 성공: end_date = " . $data['end_date']);
            }
            if (empty($data['start_time'])) {
                $errors[] = '시작 시간을 입력해주세요.';
                error_log("검증 실패: start_time 누락");
            } else {
                error_log("검증 성공: start_time = " . $data['start_time']);
            }
            if (empty($data['end_time'])) {
                $errors[] = '종료 시간을 입력해주세요.';
                error_log("검증 실패: end_time 누락");
            } else {
                error_log("검증 성공: end_time = " . $data['end_time']);
            }
            error_log("=== PUBLISHED 필수 필드 검증 완료, 현재 오류 수: " . count($errors) . " ===");
        }
        
        // 복수 강사 데이터 구성 및 검증
        $instructors = [];
        
        error_log("=== 강사 데이터 검증 시작 ===");
        error_log("전달받은 강사 데이터: " . json_encode($data['instructors'] ?? 'NOT_SET'));
        
        // 디버깅용 직접 파일 로그
        file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "=== 강사 데이터 검증 시작 (" . date('Y-m-d H:i:s') . ") ===\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "isDraft: " . ($isDraft ? 'true' : 'false') . "\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "전달받은 강사 데이터: " . json_encode($data['instructors'] ?? 'NOT_SET') . "\n", FILE_APPEND);
        
        // 임시저장 시 기존 강사 이미지 정보 보존
        $existingInstructors = [];
        $currentUserId = $_SESSION['user_id'] ?? null;
        file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "user_id 확인: data[user_id]=" . ($data['user_id'] ?? 'NULL') . ", session[user_id]=" . ($currentUserId ?? 'NULL') . "\n", FILE_APPEND);
        
        if ($isDraft && !empty($currentUserId)) {
            $existingSql = "SELECT instructors_json FROM lectures WHERE user_id = ? AND status = 'draft' ORDER BY updated_at DESC LIMIT 1";
            $existingResult = $this->db->fetch($existingSql, [':user_id' => $currentUserId]);
            file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "기존 데이터 조회 SQL: {$existingSql}, user_id: {$currentUserId}\n", FILE_APPEND);
            file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "기존 데이터 조회 결과: " . json_encode($existingResult) . "\n", FILE_APPEND);
            if ($existingResult && !empty($existingResult['instructors_json'])) {
                $existingInstructors = json_decode($existingResult['instructors_json'], true) ?: [];
                file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "기존 강사 데이터 파싱됨: " . json_encode($existingInstructors) . "\n", FILE_APPEND);
            } else {
                file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "기존 강사 데이터 없음\n", FILE_APPEND);
            }
        }
        
        if (isset($data['instructors']) && is_array($data['instructors'])) {
            foreach ($data['instructors'] as $index => $instructor) {
                error_log("강사 {$index} 처리 중: " . json_encode($instructor));
                
                $instructorName = trim($instructor['name'] ?? '');
                $instructorInfo = trim($instructor['info'] ?? '');
                $instructorTitle = trim($instructor['title'] ?? '');
                
                if (!empty($instructorName)) {
                    $instructorData = [
                        'name' => $instructorName,
                        'info' => $instructorInfo,
                        'title' => $instructorTitle
                    ];
                    
                    // 이미지 정보 처리 (새로 업로드된 이미지 우선, 없으면 기존 이미지 보존)
                    if (isset($instructor['image']) && !empty($instructor['image'])) {
                        $instructorData['image'] = $instructor['image'];
                        error_log("강사 {$index} 새 이미지 추가됨: " . $instructor['image']);
                    } elseif (isset($existingInstructors[$index]['image']) && !empty($existingInstructors[$index]['image'])) {
                        $instructorData['image'] = $existingInstructors[$index]['image'];
                        error_log("강사 {$index} 기존 이미지 보존됨: " . $existingInstructors[$index]['image']);
                    } else {
                        error_log("강사 {$index} 이미지 없음 (새 이미지: " . ($instructor['image'] ?? 'NOT_SET') . ", 기존 이미지: " . ($existingInstructors[$index]['image'] ?? 'NOT_SET') . ")");
                    }
                    
                    $instructors[] = $instructorData;
                    file_put_contents('/var/www/html/topmkt/debug_instructor_validation.log', "강사 {$index} 최종 데이터: " . json_encode($instructorData) . "\n", FILE_APPEND);
                }
            }
        }
        
        if (!$isDraft && empty($instructors)) {
            $errors[] = '최소 1명의 강사 정보를 입력해주세요.';
            error_log("검증 실패: 강사 정보 누락 (강사 수: " . count($instructors) . ")");
        } else if (!$isDraft) {
            error_log("검증 성공: 강사 정보 있음 (강사 수: " . count($instructors) . ")");
        }
        
        // 강사 데이터 처리
        if (!empty($instructors)) {
            $data['instructors_json'] = json_encode($instructors, JSON_UNESCAPED_UNICODE);
            error_log("=== 강사 JSON 변환 완료 ===");
            error_log("최종 강사 배열: " . json_encode($instructors));
            error_log("JSON 변환 결과: " . $data['instructors_json']);
            
            // 첫 번째 강사를 기본 강사로 설정 (기존 필드와 호환성 유지)
            $data['instructor_name'] = $instructors[0]['name'];
            $data['instructor_info'] = $instructors[0]['info'];
        } else {
            error_log("강사 데이터 없음 - 기본값으로 설정");
            // 강사 정보가 없는 경우 기본값 설정
            $data['instructors_json'] = null;
            $data['instructor_name'] = '';
            $data['instructor_info'] = '';
        }
        
        $data['instructors'] = $instructors;
        
        // 강의 이미지 처리 (기존 이미지와 새 이미지 병합)
        $finalLectureImages = [];
        
        // 1. 프론트엔드에서 전송된 기존 이미지 데이터 먼저 확인
        error_log("=== validateLectureData 메서드에서 강의 이미지 처리 시작 ===");
        file_put_contents('/workspace/debug_lecture_images.log', "=== 강의 이미지 처리 시작 - " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND | LOCK_EX);
        file_put_contents('/workspace/debug_lecture_images.log', "POST existing_lecture_images 존재: " . (isset($data['existing_lecture_images']) ? 'YES' : 'NO') . "\n", FILE_APPEND | LOCK_EX);
        if (isset($data['existing_lecture_images'])) {
            file_put_contents('/workspace/debug_lecture_images.log', "existing_lecture_images 데이터: " . $data['existing_lecture_images'] . "\n", FILE_APPEND | LOCK_EX);
        }
        
        // store 메서드에서 이미 기존+새 이미지 병합이 완료되어 lecture_images_data에 저장됨
        // 중복 처리 방지를 위해 lecture_images_data만 사용
        file_put_contents('/workspace/debug_lecture_images.log', "이미지 병합 데이터 확인: " . (isset($data['lecture_images_data']) ? 'YES' : 'NO') . "\n", FILE_APPEND);
        if (isset($data['lecture_images_data']) && !empty($data['lecture_images_data'])) {
            $finalLectureImages = $data['lecture_images_data'];
            file_put_contents('/workspace/debug_lecture_images.log', "병합된 강의 이미지 사용: " . count($data['lecture_images_data']) . "개\n", FILE_APPEND);
        }
        // lecture_images_data가 없으면 기존 이미지만 사용 (신규 등록 시)
        else if (isset($data['existing_lecture_images']) && !empty($data['existing_lecture_images'])) {
            file_put_contents('/workspace/debug_lecture_images.log', "기존 이미지만 사용: " . $data['existing_lecture_images'] . "\n", FILE_APPEND);
            try {
                $existingFromFrontend = json_decode($data['existing_lecture_images'], true);
                if (is_array($existingFromFrontend)) {
                    $finalLectureImages = $existingFromFrontend;
                    file_put_contents('/workspace/debug_lecture_images.log', "기존 강의 이미지 로드됨: " . count($existingFromFrontend) . "개\n", FILE_APPEND);
                }
            } catch (Exception $e) {
                file_put_contents('/workspace/debug_lecture_images.log', "기존 강의 이미지 파싱 오류: " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }
        // 마지막으로 DB에서 조회 (다른 모든 방법이 실패한 경우)
        else if ($isDraft && !empty($currentUserId)) {
            file_put_contents('/workspace/debug_lecture_images.log', "DB에서 기존 이미지 조회 시도 - user_id: $currentUserId\n", FILE_APPEND);
            $existingLectureResult = $this->db->fetch("SELECT lecture_images FROM lectures WHERE user_id = ? AND status = 'draft' ORDER BY updated_at DESC LIMIT 1", [':user_id' => $currentUserId]);
            if ($existingLectureResult && !empty($existingLectureResult['lecture_images'])) {
                $existingImages = json_decode($existingLectureResult['lecture_images'], true);
                if (is_array($existingImages)) {
                    $finalLectureImages = $existingImages;
                    file_put_contents('/workspace/debug_lecture_images.log', "DB에서 기존 강의 이미지 로드됨: " . count($existingImages) . "개\n", FILE_APPEND);
                }
            }
        }
        
        // 최종 강의 이미지 저장
        file_put_contents('/workspace/debug_lecture_images.log', "최종 이미지 배열 크기: " . count($finalLectureImages) . "\n", FILE_APPEND);
        if (!empty($finalLectureImages)) {
            $data['lecture_images'] = json_encode($finalLectureImages, JSON_UNESCAPED_UNICODE);
            file_put_contents('/workspace/debug_lecture_images.log', "최종 강의 이미지 JSON 변환 완료: " . count($finalLectureImages) . "개 이미지\n", FILE_APPEND);
        } else {
            $data['lecture_images'] = null;
            file_put_contents('/workspace/debug_lecture_images.log', "강의 이미지 없음\n", FILE_APPEND);
        }
        
        // 콘텐츠 유형은 강의로 고정
        $data['content_type'] = 'lecture';
        
        // 날짜 유효성 검증
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
                $errors[] = '종료 날짜는 시작 날짜보다 늦어야 합니다.';
            }
        }
        
        // 시간 유효성 검증 (임시저장이 아니고 시간이 모두 입력된 경우에만)
        if (!$isDraft && !empty($data['start_time']) && !empty($data['end_time']) && !empty($data['start_date']) && !empty($data['end_date'])) {
            // 디버깅을 위한 로그
            error_log("시간 검증: start_date={$data['start_date']}, end_date={$data['end_date']}, start_time={$data['start_time']}, end_time={$data['end_time']}");
            
            // 날짜와 시간을 함께 고려한 검증
            $startFullDateTime = DateTime::createFromFormat('Y-m-d H:i', $data['start_date'] . ' ' . $data['start_time']);
            $endFullDateTime = DateTime::createFromFormat('Y-m-d H:i', $data['end_date'] . ' ' . $data['end_time']);
            
            if ($startFullDateTime && $endFullDateTime) {
                error_log("날짜시간 객체 생성 성공: start={$startFullDateTime->format('Y-m-d H:i')}, end={$endFullDateTime->format('Y-m-d H:i')}");
                
                // 종료일시가 시작일시보다 이전인 경우만 오류 처리
                if ($startFullDateTime > $endFullDateTime) {
                    error_log("시간 검증 실패: 시작 일시가 종료 일시보다 늦음");
                    $errors[] = '종료 일시는 시작 일시보다 늦어야 합니다.';
                } else {
                    error_log("시간 검증 성공");
                }
            } else {
                error_log("날짜시간 객체 생성 실패");
            }
        } else if ($isDraft) {
            error_log("임시저장이므로 시간 검증 건너뜀");
        }
        
        // 정원 검증
        if (!empty($data['max_participants']) && intval($data['max_participants']) < 1) {
            $errors[] = '최대 참가자 수는 1명 이상이어야 합니다.';
        }
        
        // 위치 타입별 필수 필드 검증
        error_log("=== 위치 타입 검증 시작 ===");
        error_log("location_type 값: " . ($data['location_type'] ?? 'NULL'));
        if (!empty($data['location_type'])) {
            if ($data['location_type'] === 'offline') {
                error_log("오프라인 진행 - venue_name 검증");
                if (empty($data['venue_name'])) {
                    $errors[] = '오프라인 진행 시 장소명은 필수입니다.';
                    error_log("검증 실패: venue_name 누락");
                } else {
                    error_log("검증 성공: venue_name = " . $data['venue_name']);
                }
            } elseif ($data['location_type'] === 'online') {
                error_log("온라인 진행 - online_link 검증");
                if (empty($data['online_link'])) {
                    $errors[] = '온라인 진행 시 온라인 링크는 필수입니다.';
                    error_log("검증 실패: online_link 누락");
                } elseif (!filter_var($data['online_link'], FILTER_VALIDATE_URL)) {
                    $errors[] = '올바른 URL 형식을 입력해주세요.';
                    error_log("검증 실패: online_link 형식 오류 - " . $data['online_link']);
                } else {
                    error_log("검증 성공: online_link = " . $data['online_link']);
                }
            } else {
                error_log("알 수 없는 location_type: " . $data['location_type']);
            }
        } else {
            $errors[] = '진행 방식을 선택해주세요.';
            error_log("검증 실패: location_type 누락");
        }
        error_log("=== 위치 타입 검증 완료, 현재 오류 수: " . count($errors) . " ===");
        
        // 카테고리는 세미나로 고정
        $data['category'] = 'seminar';
        
        // 과거 날짜 검증
        if (!empty($data['start_date']) && strtotime($data['start_date']) < strtotime(date('Y-m-d'))) {
            $errors[] = '시작 날짜는 오늘 이후여야 합니다.';
        }
        
        // YouTube URL 검증
        if (!empty($data['youtube_video'])) {
            $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
            if (!preg_match($pattern, $data['youtube_video'])) {
                $errors[] = '올바른 YouTube URL을 입력해주세요.';
            }
        }
        
        // 난이도는 전체 대상으로 고정
        $data['difficulty_level'] = 'all';
        
        // 시간대 기본값 설정
        if (empty($data['timezone'])) {
            $data['timezone'] = 'Asia/Seoul';
        }
        
        
        // registration_deadline 처리
        error_log("registration_deadline 원본 데이터: " . ($data['registration_deadline'] ?? 'NULL'));
        if (!empty($data['registration_deadline'])) {
            try {
                // datetime-local 형식 (2025-06-18T16:20)을 MySQL datetime 형식으로 변환
                $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $data['registration_deadline']);
                if ($dateTime) {
                    $convertedDate = $dateTime->format('Y-m-d H:i:s');
                    $data['registration_deadline'] = $convertedDate;
                    error_log("registration_deadline 변환된 데이터: " . $convertedDate);
                } else {
                    // fallback: strtotime 사용
                    $timestamp = strtotime($data['registration_deadline']);
                    if ($timestamp !== false) {
                        $convertedDate = date('Y-m-d H:i:s', $timestamp);
                        $data['registration_deadline'] = $convertedDate;
                        error_log("registration_deadline fallback 변환: " . $convertedDate);
                    } else {
                        $data['registration_deadline'] = null;
                        error_log("registration_deadline 변환 실패");
                    }
                }
            } catch (Exception $e) {
                error_log("registration_deadline 변환 오류: " . $e->getMessage());
                $data['registration_deadline'] = null;
            }
        } else {
            $data['registration_deadline'] = null;
            error_log("registration_deadline이 비어있음");
        }
        
        // youtube_video 처리
        error_log("youtube_video 원본 데이터: " . ($data['youtube_video'] ?? 'NULL'));
        if (!empty($data['youtube_video'])) {
            $data['youtube_video'] = trim($data['youtube_video']);
            error_log("youtube_video 처리된 데이터: " . $data['youtube_video']);
        } else {
            $data['youtube_video'] = null;
            error_log("youtube_video가 비어있음");
        }
        
        // status 값 처리 (임시저장 vs 등록)
        $data['status'] = isset($data['status']) && in_array($data['status'], ['draft', 'published']) ? $data['status'] : 'draft';
        
        // 중복 강의 검증 (제목, 날짜, 시간이 동일한 경우) - 임시저장이 아닌 경우에만
        if (!$isDraft && !empty($data['title']) && !empty($data['start_date']) && !empty($data['start_time'])) {
            error_log("=== 중복 강의 검증 시작 ===");
            $duplicateCheck = $this->checkDuplicateLecture($data);
            if (!$duplicateCheck['valid']) {
                $errors[] = $duplicateCheck['message'];
                error_log("검증 실패: 중복 강의 발견 - " . $duplicateCheck['message']);
            } else {
                error_log("검증 성공: 중복 강의 없음");
            }
        } else if (!$isDraft) {
            error_log("중복 강의 검증 건너뜀 - 필수 데이터 누락 (title:" . (!empty($data['title']) ? 'OK' : 'MISSING') . ", start_date:" . (!empty($data['start_date']) ? 'OK' : 'MISSING') . ", start_time:" . (!empty($data['start_time']) ? 'OK' : 'MISSING') . ")");
        }
        
        error_log("=== 최종 검증 결과 ===");
        error_log("총 오류 수: " . count($errors));
        if (!empty($errors)) {
            error_log("오류 목록: " . json_encode($errors));
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'message' => empty($errors) ? '' : implode(' ', $errors),
            'data' => $data  // 수정된 데이터 반환
        ];
    }
    
    /**
     * 중복 강의 검사
     */
    private function checkDuplicateLecture($data) {
        try {
            $currentUserId = AuthMiddleware::getCurrentUserId();
            
            $sql = "
                SELECT COUNT(*) as count 
                FROM lectures 
                WHERE title = :title 
                AND start_date = :start_date 
                AND start_time = :start_time 
                AND status = 'published'
                AND user_id != :user_id
            ";
            
            $result = $this->db->fetch($sql, [
                ':title' => $data['title'],
                ':start_date' => $data['start_date'],
                ':start_time' => $data['start_time'],
                ':user_id' => $currentUserId
            ]);
            
            if ($result && $result['count'] > 0) {
                return [
                    'valid' => false,
                    'message' => '동일한 제목, 날짜, 시간의 강의가 이미 등록되어 있습니다.'
                ];
            }
            
            return ['valid' => true];
            
        } catch (Exception $e) {
            error_log("중복 강의 검사 오류: " . $e->getMessage());
            // 에러 시에는 통과시킴 (보수적 접근)
            return ['valid' => true];
        }
    }
    
    /**
     * 강의 생성
     */
    private function createLecture($data, $userId) {
        try {
            $sql = "
                INSERT INTO lectures (
                    user_id, title, description, instructor_name, instructor_info,
                    start_date, end_date, start_time, end_time, timezone,
                    location_type, venue_name, venue_address, venue_latitude, venue_longitude, online_link,
                    max_participants, registration_fee, registration_deadline, category, content_type, 
                    instructors_json, lecture_images, requirements, benefits, youtube_video,
                    status, created_at
                ) VALUES (
                    :user_id, :title, :description, :instructor_name, :instructor_info,
                    :start_date, :end_date, :start_time, :end_time, :timezone,
                    :location_type, :venue_name, :venue_address, :venue_latitude, :venue_longitude, :online_link,
                    :max_participants, :registration_fee, :registration_deadline, :category, :content_type,
                    :instructors_json, :lecture_images, :requirements, :benefits, :youtube_video,
                    :status, NOW()
                )
            ";
            
            $params = [
                ':user_id' => $userId,
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':instructor_name' => $data['instructor_name'],
                ':instructor_info' => $data['instructor_info'] ?? null,
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':timezone' => $data['timezone'] ?? 'Asia/Seoul',
                ':location_type' => $data['location_type'] ?? 'offline',
                ':venue_name' => $data['venue_name'] ?? null,
                ':venue_address' => $data['venue_address'] ?? null,
                ':venue_latitude' => !empty($data['venue_latitude']) ? floatval($data['venue_latitude']) : null,
                ':venue_longitude' => !empty($data['venue_longitude']) ? floatval($data['venue_longitude']) : null,
                ':online_link' => $data['online_link'] ?? null,
                ':max_participants' => empty($data['max_participants']) ? null : intval($data['max_participants']),
                ':registration_fee' => intval($data['registration_fee'] ?? 0),
                ':registration_deadline' => $data['registration_deadline'] ?? null,
                ':category' => $data['category'] ?? 'seminar',
                ':content_type' => $data['content_type'] ?? 'lecture',
                ':instructors_json' => $data['instructors_json'] ?? null,
                ':lecture_images' => $data['lecture_images'] ?? null,
                ':requirements' => $data['requirements'] ?? null,
                ':benefits' => $data['benefits'] ?? null,
                ':youtube_video' => $data['youtube_video'] ?? null,
                ':status' => $data['status'] ?? 'draft'
            ];
            
            error_log("=== createLecture 메서드 시작 ===");
            error_log("받은 데이터 - registration_deadline: " . ($data['registration_deadline'] ?? 'NULL'));
            error_log("받은 데이터 - youtube_video: " . ($data['youtube_video'] ?? 'NULL'));
            error_log("SQL 파라미터 - registration_deadline: " . ($params[':registration_deadline'] ?? 'NULL'));
            error_log("SQL 파라미터 - youtube_video: " . ($params[':youtube_video'] ?? 'NULL'));
            error_log("실행할 SQL: " . preg_replace('/\s+/', ' ', trim($sql)));
            error_log("SQL 파라미터 전체: " . json_encode($params));
            
            $result = $this->db->execute($sql, $params);
            error_log("SQL 실행 결과: " . ($result ? 'SUCCESS' : 'FAILED'));
            
            $lectureId = $this->db->lastInsertId();
            error_log("생성된 강의 ID: " . $lectureId);
            
            // 디버깅: 실제 저장된 데이터 확인
            if ($lectureId) {
                $savedData = $this->db->fetch("SELECT registration_deadline, youtube_video FROM lectures WHERE id = ?", [$lectureId]);
                error_log("실제 저장된 데이터: " . json_encode($savedData));
                
                // 강의가 published 상태로 등록되었다면 해당 사용자의 draft 강의들을 삭제
                if (isset($data['status']) && $data['status'] === 'published') {
                    $this->deleteDraftLectures($userId, $lectureId);
                }
            }
            
            return $lectureId;
            
        } catch (Exception $e) {
            error_log("createLecture 예외: " . $e->getMessage());
            error_log("createLecture 스택 추적: " . $e->getTraceAsString());
            throw $e;
        }
    }
    
    /**
     * 강의 업데이트
     */
    private function updateLecture($lectureId, $data, $userId) {
        try {
            $sql = "
                UPDATE lectures SET 
                    title = :title,
                    description = :description,
                    instructor_name = :instructor_name,
                    instructor_info = :instructor_info,
                    start_date = :start_date,
                    end_date = :end_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    timezone = :timezone,
                    location_type = :location_type,
                    venue_name = :venue_name,
                    venue_address = :venue_address,
                    venue_latitude = :venue_latitude,
                    venue_longitude = :venue_longitude,
                    online_link = :online_link,
                    max_participants = :max_participants,
                    registration_fee = :registration_fee,
                    registration_deadline = :registration_deadline,
                    category = :category,
                    content_type = :content_type,
                    instructors_json = :instructors_json,
                    lecture_images = :lecture_images,
                    requirements = :requirements,
                    benefits = :benefits,
                    youtube_video = :youtube_video,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :lecture_id
            ";
            
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':instructor_name' => $data['instructor_name'],
                ':instructor_info' => $data['instructor_info'] ?? null,
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':timezone' => $data['timezone'] ?? 'Asia/Seoul',
                ':location_type' => $data['location_type'] ?? 'offline',
                ':venue_name' => $data['venue_name'] ?? null,
                ':venue_address' => $data['venue_address'] ?? null,
                ':venue_latitude' => !empty($data['venue_latitude']) ? floatval($data['venue_latitude']) : null,
                ':venue_longitude' => !empty($data['venue_longitude']) ? floatval($data['venue_longitude']) : null,
                ':online_link' => $data['online_link'] ?? null,
                ':max_participants' => empty($data['max_participants']) ? null : intval($data['max_participants']),
                ':registration_fee' => intval($data['registration_fee'] ?? 0),
                ':registration_deadline' => $data['registration_deadline'] ?? null,
                ':category' => $data['category'] ?? 'seminar',
                ':content_type' => $data['content_type'] ?? 'lecture',
                ':instructors_json' => $data['instructors_json'] ?? null,
                ':lecture_images' => $data['lecture_images'] ?? null,
                ':requirements' => $data['requirements'] ?? null,
                ':benefits' => $data['benefits'] ?? null,
                ':youtube_video' => $data['youtube_video'] ?? null,
                ':status' => $data['status'] ?? 'draft',
                ':lecture_id' => $lectureId
            ];
            
            error_log("=== updateLecture 메서드 시작 ===");
            error_log("업데이트할 강의 ID: " . $lectureId);
            error_log("받은 데이터 - registration_deadline: " . ($data['registration_deadline'] ?? 'NULL'));
            error_log("받은 데이터 - youtube_video: " . ($data['youtube_video'] ?? 'NULL'));
            error_log("받은 데이터 - lecture_images 길이: " . (isset($data['lecture_images']) ? strlen($data['lecture_images']) : 'NULL'));
            error_log("SQL 파라미터 - registration_deadline: " . ($params[':registration_deadline'] ?? 'NULL'));
            error_log("SQL 파라미터 - youtube_video: " . ($params[':youtube_video'] ?? 'NULL'));
            error_log("SQL 파라미터 - lecture_images 길이: " . (isset($params[':lecture_images']) ? strlen($params[':lecture_images']) : 'NULL'));
            error_log("실행할 SQL 길이: " . strlen($sql));
            error_log("파라미터 배열 크기: " . count($params));
            error_log("파라미터 배열 empty 체크: " . (empty($params) ? 'YES' : 'NO'));
            
            // UPDATE 실행 직전에 정보 저장
            $GLOBALS['debug_before_execute'] = [
                'sql_length' => strlen($sql),
                'params_count' => count($params),
                'params_empty' => empty($params),
                'sql_starts_with_update' => stripos($sql, 'UPDATE') === 0,
                'sql_preview' => substr($sql, 0, 200)
            ];
            
            try {
                $result = $this->db->execute($sql, $params);
                
                // 글로벌 변수에 결과 저장 (응답에 포함시키기 위해)
                $GLOBALS['debug_sql_result'] = [
                    'affected_rows' => $result,
                    'registration_deadline_sent' => $data['registration_deadline'] ?? 'NULL',
                    'youtube_video_sent' => $data['youtube_video'] ?? 'NULL',
                    'error' => null,
                    'params_count' => count($params),
                    'has_registration_deadline_param' => isset($params[':registration_deadline']),
                    'has_youtube_video_param' => isset($params[':youtube_video'])
                ];
            } catch (Exception $sqlException) {
                // SQL 실행 에러 정보 저장
                $GLOBALS['debug_sql_result'] = [
                    'affected_rows' => -1,
                    'registration_deadline_sent' => $data['registration_deadline'] ?? 'NULL',
                    'youtube_video_sent' => $data['youtube_video'] ?? 'NULL',
                    'error' => $sqlException->getMessage()
                ];
                
                // 에러를 다시 throw (기존 로직 유지)
                throw $sqlException;
            }
            
            // 업데이트 결과 로깅
            $updateResult = [
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => 'updateLecture_result',
                'lecture_id' => $lectureId,
                'affected_rows' => $result,
                'registration_deadline_param' => $data['registration_deadline'] ?? 'NULL',
                'youtube_video_param' => $data['youtube_video'] ?? 'NULL'
            ];
            file_put_contents('/var/www/html/topmkt/public/debug.log', json_encode($updateResult) . "\n", FILE_APPEND);
            
            // 실제 저장된 데이터 확인
            $savedData = $this->db->fetch("SELECT registration_deadline, youtube_video, updated_at FROM lectures WHERE id = ?", [$lectureId]);
            $savedResult = [
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => 'actual_saved_data',
                'lecture_id' => $lectureId,
                'saved_registration_deadline' => $savedData['registration_deadline'] ?? 'NULL',
                'saved_youtube_video' => $savedData['youtube_video'] ?? 'NULL',
                'saved_updated_at' => $savedData['updated_at'] ?? 'NULL'
            ];
            file_put_contents('/var/www/html/topmkt/public/debug.log', json_encode($savedResult) . "\n", FILE_APPEND);
            
            // 강의가 published 상태로 업데이트되었다면 해당 사용자의 다른 draft 강의들을 삭제
            if (isset($data['status']) && $data['status'] === 'published') {
                $this->deleteDraftLectures($userId, $lectureId);
            }
            
            return $lectureId; // 업데이트된 강의의 ID 반환
            
        } catch (Exception $e) {
            error_log("updateLecture 예외: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * 이미지 업로드 처리
     */
    private function handleImageUploads($files) {
        $uploadedImages = [];
        
        error_log("=== 강의 이미지 업로드 처리 시작 ===");
        error_log("FILES['lecture_images'] 존재: " . (isset($files['lecture_images']) ? 'YES' : 'NO'));
        
        if (!isset($files['lecture_images']) || !is_array($files['lecture_images']['name'])) {
            error_log("강의 이미지 파일 없음");
            return $uploadedImages;
        }
        
        $uploadDir = '/var/www/html/topmkt/public/assets/uploads/lectures/';
        $webPath = '/assets/uploads/lectures/';
        
        // 업로드 디렉토리 생성
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        
        $images = $files['lecture_images'];
        $imageCount = count($images['name']);
        
        for ($i = 0; $i < $imageCount; $i++) {
            if ($images['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $images['tmp_name'][$i];
                $originalName = $images['name'][$i];
                $fileSize = $images['size'][$i];
                
                // UTF-8 인코딩 확인 및 정리
                if (!mb_check_encoding($originalName, 'UTF-8')) {
                    $originalName = mb_convert_encoding($originalName, 'UTF-8', 'auto');
                }
                
                error_log("원본 파일명: " . $originalName . " (길이: " . strlen($originalName) . ", UTF-8 체크: " . (mb_check_encoding($originalName, 'UTF-8') ? 'OK' : 'FAIL') . ")");
                
                // 파일 확장자 검증
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                
                if (!in_array($fileExt, $allowedTypes)) {
                    continue; // 허용되지 않는 파일 형식은 건너뛰기
                }
                
                // 파일 크기 검증 (5MB 제한)
                if ($fileSize > 5 * 1024 * 1024) {
                    continue; // 5MB 초과 파일은 건너뛰기
                }
                
                // 안전한 파일명 생성 (한글 지원)
                $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
                $baseFileName = pathinfo($originalName, PATHINFO_FILENAME);
                
                error_log("파일명 분해: 원본={$originalName}, 베이스={$baseFileName}, 확장자={$fileExtension}");
                
                // 한글과 영문을 모두 지원하는 안전한 파일명 생성
                $sanitizedFileName = $this->sanitizeFileName($baseFileName);
                $safeName = time() . '_' . $i . '_' . $sanitizedFileName . '.' . $fileExtension;
                
                error_log("최종 파일명: {$safeName}");
                $filePath = $uploadDir . $safeName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $imageData = [
                        'original_name' => $safeName,  // 안전한 파일명 사용
                        'file_name' => $safeName,
                        'file_path' => $webPath . $safeName,
                        'file_size' => $fileSize,
                        'upload_time' => date('Y-m-d H:i:s')
                    ];
                    $uploadedImages[] = $imageData;
                    error_log("이미지 업로드 성공: " . $safeName . " -> file_path: " . $imageData['file_path']);
                } else {
                    error_log("이미지 업로드 실패: " . $originalName . " -> " . $filePath);
                }
            }
        }
        
        return $uploadedImages;
    }
    
    /**
     * 강사 이미지 업로드 처리
     */
    private function handleInstructorImageUploads($files) {
        // 직접 파일에 로그 기록 (디버깅용)
        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "=== handleInstructorImageUploads 함수 호출됨 ===\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "시간: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "FILES 키들: " . json_encode(array_keys($files)) . "\n", FILE_APPEND);
        
        $instructorImages = [];
        
        error_log("=== 강사 이미지 업로드 처리 시작 ===");
        error_log("전체 FILES 배열: " . json_encode(array_keys($files)));
        
        // FILES 구조 상세 분석
        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "=== FILES 구조 상세 분석 ===\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "전체 FILES: " . json_encode($files) . "\n", FILE_APPEND);
        
        // PHP 다차원 배열 구조로 전송된 강사 이미지 처리
        if (isset($files['instructors']) && is_array($files['instructors'])) {
            $instructorsFiles = $files['instructors'];
            file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "instructors 파일 데이터 발견\n", FILE_APPEND);
            
            // PHP 다차원 파일 업로드 구조 처리
            if (isset($instructorsFiles['name']) && is_array($instructorsFiles['name'])) {
                foreach ($instructorsFiles['name'] as $index => $nameData) {
                    if (isset($nameData['image']) && !empty($nameData['image'])) {
                        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "강사 {$index} 이미지 처리 시작\n", FILE_APPEND);
                        
                        $originalName = $nameData['image'];
                        $tmpName = $instructorsFiles['tmp_name'][$index]['image'] ?? '';
                        $fileType = $instructorsFiles['type'][$index]['image'] ?? '';
                        $fileError = $instructorsFiles['error'][$index]['image'] ?? UPLOAD_ERR_NO_FILE;
                        $fileSize = $instructorsFiles['size'][$index]['image'] ?? 0;
                        
                        file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "파일 정보: {$originalName}, tmp: {$tmpName}, error: {$fileError}\n", FILE_APPEND);
                        
                        if ($fileError === UPLOAD_ERR_OK && !empty($tmpName) && is_uploaded_file($tmpName)) {
                            $uploadDir = '/var/www/html/topmkt/public/assets/uploads/instructors/';
                            $webPath = '/assets/uploads/instructors/';
                            
                            // 업로드 디렉토리 생성
                            if (!is_dir($uploadDir)) {
                                @mkdir($uploadDir, 0755, true);
                            }
                            
                            // 파일 확장자 검증
                            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                            
                            if (!in_array($fileExt, $allowedTypes)) {
                                file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "강사 {$index} 이미지 형식 불허용: {$fileExt}\n", FILE_APPEND);
                                continue;
                            }
                            
                            // 파일 크기 검증 (2MB 제한)
                            if ($fileSize > 2 * 1024 * 1024) {
                                file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "강사 {$index} 이미지 크기 초과: {$fileSize}\n", FILE_APPEND);
                                continue;
                            }
                            
                            // 안전한 파일명 생성 (한글 지원)
                            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
                            $baseFileName = pathinfo($originalName, PATHINFO_FILENAME);
                            $safeName = 'instructor_' . $index . '_' . time() . '_' . $this->sanitizeFileName($baseFileName) . '.' . $fileExtension;
                            $filePath = $uploadDir . $safeName;
                            
                            if (move_uploaded_file($tmpName, $filePath)) {
                                $instructorImages[$index] = $webPath . $safeName;
                                file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "강사 {$index} 이미지 업로드 성공: {$webPath}{$safeName}\n", FILE_APPEND);
                                error_log("강사 {$index} 이미지 업로드 성공: " . $webPath . $safeName);
                            } else {
                                file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "강사 {$index} 이미지 업로드 실패\n", FILE_APPEND);
                                error_log("강사 {$index} 이미지 업로드 실패");
                            }
                        } else {
                            file_put_contents('/var/www/html/topmkt/debug_instructor_images.log', "강사 {$index} 파일 에러 또는 임시파일 없음\n", FILE_APPEND);
                        }
                    }
                }
            }
        }
        
        error_log("강사 이미지 처리 결과: " . json_encode($instructorImages));
        return $instructorImages;
    }
    
    /**
     * 강의 이미지 업데이트 (삭제 처리)
     */
    public function updateImages() {
        // 오류 출력 방지
        ini_set('display_errors', 0);
        error_reporting(0);
        
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => '허용되지 않은 요청 방식입니다.']);
                exit;
            }
            
            // 로그인 상태 확인
            require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
            if (!AuthMiddleware::isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
                exit;
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => '사용자 정보를 찾을 수 없습니다.']);
                exit;
            }
            
            // 기업회원 권한 확인
            require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
            $permission = CorporateMiddleware::checkLectureEventPermission();
            if (!$permission['hasPermission']) {
                echo json_encode(['success' => false, 'message' => $permission['message']]);
                exit;
            }
            
            // CSRF 토큰 검증
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'CSRF 토큰이 유효하지 않습니다.']);
                exit;
            }
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'update_images') {
                $lectureImages = $_POST['lecture_images'] ?? '[]';
                
                // 사용자의 최신 draft 강의 조회
                $draftLecture = $this->getLatestDraftLecture($userId);
                
                if (!$draftLecture) {
                    echo json_encode(['success' => false, 'message' => '임시저장된 강의를 찾을 수 없습니다.']);
                    exit;
                }
                
                // 강의 이미지 업데이트
                $sql = "UPDATE lectures SET lecture_images = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
                $result = $this->db->execute($sql, [$lectureImages, $draftLecture['id'], $userId]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => '이미지가 성공적으로 업데이트되었습니다.']);
                } else {
                    echo json_encode(['success' => false, 'message' => '이미지 업데이트에 실패했습니다.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => '유효하지 않은 액션입니다.']);
            }
        } catch (Exception $e) {
            error_log('이미지 업데이트 오류: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.']);
        }
    }
    
    /**
     * 가장 최근 임시저장된 강의 조회
     */
    private function getLatestDraftLecture($userId) {
        try {
            $sql = "
                SELECT * FROM lectures 
                WHERE status = 'draft' 
                AND content_type = 'lecture'
                AND user_id = :user_id
                ORDER BY updated_at DESC, created_at DESC 
                LIMIT 1
            ";
            
            $result = $this->db->fetch($sql, [':user_id' => $userId]);
            
            if ($result) {
                // 디버깅: 데이터베이스에서 로드된 원본 데이터 확인
                error_log("=== getLatestDraftLecture 디버깅 ===");
                error_log("DB에서 로드된 lecture_images 원본: " . $result['lecture_images']);
                error_log("lecture_images 길이: " . strlen($result['lecture_images']));
                
                // instructors_json 파싱
                if (!empty($result['instructors_json'])) {
                    $result['instructors'] = json_decode($result['instructors_json'], true);
                }
                
                // lecture_images도 파싱
                if (!empty($result['lecture_images'])) {
                    try {
                        $parsed = json_decode($result['lecture_images'], true);
                        error_log("파싱된 lecture_images: " . json_encode($parsed));
                        error_log("파싱된 이미지 개수: " . (is_array($parsed) ? count($parsed) : 'NOT_ARRAY'));
                        $result['lecture_images'] = $parsed;
                    } catch (Exception $e) {
                        error_log("강의 이미지 JSON 파싱 오류: " . $e->getMessage());
                        $result['lecture_images'] = null;
                    }
                } else {
                    error_log("lecture_images 필드가 비어있음");
                    $result['lecture_images'] = null;
                }
                
                return $result;
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("임시저장 강의 조회 오류: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * CSRF 토큰 검증
     */
    private function validateCsrfToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    /**
     * 강의 신청 처리
     */
    public function register($id) {
        try {
            // 로그인 확인
            if (!isset($_SESSION['user_id'])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    ResponseHelper::error('로그인이 필요합니다.', 401);
                    return;
                } else {
                    header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
                    exit;
                }
            }
            
            $lectureId = intval($id);
            $userId = $_SESSION['user_id'];
            
            // 강의 정보 확인
            $lecture = $this->getLectureById($lectureId);
            if (!$lecture) {
                ResponseHelper::error('존재하지 않는 강의입니다.', 404);
                return;
            }
            
            // 신청 가능 여부 확인
            if (!$this->canRegisterLecture($lecture)) {
                ResponseHelper::error('신청할 수 없는 강의입니다.', 400);
                return;
            }
            
            // 이미 신청한 사용자인지 확인
            $existingRegistration = $this->getUserRegistration($lectureId, $userId);
            if ($existingRegistration) {
                ResponseHelper::error('이미 신청한 강의입니다.', 400);
                return;
            }
            
            // 사용자 정보 가져오기
            $user = $this->userModel->findById($userId);
            if (!$user) {
                ResponseHelper::error('사용자 정보를 찾을 수 없습니다.', 404);
                return;
            }
            
            // 신청 데이터 저장
            $registrationData = [
                'lecture_id' => $lectureId,
                'user_id' => $userId,
                'participant_name' => $user['nickname'],
                'participant_email' => $user['email'],
                'participant_phone' => $user['phone'],
                'status' => 'confirmed'
            ];
            
            $sql = "
                INSERT INTO lecture_registrations (
                    lecture_id, user_id, participant_name, participant_email, 
                    participant_phone, status, registration_date
                ) VALUES (
                    :lecture_id, :user_id, :participant_name, :participant_email,
                    :participant_phone, :status, NOW()
                )
            ";
            
            $this->db->beginTransaction();
            
            try {
                // 신청 정보 저장
                $this->db->execute($sql, $registrationData);
                
                // 강의 신청자 수 업데이트
                $this->db->execute(
                    "UPDATE lectures SET registration_count = registration_count + 1 WHERE id = :id",
                    [':id' => $lectureId]
                );
                
                $this->db->commit();
                
                ResponseHelper::sendSuccess([
                    'message' => '강의 신청이 완료되었습니다.',
                    'redirectUrl' => '/lectures/' . $lectureId
                ]);
                
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("강의 신청 오류: " . $e->getMessage());
            ResponseHelper::error('강의 신청 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * iCal 파일 생성
     */
    public function generateICal($id) {
        try {
            $lectureId = intval($id);
            $lecture = $this->getLectureById($lectureId);
            
            if (!$lecture) {
                header("HTTP/1.0 404 Not Found");
                return;
            }
            
            // iCal 내용 생성
            $ical = $this->createICalContent($lecture);
            
            // 헤더 설정
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename="lecture_' . $lectureId . '.ics"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            echo $ical;
            
        } catch (Exception $e) {
            error_log("iCal 생성 오류: " . $e->getMessage());
            header("HTTP/1.0 500 Internal Server Error");
            echo "iCal 파일 생성 중 오류가 발생했습니다.";
        }
    }
    
    /**
     * iCal 콘텐츠 생성
     */
    private function createICalContent($lecture) {
        $startDateTime = $lecture['start_date'] . 'T' . str_replace(':', '', $lecture['start_time']) . '00';
        $endDateTime = $lecture['end_date'] . 'T' . str_replace(':', '', $lecture['end_time']) . '00';
        $now = date('Ymd\THis\Z');
        
        $location = '';
        if ($lecture['location_type'] === 'online') {
            $location = '온라인';
            if (!empty($lecture['online_link'])) {
                $location .= ' - ' . $lecture['online_link'];
            }
        } elseif (!empty($lecture['venue_name'])) {
            $location = $lecture['venue_name'];
            if (!empty($lecture['venue_address'])) {
                $location .= ', ' . $lecture['venue_address'];
            }
        }
        
        $description = $lecture['description'];
        if (!empty($lecture['instructor_name'])) {
            $description .= "\\n\\n강사: " . $lecture['instructor_name'];
        }
        if (!empty($lecture['registration_fee']) && $lecture['registration_fee'] > 0) {
            $description .= "\\n참가비: " . number_format($lecture['registration_fee']) . "원";
        }
        
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//TopMarketing//Lecture Calendar//KO\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:lecture-" . $lecture['id'] . "-" . time() . "@topmarketing.kr\r\n";
        $ical .= "DTSTAMP:" . $now . "\r\n";
        $ical .= "DTSTART:" . $startDateTime . "\r\n";
        $ical .= "DTEND:" . $endDateTime . "\r\n";
        $ical .= "SUMMARY:" . $this->escapeICalText($lecture['title']) . "\r\n";
        $ical .= "DESCRIPTION:" . $this->escapeICalText($description) . "\r\n";
        if (!empty($location)) {
            $ical .= "LOCATION:" . $this->escapeICalText($location) . "\r\n";
        }
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "SEQUENCE:0\r\n";
        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR\r\n";
        
        return $ical;
    }
    
    /**
     * iCal 텍스트 이스케이프
     */
    private function escapeICalText($text) {
        $text = str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $text);
        return $text;
    }
    
    /**
     * iCal URL 생성
     */
    private function generateICalUrl($lectureId) {
        return '/lectures/' . $lectureId . '/ical';
    }
    
    /**
     * 강의 테이블 존재 확인
     */
    private function checkLectureTablesExist() {
        try {
            $tables = $this->db->fetchAll("SHOW TABLES LIKE 'lectures'");
            return !empty($tables);
        } catch (Exception $e) {
            // 데이터베이스 연결 실패 시 데모 모드로 동작
            // 실제로는 테이블이 없지만 데모 데이터를 사용할 수 있도록 true 반환
            return true;
        }
    }
    
    /**
     * 설정 페이지 표시
     */
    private function showSetupPage() {
        $headerData = [
            'title' => '강의 시스템 설정 - 탑마케팅',
            'description' => '강의 시스템을 초기화합니다',
            'pageSection' => 'lectures'
        ];
        
        $this->renderView('lectures/setup', [], $headerData);
    }
    
    /**
     * 오류 페이지 표시
     */
    private function showErrorPage($message, $details = '') {
        $headerData = [
            'title' => '오류 발생 - 탑마케팅',
            'description' => '강의 시스템에서 오류가 발생했습니다',
            'pageSection' => 'lectures'
        ];
        
        $viewData = [
            'errorMessage' => $message,
            'errorDetails' => $details
        ];
        
        $this->renderView('lectures/error', $viewData, $headerData);
    }
    
    /**
     * 파일명 안전하게 처리 (ASCII 전용, 한글 문제 방지)
     */
    private function sanitizeFileName($filename) {
        // 한글이나 특수문자가 있으면 무조건 유니크 파일명 생성
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename) || empty(trim($filename))) {
            $uniqueName = 'file_' . uniqid();
            error_log("파일명에 특수문자/한글 발견: " . $filename . " -> " . $uniqueName);
            return $uniqueName;
        }
        
        // ASCII 영숫자와 안전한 문자만 있는 경우 정리 후 반환
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $sanitized = preg_replace('/\s+/', '_', $sanitized);
        $sanitized = trim($sanitized, '._-');
        
        // 빈 파일명 방지
        if (empty($sanitized)) {
            $sanitized = 'file_' . uniqid();
        }
        
        error_log("안전한 파일명 생성: " . $filename . " -> " . $sanitized);
        return $sanitized;
    }

    /**
     * 강의 이미지 조회
     */
    private function getLectureImages($lectureId) {
        try {
            // lectures 테이블에서 lecture_images JSON 필드를 조회
            $sql = "SELECT lecture_images FROM lectures WHERE id = :lecture_id";
            $result = $this->db->fetch($sql, [':lecture_id' => $lectureId]);
            
            
            if (!$result || empty($result['lecture_images'])) {
                error_log("강의 이미지 데이터 없음 - lecture_images 필드가 비어있음");
                return [];
            }
            
            // JSON 디코드
            $imagesData = json_decode($result['lecture_images'], true);
            if (!is_array($imagesData)) {
                return [];
            }
            
            // display_order가 있으면 그 순서대로 정렬
            if (!empty($imagesData) && isset($imagesData[0]['display_order'])) {
                usort($imagesData, function($a, $b) {
                    $orderA = $a['display_order'] ?? 999;
                    $orderB = $b['display_order'] ?? 999;
                    return $orderA - $orderB;
                });
                error_log("이미지를 display_order로 정렬 완료: " . json_encode(array_column($imagesData, 'display_order')));
            }
            
            // 강의 이미지 데이터를 뷰에서 사용할 형태로 변환 (모든 필드 보존)
            $formattedImages = array_map(function($image, $index) {
                // file_path가 없는 경우 file_name으로 경로 생성
                $imagePath = '';
                if (!empty($image['file_path'])) {
                    $imagePath = $image['file_path'];
                } elseif (!empty($image['file_name'])) {
                    // file_name으로 강의 이미지 경로 생성
                    $imagePath = '/assets/uploads/lectures/' . $image['file_name'];
                }
                
                // 모든 원본 필드를 보존하면서 추가 필드 포함
                $formattedImage = $image; // 원본 데이터 보존
                $formattedImage['id'] = $index + 1;
                $formattedImage['url'] = $imagePath;
                $formattedImage['alt_text'] = $image['original_name'] ?? '강의 이미지';
                
                return $formattedImage;
            }, $imagesData, array_keys($imagesData));
            
            return $formattedImages;
            
        } catch (Exception $e) {
            error_log("강의 이미지 조회 오류: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 뷰 렌더링
     */
    private function renderView($view, $data = [], $headerData = []) {
        try {
            // 뷰 파일 경로를 먼저 저장 (extract 전에)
            $viewPath = SRC_PATH . '/views/' . $view . '.php';
            
            // 데이터 추출 (PHP extract 사용)
            extract($data);
            extract($headerData);
            
            // 헤더 렌더링
            require_once SRC_PATH . '/views/templates/header.php';
            
            // 메인 뷰 렌더링
            if (file_exists($viewPath)) {
                require_once $viewPath;
            } else {
                echo "<div class='error-message'>뷰 파일을 찾을 수 없습니다: {$view}</div>";
            }
            
            // 푸터 렌더링
            require_once SRC_PATH . '/views/templates/footer.php';
            
        } catch (Exception $e) {
            error_log("뷰 렌더링 오류: " . $e->getMessage());
            echo "<div class='error-message'>페이지 렌더링 중 오류가 발생했습니다.</div>";
        }
    }
    
    /**
     * 사용자의 임시저장(draft) 강의들을 삭제
     */
    private function deleteDraftLectures($userId, $excludeId = null) {
        try {
            $sql = "DELETE FROM lectures WHERE user_id = :user_id AND status = 'draft'";
            $params = [':user_id' => $userId];
            
            // 현재 생성된 강의는 제외
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            
            $result = $this->db->execute($sql, $params);
            
            if ($result) {
                error_log("사용자 {$userId}의 임시저장 강의들이 삭제되었습니다. (제외: {$excludeId})");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("임시저장 강의 삭제 중 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process legacy image merging logic (existing behavior)
     * Used as fallback when ordered_lecture_images is not provided
     */
    private function processLegacyImageMerging($debugLog, &$existingImages, $uploadedImages, &$finalLectureImages) {
        // 1. 기존 이미지 먼저 추가
        if (isset($_POST['existing_lecture_images']) && !empty($_POST['existing_lecture_images'])) {
            file_put_contents($debugLog, "기존 이미지 JSON 길이: " . strlen($_POST['existing_lecture_images']) . "\n", FILE_APPEND);
            try {
                $existingImages = json_decode($_POST['existing_lecture_images'], true);
                file_put_contents($debugLog, "JSON 디코드 결과: " . var_export($existingImages, true) . "\n", FILE_APPEND);
                if (is_array($existingImages)) {
                    // Ensure all existing images have file_path field
                    foreach ($existingImages as &$existingImage) {
                        if (!isset($existingImage['file_path']) && isset($existingImage['file_name'])) {
                            $existingImage['file_path'] = '/assets/uploads/lectures/' . $existingImage['file_name'];
                            file_put_contents($debugLog, "기존 이미지 file_path 누락으로 생성됨: " . $existingImage['file_path'] . "\n", FILE_APPEND);
                        }
                    }
                    unset($existingImage); // Clean up reference
                    
                    $finalLectureImages = $existingImages;
                    file_put_contents($debugLog, "기존 강의 이미지 병합: " . count($existingImages) . "개\n", FILE_APPEND);
                    error_log("기존 강의 이미지 병합: " . count($existingImages) . "개");
                } else {
                    file_put_contents($debugLog, "경고: 기존 이미지가 배열이 아님\n", FILE_APPEND);
                }
            } catch (Exception $e) {
                file_put_contents($debugLog, "기존 강의 이미지 파싱 오류: " . $e->getMessage() . "\n", FILE_APPEND);
                error_log("기존 강의 이미지 파싱 오류: " . $e->getMessage());
            }
        } else {
            file_put_contents($debugLog, "기존 이미지 없음\n", FILE_APPEND);
        }
        
        // 2. 새 이미지 추가 (중복 제거)
        if (!empty($uploadedImages)) {
            file_put_contents($debugLog, "새 이미지 추가 전 기존 이미지 수: " . count($finalLectureImages) . "\n", FILE_APPEND);
            
            // 중복 이미지 제거 - 파일명 기준으로 중복 체크
            $existingFileNames = [];
            foreach ($finalLectureImages as $existingImage) {
                if (isset($existingImage['file_name'])) {
                    $existingFileNames[] = $existingImage['file_name'];
                }
            }
            
            // 새 이미지 중에서 중복되지 않는 것만 추가
            foreach ($uploadedImages as $newImage) {
                if (isset($newImage['file_name']) && !in_array($newImage['file_name'], $existingFileNames)) {
                    // Ensure file_path is always set for new images
                    if (!isset($newImage['file_path']) && isset($newImage['file_name'])) {
                        $newImage['file_path'] = '/assets/uploads/lectures/' . $newImage['file_name'];
                        file_put_contents($debugLog, "Legacy 처리: file_path 누락으로 생성됨: " . $newImage['file_path'] . "\n", FILE_APPEND);
                    }
                    
                    $finalLectureImages[] = $newImage;
                    $existingFileNames[] = $newImage['file_name']; // 추가한 파일명도 중복 체크 목록에 추가
                    file_put_contents($debugLog, "새 이미지 추가: " . $newImage['file_name'] . " (file_path: " . ($newImage['file_path'] ?? 'MISSING') . ")\n", FILE_APPEND);
                } else {
                    file_put_contents($debugLog, "중복 이미지 제외: " . ($newImage['file_name'] ?? 'UNKNOWN') . "\n", FILE_APPEND);
                }
            }
            
            file_put_contents($debugLog, "중복 제거 후 총 이미지 수: " . count($finalLectureImages) . "\n", FILE_APPEND);
            error_log("새 강의 이미지 추가 (중복 제거 후): " . count($finalLectureImages) . "개");
        } else {
            file_put_contents($debugLog, "새 이미지 없음\n", FILE_APPEND);
        }
    }
}
?>