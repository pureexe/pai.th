# sub.th
ซับ.ไทย เวอร์ชั่นเขี่ยนใหม่ด้วย codeigiter

## เซิฟเวอร์
- รองรับ HTTP verb GET/POST/DELETE/PUT
- PHP 7.1 + MariaDB 10.1 (ยังรองรับ Compatible PHP 5.5 + Mysql 5.5)

## ขั้นตอนการสำรองข้อมูล
1. โหลดข้อมูล SQL ออกมาจากโฮสต์
2. ติดตั้ง SQL เข้ายัง localhost
3. clone Repo นี้เวอร์ชั่นล่าสุดลงไปยังเครื่อง
4. ใช้คำสั่ง `php index.php exporter firebase` เพื่อสร้างแบ็คอัปสำหรับ firebase
5. ใช้คำสั่ง `php index.php exporter jekyll` เพื่อสร้างแบ็คอัปสำหรับ jekyll
6. เข้า Firebase Real time database console จะเห็นด้านมุมบนขวามีปุ่มเครื่องหมาย + เขียนว่านำเข้าไฟล์ json ให้เลือกอัปโหลดไฟล์ซึ่งอยู่ที่ `_output/firebase/subth_output.json`
7. clone https://github.com/pureexe/sub.th-juti แล้วนำไฟล์ในโฟลเดอร์ `_output/jekyl/_path` ไปวางแทนที่ `_path` ก่อน commit ขึ้นไปใหม่
