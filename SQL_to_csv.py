import csv
import re

def sql_to_csv(sql_file, csv_file):
    rows = []
    
    with open(sql_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 提取所有 INSERT 语句中的数据
    pattern = r"INSERT INTO `?\w+`? VALUES\s*(.+?);"
    matches = re.findall(pattern, content, re.DOTALL)
    
    for match in matches:
        # 提取每一行数据
        row_pattern = r"\(([^)]+)\)"
        rows_data = re.findall(row_pattern, match)
        for row in rows_data:
            # 处理字段，去掉引号
            fields = next(csv.reader([row]))
            rows.append(fields)
    
    with open(csv_file, 'w', newline='', encoding='utf-8-sig') as f:
        writer = csv.writer(f)
        # 写入表头
        writer.writerow(['phone', 'pref', 'province', 'city', 'isp', 'post_code', 'area_code', 'city_code', 'isp_type'])
        writer.writerows(rows)
    
    print(f"完成！共导出 {len(rows)} 条数据 → {csv_file}")

# 使用方法：修改成你的文件路径
sql_to_csv('phone_location.sql', 'phone_location.csv')