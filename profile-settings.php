<?php
/**
 * 프로필 설정 페이지
 * 
 * 사용자가 프로필 정보(회사명, 자기소개, 프로필 이미지)를 관리할 수 있는 페이지입니다.
 */

session_start();

// 로그인 체크
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth.php');
    exit();
}

$title = '프로필 설정';
include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-3">
            <!-- 사이드바 메뉴 -->
            <div class="card mb-4">
                <div class="card-header">내 계정</div>
                <div class="list-group list-group-flush">
                    <a href="/profile-settings.php" class="list-group-item list-group-item-action active">프로필 설정</a>
                    <a href="/account-settings.php" class="list-group-item list-group-item-action">계정 설정</a>
                    <a href="/notification-settings.php" class="list-group-item list-group-item-action">알림 설정</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- 프로필 설정 폼 -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">프로필 설정</h5>
                </div>
                <div class="card-body">
                    <!-- 알림 메시지 -->
                    <div id="alertMessage" class="alert" role="alert" style="display: none;"></div>
                    
                    <!-- 프로필 이미지 -->
                    <div class="mb-4 text-center">
                        <div class="profile-image-container mb-3">
                            <img id="profileImage" src="/public/assets/images/default-profile.png" alt="프로필 이미지" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="profile-image-overlay">
                                <label for="profileImageInput" class="btn btn-sm btn-light rounded-circle">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                        </div>
                        <input type="file" id="profileImageInput" class="d-none" accept="image/*">
                        <small class="text-muted d-block">PNG, JPG 또는 GIF 파일, 최대 5MB</small>
                    </div>
                    
                    <form id="profileForm">
                        <!-- 기본 정보 -->
                        <div class="mb-3">
                            <label for="nickname" class="form-label">닉네임</label>
                            <input type="text" class="form-control" id="nickname" placeholder="닉네임" readonly>
                            <small class="text-muted">닉네임은 변경할 수 없습니다.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company" class="form-label">회사명</label>
                            <input type="text" class="form-control" id="company" placeholder="회사명을 입력하세요" maxlength="100">
                        </div>
                        
                        <div class="mb-3">
                            <label for="introduction" class="form-label">자기소개</label>
                            <textarea class="form-control" id="introduction" rows="4" placeholder="자기소개를 입력하세요" maxlength="5000"></textarea>
                            <small class="text-muted"><span id="introductionLength">0</span>/5000자</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">이메일</label>
                            <input type="email" class="form-control" id="email" placeholder="이메일을 입력하세요">
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">포지션</label>
                            <select class="form-select" id="position">
                                <option value="leader">리더</option>
                                <option value="sales">영업</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">저장하기</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 프로필 설정 스크립트 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 요소 참조
    const profileForm = document.getElementById('profileForm');
    const profileImage = document.getElementById('profileImage');
    const profileImageInput = document.getElementById('profileImageInput');
    const nicknameInput = document.getElementById('nickname');
    const companyInput = document.getElementById('company');
    const introductionInput = document.getElementById('introduction');
    const emailInput = document.getElementById('email');
    const positionInput = document.getElementById('position');
    const introductionLength = document.getElementById('introductionLength');
    const alertMessage = document.getElementById('alertMessage');
    
    // 프로필 이미지 URL
    let profileImageUrl = '';
    
    // 알림 메시지 표시 함수
    function showAlert(message, type = 'success') {
        alertMessage.textContent = message;
        alertMessage.className = `alert alert-${type}`;
        alertMessage.style.display = 'block';
        
        // 5초 후 자동으로 사라짐
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }
    
    // 사용자 프로필 정보 로드
    async function loadProfileData() {
        try {
            const response = await fetch('/api/user/profile.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success && data.data) {
                // 프로필 데이터 설정
                nicknameInput.value = data.data.nickname || '';
                companyInput.value = data.data.company || '';
                introductionInput.value = data.data.introduction || '';
                emailInput.value = data.data.email || '';
                positionInput.value = data.data.position || 'leader';
                
                // 프로필 이미지 설정
                if (data.data.profile_image) {
                    profileImage.src = data.data.profile_image;
                    profileImageUrl = data.data.profile_image;
                }
                
                // 자기소개 길이 업데이트
                introductionLength.textContent = introductionInput.value.length;
            }
        } catch (error) {
            console.error('프로필 정보 로드 오류:', error);
            showAlert('프로필 정보를 불러오는 중 오류가 발생했습니다.', 'danger');
        }
    }
    
    // 프로필 이미지 업로드
    async function uploadProfileImage(file) {
        // 파일 크기 검증 (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('이미지 크기는 5MB를 초과할 수 없습니다.', 'danger');
            return null;
        }
        
        // 이미지 파일 타입 검증
        if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
            showAlert('지원되는 이미지 형식은 JPEG, PNG, GIF, WEBP 입니다.', 'danger');
            return null;
        }
        
        try {
            // 이미지를 Base64로 변환
            const base64Data = await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });
            
            // 서버에 업로드
            const response = await fetch('/api/user/upload-profile-image.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image: base64Data }),
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success && data.url) {
                showAlert('프로필 이미지가 업로드되었습니다.');
                return data.url;
            } else {
                showAlert(data.message || '이미지 업로드에 실패했습니다.', 'danger');
                return null;
            }
        } catch (error) {
            console.error('이미지 업로드 오류:', error);
            showAlert('이미지 업로드 중 오류가 발생했습니다.', 'danger');
            return null;
        }
    }
    
    // 프로필 정보 업데이트
    async function updateProfile(profileData) {
        try {
            const response = await fetch('/api/user/update-profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(profileData),
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('프로필 정보가 성공적으로 업데이트되었습니다.');
                
                // 업데이트된 정보 반영
                if (data.data) {
                    if (data.data.company) companyInput.value = data.data.company;
                    if (data.data.introduction) introductionInput.value = data.data.introduction;
                    if (data.data.email) emailInput.value = data.data.email;
                    if (data.data.position) positionInput.value = data.data.position;
                    if (data.data.profile_image) {
                        profileImage.src = data.data.profile_image;
                        profileImageUrl = data.data.profile_image;
                    }
                }
                
                return true;
            } else {
                showAlert(data.message || '프로필 업데이트에 실패했습니다.', 'danger');
                return false;
            }
        } catch (error) {
            console.error('프로필 업데이트 오류:', error);
            showAlert('프로필 업데이트 중 오류가 발생했습니다.', 'danger');
            return false;
        }
    }
    
    // 이벤트 리스너: 프로필 이미지 선택
    profileImageInput.addEventListener('change', async function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            // 이미지 미리보기
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
            
            // 이미지 업로드
            const imageUrl = await uploadProfileImage(file);
            
            if (imageUrl) {
                profileImageUrl = imageUrl;
                
                // 프로필 업데이트
                await updateProfile({ profile_image: imageUrl });
            } else {
                // 업로드 실패 시 원래 이미지로 복원
                profileImage.src = profileImageUrl || '/public/assets/images/default-profile.png';
            }
        }
    });
    
    // 이벤트 리스너: 자기소개 길이 계산
    introductionInput.addEventListener('input', function() {
        introductionLength.textContent = this.value.length;
    });
    
    // 이벤트 리스너: 프로필 폼 제출
    profileForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // 폼 데이터 수집
        const profileData = {
            company: companyInput.value.trim(),
            introduction: introductionInput.value.trim(),
            email: emailInput.value.trim(),
            position: positionInput.value
        };
        
        // 프로필 업데이트
        await updateProfile(profileData);
    });
    
    // 초기 프로필 데이터 로드
    loadProfileData();
});
</script>

<style>
.profile-image-container {
    position: relative;
    display: inline-block;
}

.profile-image-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
}
</style>

<?php include 'includes/footer.php'; ?> 