import re


def mysql_insert_to_mssql(
    input_file,
    output_file,
    go_interval=10000
):
    """
    将 MySQL SQL 文件转换为 MSSQL 导入脚本：
    - 仅保留 INSERT INTO 语句
    - `table` -> [table]
    - 每 go_interval 条插入一次 GO
    """

    insert_count = 0

    insert_pattern = re.compile(
        r"^\s*INSERT\s+INTO\s+`([^`]+)`",
        re.IGNORECASE
    )

    with open(input_file, "r", encoding="utf-8") as fin, \
            open(output_file, "w", encoding="utf-8-sig") as fout:

        for line in fin:
            line_strip = line.strip()

            # 只处理 INSERT
            if not line_strip.upper().startswith("INSERT INTO"):
                continue

            # 表名转换：`xxx` -> [xxx]
            line = insert_pattern.sub(
                lambda m: f"INSERT INTO [{m.group(1)}]",
                line
            )

            fout.write(line)

            insert_count += 1

            # 每隔一定数量插入 GO
            if go_interval > 0 and insert_count % go_interval == 0:
                fout.write("GO\n")

    print(f"转换完成，共输出 {insert_count} 条 INSERT。")


if __name__ == "__main__":
    mysql_insert_to_mssql(
        input_file="mysql\\phone_location.sql",
        output_file="mssql\\phone_location-data.sql",
        go_interval=10000   # 每10000条插入一个GO；如不需要可设为0
    )