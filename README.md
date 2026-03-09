# 書籍借閱管理系統

> Laravel 12 / PHP 8.4 / Composer / Eloquent ORM / MySQL 8 / Redis 7 / Blade

以「書籍借閱管理」為案例的 Laravel 練習專案，涵蓋常見分層架構：
書籍 CRUD（MySQL 持久化 + Redis 快取）、MVC 頁面與 REST API 並存、
Laravel Migration 資料庫版本管理、Service 層分離與 Cache 機制。

## 前置條件

本專案搭配容器化開發環境使用（例如 GitHub Codespaces 或 Dev Container），
容器啟動後 MySQL 與 Redis 服務已就緒，不需另行安裝。

若要在本機直接執行，請自行準備以下服務：

- PHP 8.4+
- Composer
- MySQL 8.0（DB: `app`, User: `app`, Password: `app`）
  - Connection: `mysql://app:app@localhost:3306/app`
- Redis 7
  - Host: `localhost`, Port: `6379`

## 快速開始

### 使用 Dev Container（推薦）

1. 安裝 VS Code 擴充套件 **Dev Containers**（`ms-vscode-remote.remote-containers`）
2. Clone 專案並用 VS Code 開啟
3. 左下角點選 `><` 圖示 → 選擇 **Reopen in Container**
4. 等待容器建置完成（首次需較長時間），MySQL 與 Redis 會自動啟動並通過 healthcheck
5. 在容器內 Terminal 執行：

```bash
# 安裝依賴
composer install

# 執行 Migration（自動建表與匯入範例資料）
php artisan migrate --seed

# 啟動開發伺服器
php artisan serve
```

> GitHub Codespaces 使用者：直接在 Codespaces 開啟專案即可，會自動套用 Dev Container 設定。

### 本機直接執行

