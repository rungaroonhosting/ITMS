#!/bin/bash

# ฟังก์ชันแก้ไข migration files ให้เอา foreign keys ออก
fix_migration() {
    local file=$1
    # เอา foreignId และ constrained ออก แล้วเปลี่ยนเป็น unsignedBigInteger
    sed -i 's/->foreignId(\([^)]*\))->constrained.*$/->unsignedBigInteger($1);/g' "$file"
    sed -i 's/->foreign(\([^)]*\)).*$/\/\/ Foreign key removed - will be added later/g' "$file"
}

# แก้ไข migration files ทั้งหมด
for file in database/migrations/*create_*.php; do
    if [[ "$file" != *"users"* && "$file" != *"cache"* && "$file" != *"jobs"* && "$file" != *"personal_access_tokens"* ]]; then
        echo "Fixing $file"
        fix_migration "$file"
    fi
done
