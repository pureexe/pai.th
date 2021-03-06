# ไป.ไทย
ระบบสำหรับย่อลิงก์เป็นภาษาไทย เช่น [https://ไป.ไทย/กข๑งด](https://ไป.ไทย/กข๑งด)

![](/assets/images/preview.png)

## ความสามารถ
- ลิงก์ย่อเป็นภาษาไทย
- ผู้ใช้จะใช้งานได้เมื่อรับเชิญเท่านั้น
- จำกัดให้ผู้ใช้สามารถย่อลิงก์เฉพาะโดเมนที่ต้องการเท่านั้น

## เซิฟเวอร์ที่ต้องการ
- รองรับ HTTP verb GET/POST/DELETE/PUT
- PHP 7.1 + MariaDB 10.1 (ยังรองรับ PHP 5.5 + Mysql 5.5)

## การติดตั้ง
1. สร้างฐานข้อมูลจากไฟล์ paith.sql
2. ตั้งค่าฐานข้อมูล `application/config/database.php`
3. เข้าระบบที่ `example.com/จัดการ`
4. เข้าสู่ระบบด้วย ชื่อผู้ใช้ `ผู้ดูแล` รหัสผ่าน `superadmin`
5. เลือก `ผู้ดูแล` ที่มุมขวาบน จากนั้นเริ่มทำการสร้างบัตรเชิญเพื่อเชิญผู้ใช้ที่ต้องการให้ใช้งานเว็บนี้สามารถสร้างบัญชีได้ 

## ระบบสำรอง
ไป.ไทย มีระบบสำรองสำหรับกรณีโฮสต์ดับหรือถูกโจมตีเพื่อให้ใช้งานได้ลื่นไหล โดยขณะใช้งานระบบสำรอง จะไม่สามารถสร้างลิงก์เพิ่มได้ แต่ผู้ใช้จะยังคงถูกเปลี่ยนเส้นทางไปตามปกติ โดยให้ทำการ fork [กลับ.ไทย](https://github.com/pureexe/glab.th) จากนั้นทำการเปิดใช้งาน Github Pages เมื่อเว็บ ไป.ไทย ล่มให้ redirect ไปยังโดเมนของ repo กลับ.ไทย แล้วผู้ใช้จะสามารถเปลี่ยนเส้นทางได้อย่างต่อเนื่อง

### Jekyll
Jekyll เป็นหนึ่งในระบบสำรองที่ ไป.ไทย เลือกใช้งานเนื่องจาก Github Pages สามารถรองรับการโจมตีได้เป็นอย่างดีโดยขั้นตอนมีดังนี้

1. `php index.php exporter jekyll` เพื่อสร้างแบ็คอัปสำหรับ jekyll
2. แล้วนำไฟล์ในโฟลเดอร์ `_output/jekyll/_path` ไปวางแทนที่ `_path` ก่อน commit โค้ดของ glab.th ขึ้นไปใหม่

ทั้งนี้ขอแนะนำให้สำรองข้อมูลด้วยวิธีนี้โดยการตั้ง cronjob วันละ 1 ครั้ง


### Firebase
Firebase นั้นสามารถใช้งานได้ฟรี แต่รองรับ user ได้ไม่มากนัก โดยการใช้ระบบสำรองบน firebase เมื่อมีการสร้างลิงค์ระบบของ ไป.ไทย จะทำการเพิ่มข้อมูลเข้า firebase เองเพื่อใช้ยามฉุกเฉิน

#### การตั้งค่าสำรองข้อมูลกับ Firebase

1. นำ firebase admin key มาใส่ที่ `/application/config/firebase-adminsdk.json`
2. แก้ไข `/application/config/paith.php` ให้ตรงกับโปรเจคของ firebase

#### การบังคับสำรองข้อมูลบน Firebase

1. ใช้คำสั่ง `php index.php exporter firebase` เพื่อสร้างแบ็คอัปสำหรับ firebase
2. เข้า Firebase Real time database console จะเห็นด้านมุมบนขวามีปุ่มเครื่องหมาย + เขียนว่านำเข้าไฟล์ json ให้เลือกอัปโหลดไฟล์ซึ่งอยู่ที่ `_output/firebase/paith_output.json`

## สถิติจากการใช้งานจริง

![](/assets/images/stats.png)
