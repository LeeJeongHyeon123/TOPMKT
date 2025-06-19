#!/bin/bash

# 카테고리 아이콘 이미지 복사 스크립트
echo "카테고리 아이콘 이미지 복사 시작..."

# 기본 경로 설정
SOURCE_DIR="/var/www/html/topmkt/public/assets/uploads/temp_figma_images"
TARGET_DIR="/var/www/html/topmkt/public/assets/uploads/temp_figma_images"

# 1. 간식 (이미 완료)
echo "1. 간식 아이콘 - 이미 완료"

# 2. 한식
echo "2. 한식 아이콘 복사 중..."
cp "$SOURCE_DIR/2_1_4x_1_1_10578.png" "$TARGET_DIR/category-korean.png"

# 3. 치킨
echo "3. 치킨 아이콘 복사 중..."
cp "$SOURCE_DIR/3_1_4x_1_1_10580.png" "$TARGET_DIR/category-chicken.png"

# 4. 피자/양식
echo "4. 피자/양식 아이콘 복사 중..."
cp "$SOURCE_DIR/4_1_4x_1_1_10582.png" "$TARGET_DIR/category-pizza.png"

echo "카테고리 아이콘 복사 완료!"
echo "복사된 파일들:"
ls -la "$TARGET_DIR/category-*.png"