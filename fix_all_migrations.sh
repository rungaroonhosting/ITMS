#!/bin/bash

echo "🔧 Fixing all migration files..."

# รายการไฟล์ที่ต้องแก้ไข
migrations=(
    "service_requests_table"
    "service_request_logs_table"
    "service_request_types_table"
    "agreements_table"
    "agreement_renewals_table"
    "attachments_table"
)

for migration in "${migrations[@]}"; do
    file=$(find database/migrations -name "*${migration}.php" | head -1)
    if [ -f "$file" ]; then
        echo "Fixing $file"
        
        # แปลง foreignId เป็น unsignedBigInteger
        sed -i 's/\$table->foreignId(\([^)]*\))->constrained.*$/\$table->unsignedBigInteger($1);/g' "$file"
        
        # ลบบรรทัดที่มี foreign
        sed -i '/\$table->foreign(/d' "$file"
        
        # ลบบรรทัดที่มี ->constrained
        sed -i '/->constrained/d' "$file"
        
        # ลบบรรทัดที่มี onDelete, onUpdate
        sed -i '/->onDelete(/d' "$file"
        sed -i '/->onUpdate(/d' "$file"
        
        echo "✅ Fixed $file"
    else
        echo "❌ File not found: $migration"
    fi
done

echo "🎉 All migration files fixed!"