請先確認[前置條件](#前置條件)中的服務已就緒，再執行：

```bash
git clone <repo-url> /workspaces/learn-php-laravel
cd /workspaces/learn-php-laravel

# 複製環境設定
cp .env.example .env

# 安裝依賴
composer install

# 產生 Application Key
php artisan key:generate

# 執行 Migration（自動建表與匯入範例資料）
php artisan migrate --seed

# 啟動開發伺服器
php artisan serve
```

啟動成功後驗證：

```bash
curl http://localhost:8000/hello          # Hello, Laravel!
curl http://localhost:8000/api/books      # JSON 陣列，3 筆範例書籍
```

瀏覽器開啟 http://localhost:8000/books 可查看 Blade 書籍清單頁。

## 可用網址

| 網址 | 說明 |
|------|------|
| http://localhost:8000/books | 書籍清單頁（Blade） |
| http://localhost:8000/books/{id} | 書籍詳情頁（Blade） |
| http://localhost:8000/api/documentation | Swagger UI — 互動式 API 文件與測試 |

> Swagger UI 僅顯示 `/api/books` 相關端點，MVC 頁面不會出現。

## API 端點

### REST API

| Method | Path | 說明 |
|--------|------|------|
| GET | `/api/books` | 取得所有書籍 |
| GET | `/api/books/{id}` | 取得單本書籍 |
| POST | `/api/books` | 新增書籍 |
| PUT | `/api/books/{id}` | 更新書籍 |
| DELETE | `/api/books/{id}` | 刪除書籍 |

### MVC 頁面

| Path | 說明 |
|------|------|
| `/books` | 書籍清單頁 |
| `/books/create` | 新增書籍表單 |
| `/books/{id}` | 書籍詳情頁 |
| `/books/{id}/edit` | 編輯書籍表單 |

## 專案結構

```
.devcontainer/
└── devcontainer.json              # Dev Container 設定（Codespaces / VS Code 遠端開發）
docker/
└── php/
    └── Dockerfile                 # PHP 8.4 開發容器映像
docker-compose.yml                 # 三服務編排：app(bind mount) / db(MySQL 8) / redis(Redis 7)
.env.example                       # 環境變數範本

app/
├── Http/
│   ├── Controllers/
│   │   ├── BookController.php           # @Controller — Blade 頁面
│   │   └── Api/BookApiController.php    # REST API — JSON
│   └── Requests/
│       └── BookRequest.php              # Form Request 驗證
├── Models/
│   └── Book.php                         # Eloquent Model
├── Services/
│   └── BookService.php                  # 業務邏輯層（Cache-Aside）
└── Resources/
    └── BookResource.php                 # API Resource（輸出轉換）

config/
├── app.php                              # 應用設定
├── database.php                         # 資料庫連線設定
└── cache.php                            # 快取設定

database/
├── migrations/
│   ├── xxxx_create_books_table.php      # 建表 Migration
│   └── ...
└── seeders/
    └── BookSeeder.php                   # 範例資料

resources/views/
└── books/
    ├── index.blade.php                  # 書籍清單頁
    ├── show.blade.php                   # 書籍詳情頁
    ├── create.blade.php                 # 新增表單頁
    └── edit.blade.php                   # 編輯表單頁

routes/
├── web.php                              # MVC 路由
└── api.php                              # API 路由

tests/
├── Unit/
│   └── BookServiceTest.php              # 單元測試
└── Feature/
    └── BookApiTest.php                  # Feature Test（API 測試）
```

## 設定檔與環境

| 設定檔 | DB Host | Redis Host | 適用環境 |
|--------|---------|------------|----------|
| `.env` | localhost | localhost | 本機直接開發 |
| `.env.docker` | db | redis | 容器環境（覆寫連線位址） |

容器環境已透過 `.env.docker` 自動啟用正確的連線設定，不需手動設定。
本機開發時使用 `.env` 即可。

### 容器服務與 Port 對應

| 服務 | 映像 | 容器內 Port | 預設對外 Port | 環境變數 |
|------|------|-------------|--------------|----------|
| app | `docker/php/Dockerfile` | 8000 | 8000 | `APP_PORT` |
| db | `mysql:8.0` | 3306 | 3306 | `MYSQL_PORT` |
| redis | `redis:7-alpine` | 6379 | 6379 | `REDIS_PORT` |

若需調整 Port，複製 `.env.example` 為 `.env` 後修改即可：

```bash
cp .env.example .env
```

## 開發

### Hot Reload 模式

Laravel 預設支援程式碼變更自動生效，只需重新整理瀏覽器即可。

若要搭配前端資源（Vite）：

```bash
# Terminal 1 — 啟動 Laravel
php artisan serve

# Terminal 2 — 啟動 Vite（前端 hot reload）
npm run dev
```

### 測試

```bash
php artisan test                          # 執行全部測試
php artisan test --filter=BookServiceTest # 只跑單一測試類
```

| 測試類 | 類型 | 案例數 |
|--------|------|--------|
| **BookServiceTest** | 單元測試（Mock） | 5 |
| **BookApiTest** | Feature Test（API 測試） | 5 |

### 常用 Artisan 指令

```bash
php artisan migrate              # 執行 Migration
php artisan migrate:rollback     # 回滾上一次 Migration
php artisan db:seed              # 執行 Seeder
php artisan cache:clear          # 清除快取
php artisan config:clear         # 清除設定快取
php artisan route:list           # 列出所有路由
php artisan make:controller      # 建立 Controller
php artisan make:model           # 建立 Model
php artisan make:migration       # 建立 Migration
```

## 教案文件

- [PHP Laravel 新手入門教案（導覽）](docs/新手入門教案/README.md)
- [PHP Laravel 新手入門教案綱要](docs/新手入門教案/新手入門教案綱要.md)
- [U00 — 課前準備](docs/新手入門教案/00-課前準備.md)
- [U01 — 專案啟動與第一支 API](docs/新手入門教案/01-專案啟動與第一支API.md)
- [U02 — 假畫面與 Blade 初體驗](docs/新手入門教案/02-假畫面與Blade初體驗.md)
- [U03 — API 回傳假資料](docs/新手入門教案/03-API回傳假資料.md)
- [U04 — 設定檔與多環境切換](docs/新手入門教案/04-設定檔與多環境切換.md)
- [U05 — Migration 與資料庫設計](docs/新手入門教案/05-Migration與資料庫設計.md)
- [U06 — Eloquent 串接資料庫](docs/新手入門教案/06-Eloquent串接資料庫.md)
- [U07 — Service 與 Resource 分層](docs/新手入門教案/07-Service與Resource分層.md)
- [U08 — REST API 完善與錯誤處理](docs/新手入門教案/08-REST-API完善與錯誤處理.md)
- [U09 — Redis 快取整合](docs/新手入門教案/09-Redis快取整合.md)
- [U10 — 測試策略與實戰](docs/新手入門教案/10-測試策略與實戰.md)
