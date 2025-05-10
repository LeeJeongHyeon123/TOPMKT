// 국가 선택 드롭다운 및 국가별 전화번호 처리 모듈
// - 국가 드롭다운 토글, 옵션 선택, 전화번호 포맷/유효성 검사 등

// 국가별 전화번호 포맷 정의
export const phoneFormats = {
    '+82': {
        validate: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            return numbers.length >= 10 && numbers.length <= 11;
        },
        format: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            if (numbers.length <= 3) return numbers;
            if (numbers.length <= 7) return `${numbers.slice(0, 3)}-${numbers.slice(3)}`;
            return `${numbers.slice(0, 3)}-${numbers.slice(3, 7)}-${numbers.slice(7)}`;
        }
    },
    '+1': {
        validate: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            return numbers.length === 10;
        },
        format: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            if (numbers.length <= 3) return numbers;
            if (numbers.length <= 6) return `${numbers.slice(0, 3)}-${numbers.slice(3)}`;
            return `${numbers.slice(0, 3)}-${numbers.slice(3, 6)}-${numbers.slice(6)}`;
        }
    },
    '+86': {
        placeholder: '138-1234-5678',
        pattern: /^1[3-9][0-9]{2}-[0-9]{4}-[0-9]{4}$/,
        format: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            if (numbers.length <= 4) return numbers;
            if (numbers.length <= 8) return `${numbers.slice(0, 4)}-${numbers.slice(4)}`;
            return `${numbers.slice(0, 4)}-${numbers.slice(4, 8)}-${numbers.slice(8, 12)}`;
        },
        validate: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            return numbers.length === 11 && numbers.startsWith('1');
        }
    },
    '+886': {
        placeholder: '0912-345-678',
        pattern: /^09[0-9]{2}-[0-9]{3}-[0-9]{3}$/,
        format: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            if (numbers.length <= 4) return numbers;
            if (numbers.length <= 7) return `${numbers.slice(0, 4)}-${numbers.slice(4)}`;
            return `${numbers.slice(0, 4)}-${numbers.slice(4, 7)}-${numbers.slice(7, 10)}`;
        },
        validate: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            return numbers.length === 10 && numbers.startsWith('09');
        }
    },
    '+81': {
        placeholder: '090-1234-5678',
        pattern: /^0[0-9]{2}-[0-9]{4}-[0-9]{4}$/,
        format: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            if (numbers.length <= 3) return numbers;
            if (numbers.length <= 7) return `${numbers.slice(0, 3)}-${numbers.slice(3)}`;
            return `${numbers.slice(0, 3)}-${numbers.slice(3, 7)}-${numbers.slice(7, 11)}`;
        },
        validate: (value) => {
            const numbers = value.replace(/[^0-9]/g, '');
            return numbers.length === 11 && numbers.startsWith('0');
        }
    }
};

// 전화번호 입력 처리
export function handlePhoneInput(input, countryCode) {
    const format = phoneFormats[countryCode];
    if (!format) return;
    
    const formatted = format.format(input.value);
    input.value = formatted;
}

// 전화번호 입력 필드 업데이트
export function updatePhoneInput(countryCode, input) {
    if (!input) return;
    
    const format = phoneFormats[countryCode];
    if (!format) return;
    
    const formatted = format.format(input.value);
    input.value = formatted;
    input.placeholder = countryCode === '+82' ? '010-1234-1234' : '123-456-7890';
}

// 국가 선택 드롭다운 토글
export function toggleCountryDropdown(dropdown) {
    if (!dropdown) {
        console.error('드롭다운 요소가 없습니다.');
        return;
    }
    
    console.log('드롭다운 토글 시작:', {
        id: dropdown.id,
        currentClasses: dropdown.className,
        isVisible: dropdown.classList.contains('show')
    });
    
    // 다른 모든 드롭다운 닫기
    document.querySelectorAll('.country-dropdown').forEach(d => {
        if (d !== dropdown) {
            console.log('다른 드롭다운 닫기:', d.id);
            d.classList.remove('show');
        }
    });
    
    // 현재 드롭다운 토글
    dropdown.classList.toggle('show');
    
    console.log('드롭다운 상태 변경:', {
        id: dropdown.id,
        isVisible: dropdown.classList.contains('show'),
        classes: dropdown.className
    });
}

// 초기화 상태 추적을 위한 변수
let isCountryDropdownsInitialized = false;

