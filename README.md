# PC Zone (Frontend + Backend + Database)

PC Zone คือโปรเจกต์ระบบร้าน/แคตตาล็อกสินค้า + ตะกร้า/ออเดอร์ + ระบบชำระเงินอย่างง่าย
ที่ประกอบด้วย 3 ส่วนหลัก:
- **Frontend (React Native / Expo)** — แอปบนมือถือสำหรับผู้ใช้
- **Backend (PHP)** — REST-like API และหน้า Admin
- **Database (MySQL/MariaDB)** — โครงสร้างข้อมูล + seed (`pc_zone.sql`)

> Repo นี้เตรียมโครงสร้าง, `.gitignore`, และ README สำหรับขึ้น GitHub ให้พร้อมแล้ว

---

## Table of Contents
- [ภาพรวมสถาปัตยกรรม](#ภาพรวมสถาปัตยกรรม)
- [ฟีเจอร์หลัก](#ฟีเจอร์หลัก)
- [เทคโนโลยีที่ใช้](#เทคโนโลยีที่ใช้)
- [โครงสร้างไดเรกทอรี](#โครงสร้างไดเรกทอรี)
- [เริ่มต้นอย่างรวดเร็ว (Quickstart)](#เริ่มต้นอย่างรวดเร็ว-quickstart)
- [การตั้งค่า Environment Variables](#การตั้งค่า-environment-variables)
- [การรัน Frontend (Expo)](#การรัน-frontend-expo)
- [การรัน Backend (PHP)](#การรัน-backend-php)
- [Database & การนำเข้า `pc_zone.sql`](#database--การนำเข้า-pc_zonesql)
- [สรุปเส้นทาง API](#สรุปเส้นทาง-api)
- [ความปลอดภัย & แนวปฏิบัติเมื่อขึ้น GitHub](#ความปลอดภัย--แนวปฏิบัติเมื่อขึ้น-github)
- [การดีพลอย (ตัวอย่างแนวทาง)](#การดีพลอย-ตัวอย่างแนวทาง)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## ภาพรวมสถาปัตยกรรม

```
[ React Native App (Expo) ]
           |
           |  HTTPS (REST-like JSON)
           v
[  PHP Backend (API + Admin)  ] --- [Uploads]
           |
           |  PDO/MySQL
           v
[      MySQL / MariaDB       ]
```

- ฝั่งแอปเรียก API ผ่าน base URL เช่น `https://your-domain.com/pc_zone/api`
- Backend ใช้ PHP (PDO) เชื่อมต่อฐานข้อมูล
- มีหน้า Admin (เช่น dashboard, จัดการสินค้า/คำสั่งซื้อ) อยู่ที่ `backend/`

---

## ฟีเจอร์หลัก
- ลงทะเบียน/เข้าสู่ระบบผู้ใช้ (Auth)
- ดูรายการสินค้า/รายละเอียดสินค้า
- บุ๊คมาร์ค/รายการโปรด (Bookmark)
- ตะกร้าสินค้า (Cart)
- ที่อยู่จัดส่ง (Address)
- สร้างคำสั่งซื้อ + ตรวจสถานะคำสั่งซื้อ
- อัปโหลดหลักฐาน/สถานะการชำระเงิน (Payment)
- ฝั่ง Admin: จัดการผู้ใช้/สินค้า/ออเดอร์ และดูสรุปยอดรวมบางส่วน

---

## เทคโนโลยีที่ใช้
**Frontend**
- React Native (Expo)
- React Navigation, Async Storage
- Axios, date-fns, vector icons

**Backend**
- PHP (PDO)
- REST-like endpoints (ไฟล์ `.php` ใน `backend/api/*`)
- จัดเก็บรูป/ไฟล์ใน `uploads/`

**Database**
- MySQL / MariaDB
- โครงสร้างและ seed จาก `pc_zone.sql`
- การตั้งค่า `utf8mb4` แนะนำสำหรับภาษาไทย

---

## โครงสร้างไดเรกทอรี

```
pc_zone_monorepo/
├─ frontend/                 # ซอร์สแอป (RN/Expo) — ต้องย้ายเข้าโปรเจกต์ Expo ที่สร้างใหม่
│  ├─ App.js
│  └─ src/
│     ├─ screens/            # Home, Detail, Login, Cart, Checkout, Payment, etc.
│     └─ navigation/         # BottomTabNavigation
│
├─ backend/                  # PHP API + Admin
│  ├─ index.php              # หน้า login/admin entry
│  ├─ api/
│  │  ├─ database/db.php     # เชื่อมต่อ DB (ควรอ่านค่าจาก .env)
│  │  ├─ auth/               # login, register, logout
│  │  ├─ products/           # get_products, get_product
│  │  ├─ cart/               # add_to_cart, get_cart, update_cart, delete_cart_item
│  │  ├─ bookmark/           # add/check/list/remove
│  │  ├─ address/            # add/get/update/delete/set_default
│  │  ├─ orders/             # create_order, get_orders, get_order_details, update_order_status
│  │  └─ payment/            # create/process/get_total/get_payment_status
│  └─ uploads/               # รูปภาพ/หลักฐาน (อย่า commit)
│
├─ pc_zone.sql               # โครงสร้างฐานข้อมูล + seed
├─ push_to_github.bat        # สคริปต์ช่วย push (Windows)
├─ push_to_github.sh         # สคริปต์ช่วย push (macOS/Linux)
└─ README.md                 # ไฟล์นี้
```

> ⚠️ โปรเจกต์ฝั่งแอปใน zip ที่ได้รับ **ไม่มี** `package.json` → ต้องสร้างโปรเจกต์ Expo ใหม่ก่อน แล้วค่อยคัดลอก `App.js` และ `src/` ไปวาง

---

## เริ่มต้นอย่างรวดเร็ว (Quickstart)

1) **เตรียมฐานข้อมูล**
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS pc_zone CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p pc_zone < pc_zone.sql
```

2) **Backend (PHP)**
- ตั้งค่า `.env` ใน `backend/` (ตัวอย่างด้านล่าง)
- รันด้วย PHP built-in (สำหรับทดสอบ):
  ```bash
  cd backend
  php -S 127.0.0.1:8000
  ```

3) **Frontend (Expo)**
- สร้างโปรเจกต์ Expo ใหม่ แล้วติดตั้ง lib ตามด้านล่าง
- ตั้งค่า `EXPO_PUBLIC_API_URL` ให้ชี้ไป backend
- รัน `npx expo start`

---

## การตั้งค่า Environment Variables

### Backend (`backend/.env`)
> **อย่า commit** ไฟล์นี้ (ถูกกันด้วย `.gitignore` แล้ว)

```env
DB_HOST=localhost
DB_NAME=pc_zone
DB_USER=pc
DB_PASS=password
DB_CHARSET=utf8mb4

# ถ้ามีค่าตั้งค่าอื่น เช่น JWT_SECRET, APP_ENV, BASE_URL ก็สามารถเพิ่มได้
```

แก้ไฟล์ `backend/api/database/db.php` ให้โหลดค่าจาก ENV แทนค่าฝัง (หากยังไม่ได้ปรับ):
```php
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'pc_zone';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';
```

### Frontend (`.env` ในโปรเจกต์ Expo)
```env
EXPO_PUBLIC_API_URL=http://127.0.0.1/pc_zone/api
```
แล้วอ้างอิงในโค้ด:
```js
const API = process.env.EXPO_PUBLIC_API_URL;
const res = await axios.post(`${API}/auth/login.php`, payload);
```

---

## การรัน Frontend (Expo)

1) สร้างโปรเจกต์ Expo ใหม่
```bash
npm create expo@latest pc_zone_app
cd pc_zone_app
```

2) ติดตั้ง dependencies ที่ใช้
```bash
npm install axios @react-native-async-storage/async-storage @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs @expo/vector-icons expo-image-picker expo-file-system expo-linear-gradient react-native-svg date-fns
npx expo install react-native-screens react-native-safe-area-context
```

3) คัดลอกซอร์สจาก repo นี้
- คัดลอก `frontend/App.js` และโฟลเดอร์ `frontend/src/` ไปยังโปรเจกต์ Expo (`pc_zone_app/`)

4) สร้าง `.env`
```bash
echo "EXPO_PUBLIC_API_URL=http://127.0.0.1/pc_zone/api" > .env
```

5) รัน
```bash
npx expo start
```

---

## การรัน Backend (PHP)

### ตัวเลือก A: PHP Built-in server (เพื่อทดสอบ)
```bash
cd backend
php -S 127.0.0.1:8000
```

### ตัวเลือก B: XAMPP / WAMP
- วาง `backend/` ไว้ใน `htdocs` (Apache)
- เข้าผ่าน `http://127.0.0.1/pc_zone/backend` (หรือปรับ VirtualHost ให้สวยงาม)

### ตัวเลือก C: Nginx + PHP-FPM / Apache2
- ชี้ DocumentRoot ไปที่โฟลเดอร์ `backend/`
- ตรวจสิทธิ์โฟลเดอร์ `uploads/` ให้เขียนไฟล์ได้
- ตั้ง `utf8mb4` ใน MySQL/MariaDB

---

## Database & การนำเข้า `pc_zone.sql`
- สร้างฐานข้อมูล `pc_zone` (ตามตัวอย่าง Quickstart)
- นำเข้า `pc_zone.sql`
- ตรวจ encoding `utf8mb4` เพื่อรองรับภาษาไทย/อีโมจิ

> หากเปลี่ยนชื่อฐานข้อมูล/ผู้ใช้/รหัสผ่าน ให้ตามไปแก้ `.env` ของ Backend

---

## สรุปเส้นทาง API

> Base URL: `{BACKEND_BASE}/api` เช่น `http://127.0.0.1/pc_zone/api`

- `auth/`
  - `POST auth/login.php`
  - `POST auth/register.php`
  - `POST auth/logout.php`
- `products/`
  - `GET  products/get_products.php`
  - `GET  products/get_product.php?id=...`
- `cart/`
  - `POST cart/add_to_cart.php`
  - `GET  cart/get_cart.php?user_id=...`
  - `POST cart/update_cart.php`
  - `POST cart/delete_cart_item.php`
- `bookmark/`
  - `POST bookmark/add_to_bookmark.php`
  - `GET  bookmark/get_bookmarks.php?user_id=...`
  - `GET  bookmark/check_bookmark.php?user_id=...&product_id=...`
  - `POST bookmark/remove_bookmark.php`
- `address/`
  - `POST address/add_address.php`
  - `GET  address/get_address.php?user_id=...`
  - `POST address/update_address.php`
  - `POST address/delete_address.php`
  - `POST address/set_default_address.php`
- `orders/`
  - `POST orders/create_order.php`
  - `GET  orders/get_orders.php?user_id=...`
  - `GET  orders/get_order_details.php?order_id=...`
  - `GET  orders/get_order_address.php?order_id=...`
  - `POST orders/update_order_status.php`
- `payment/`
  - `POST payment/create_payment.php`
  - `POST payment/process_payment.php`
  - `GET  payment/get_total.php?order_id=...`
  - `GET  payment/get_payment_status.php?order_id=...`

> **หมายเหตุ:** ควรตรวจสอบพารามิเตอร์ที่ต้องใช้/รูปแบบ JSON ของแต่ละ endpoint ในซอร์ส `backend/api/*` อีกครั้งเพื่อความชัดเจน

---