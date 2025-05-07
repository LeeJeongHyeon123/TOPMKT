-- 데이터베이스 사용자 생성
CREATE USER 'topmkt_user'@'localhost' IDENTIFIED BY 'your_secure_password';

-- TOPMKT 데이터베이스에 대한 권한 부여
GRANT SELECT, INSERT, UPDATE, DELETE ON TOPMKT.* TO 'topmkt_user'@'localhost';

-- 권한 변경사항 적용
FLUSH PRIVILEGES; 