// 국가 선택 드롭다운 초기화
export function initializeCountryDropdowns() {
    // 이미 초기화되었다면 중복 실행 방지
    if (isCountryDropdownsInitialized) {
        console.log('국가 선택 드롭다운이 이미 초기화되어 있습니다.');
        return;
    }

    console.log('국가 선택 드롭다운 초기화 시작');
    
    const loginCountrySelect = document.getElementById('loginCountrySelect');
    const loginCountryDropdown = document.getElementById('loginCountryDropdown');
    const registerCountrySelect = document.getElementById('registerCountrySelect');
    const registerCountryDropdown = document.getElementById('registerCountryDropdown');

    console.log('드롭다운 요소 확인:', {
        loginSelect: loginCountrySelect?.id,
        loginDropdown: loginCountryDropdown?.id,
        registerSelect: registerCountrySelect?.id,
        registerDropdown: registerCountryDropdown?.id
    });

    // 전역 클릭 이벤트 핸들러
    const handleGlobalClick = (e) => {
        const isClickInsideDropdown = e.target.closest('.country-dropdown');
        const isClickInsideSelect = e.target.closest('.country-select');
        
        console.log('전역 클릭 이벤트:', {
            target: e.target.id || e.target.className,
            isClickInsideDropdown,
            isClickInsideSelect
        });
        
        if (!isClickInsideDropdown && !isClickInsideSelect) {
            document.querySelectorAll('.country-dropdown').forEach(dropdown => {
                if (dropdown.classList.contains('show')) {
                    console.log('드롭다운 닫기:', dropdown.id);
                    dropdown.classList.remove('show');
                }
            });
        }
    };

    // 로그인 국가 선택 이벤트
    if (loginCountrySelect && loginCountryDropdown) {
        // 기존 이벤트 리스너 제거
        loginCountrySelect.removeEventListener('click', handleLoginCountrySelect);
        loginCountryDropdown.removeEventListener('click', handleDropdownClick);
        
        // 새로운 이벤트 리스너 등록
        loginCountrySelect.addEventListener('click', handleLoginCountrySelect);
        loginCountryDropdown.addEventListener('click', handleDropdownClick);
    }

    // 회원가입 국가 선택 이벤트
    if (registerCountrySelect && registerCountryDropdown) {
        // 기존 이벤트 리스너 제거
        registerCountrySelect.removeEventListener('click', handleRegisterCountrySelect);
        registerCountryDropdown.removeEventListener('click', handleDropdownClick);
        
        // 새로운 이벤트 리스너 등록
        registerCountrySelect.addEventListener('click', handleRegisterCountrySelect);
        registerCountryDropdown.addEventListener('click', handleDropdownClick);
    }

    // 전역 클릭 이벤트 등록
    document.removeEventListener('click', handleGlobalClick);
    document.addEventListener('click', handleGlobalClick);

    // 국가 옵션 선택 이벤트
    document.querySelectorAll('.country-option').forEach(option => {
        option.removeEventListener('click', handleCountryOptionClick);
        option.addEventListener('click', handleCountryOptionClick);
    });

    // 초기화 완료 표시
    isCountryDropdownsInitialized = true;
    console.log('국가 선택 드롭다운 초기화 완료');
}

// 이벤트 핸들러 함수들
function handleLoginCountrySelect(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('로그인 국가 선택 클릭');
    const loginCountryDropdown = document.getElementById('loginCountryDropdown');
    if (loginCountryDropdown) {
        toggleCountryDropdown(loginCountryDropdown);
    }
}

function handleRegisterCountrySelect(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('회원가입 국가 선택 클릭');
    const registerCountryDropdown = document.getElementById('registerCountryDropdown');
    if (registerCountryDropdown) {
        toggleCountryDropdown(registerCountryDropdown);
    }
}

function handleDropdownClick(e) {
    e.stopPropagation();
}

function handleCountryOptionClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('국가 옵션 클릭:', {
        code: e.target.dataset.code,
        flag: e.target.dataset.flag,
        isLogin: e.target.closest('#loginCountryDropdown') !== null
    });
    
    const code = e.target.dataset.code;
    const flag = e.target.dataset.flag;
    const isLogin = e.target.closest('#loginCountryDropdown') !== null;
    
    if (isLogin) {
        const countryCode = document.getElementById('loginCountryCode');
        const countryFlag = document.getElementById('loginCountryFlag');
        const phone = document.getElementById('loginPhone');
        const dropdown = document.getElementById('loginCountryDropdown');
        
        if (countryCode && countryFlag && phone && dropdown) {
            countryCode.textContent = code;
            countryFlag.textContent = flag;
            dropdown.classList.remove('show');
            updatePhoneInput(code, phone);
        }
    } else {
        const countryCode = document.getElementById('registerCountryCode');
        const countryFlag = document.getElementById('registerCountryFlag');
        const phone = document.getElementById('registerPhone');
        const dropdown = document.getElementById('registerCountryDropdown');
        
        if (countryCode && countryFlag && phone && dropdown) {
            countryCode.textContent = code;
            countryFlag.textContent = flag;
            dropdown.classList.remove('show');
            updatePhoneInput(code, phone);
        }
    }
} 