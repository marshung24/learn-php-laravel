# PHP Laravel 新手入門教案

> 以本 Repo「書籍管理系統」為實作主軸，從零帶出 Laravel 全棧開發核心技能。

---

## §1 教案資訊

| 項目 | 說明 |
|------|------|
| 對象 | 有基礎程式概念（變數、迴圈、函式）與基本命令列操作經驗，但**尚未做過 PHP Web 專案**的初學者 |
| 目標 | 獨立完成一個「能跑、能測、能部署」的 CRUD Web 應用（書籍管理系統） |
| 技術棧 | PHP 8.4、Laravel 12、Composer、Eloquent ORM、Migration、MySQL 8、Redis 7、Blade、L5-Swagger、Docker Compose |
| 時數 | 課前準備（自學）+ 10 單元（各 60–120 分鐘） |
| 教學方式 | 觀念講解 → Repo 實作示範 → 學員動手做 → Code Review → 驗收 |
| 先備知識 | 基礎 SQL（SELECT / INSERT）、HTTP 概念（GET / POST）、Git 基本操作（clone / commit） |
| 不含範圍 | 前端框架（React / Vue）、Laravel Passport/Sanctum 深度、雲端進階架構（K8s / Terraform）、微服務拆分 |

---

## §2 學習成果（結訓時能做到）

1. **能說明** Laravel 分層架構（Route → Controller → Service → Repository → Model）各層的職責與呼叫方向
2. **能使用** Composer 建置專案、用 Docker Compose 一鍵啟動整套開發環境（App + MySQL + Redis）
3. **能撰寫** Laravel Migration 腳本管理資料庫 schema 演進，並解釋版本控管機制
4. **能實作** Eloquent Model 與自訂查詢方法，理解 Active Record 模式
5. **能設計** RESTful API（正確使用 HTTP 動詞與狀態碼），用 Form Request + Exception Handler 統一驗證與錯誤回應，並產出 Swagger 互動式文件
6. **能製作** Blade 伺服器端渲染頁面（列表、表單、詳情），理解 PRG 模式
7. **能實作** Redis Cache-Aside 快取模式，並用 redis-cli 驗證快取行為
8. **能撰寫** PHPUnit/Pest 單元測試與 Feature Test，達到基本覆蓋率門檻

---

## §3 閱讀指引

### 練習標記

| 標記 | 意義 |
|------|------|
| **必做** | 課堂內所有學員都應完成的核心練習 |
| **延伸挑戰** | 給進度快的學員或課後作業 |

### 依賴標記

- `⟵ 需完成 UXX` = 需先完成指定單元的對應練習
- `⟵ 需完成 UXX 延伸` = 特指依賴某單元的「延伸挑戰」成果

### 命名規範

| 項目 | 格式 | 範例 |
|------|------|------|
| 單元編號 | U00–U10 | U03 |
| 檔案路徑 | 省略 `app/` 前綴 | `Http/Controllers/BookController.php` |
| 資源路徑 | 省略 `resources/` | `views/books/index.blade.php` |
| 測試路徑 | 省略 `tests/` | `Unit/BookServiceTest.php` |

### 單元內部結構

每個單元依序包含六個子區塊：① 為什麼先教這個？ → ② 對應檔案 → ③ 核心觀念 → ④ 動手做 → ⑤ 踩坑提示 → ⑥ 驗收標準（DoD）

---

## 課程目錄

| 單元 | 名稱 | 時數 | 一句話目標 | 前置依賴 |
|------|------|------|-----------|----------|
| [U00](00-課前準備.md) | 課前準備 | 自學 | 安裝開發環境並驗證可用 | — |
| [U01](01-專案啟動與第一支API.md) | 專案啟動與第一支 API | 90 min | 能啟動專案並用瀏覽器呼叫自訂端點 | — |
| [U02](02-假畫面與Blade初體驗.md) | 假畫面 — Blade 初體驗 | 60 min | 能用 Blade 做出書籍清單頁面（假資料） | U01 |
| [U03](03-API回傳假資料.md) | API 回傳假資料 | 60 min | 能用 Controller 回傳固定 JSON 並在 Swagger 測試 | U01 |
| [U04](04-設定檔與多環境切換.md) | 設定檔與多環境切換 | 60 min | 能理解 .env 與 config 機制並用 Docker Compose 啟動完整環境 | U01 |
| [U05](05-Migration與資料庫設計.md) | Migration 與資料庫設計 | 90 min | 能撰寫 migration 自動建表與 seed data | U04 |
| [U06](06-Eloquent串接資料庫.md) | Eloquent 串接資料庫 | 120 min | 能用 Eloquent 完成真實 CRUD，頁面與 API 顯示 DB 真實資料 | U02, U03, U05 |
| [U07](07-Service與Resource分層.md) | Service 與 Resource 分層 | 90 min | 能從 Controller 抽出 Service 層，引入 Resource 做輸出轉換 | U06 |
| [U08](08-REST-API完善與錯誤處理.md) | REST API 完善與錯誤處理 | 90 min | 能用 Form Request + Exception Handler 統一驗證與錯誤回應，正確使用 HTTP 狀態碼 | U07 |
| [U09](09-Redis快取整合.md) | Redis 快取整合 | 90 min | 能用 redis-cli 驗證快取行為並說明 Cache-Aside 流程 | U07 |
| [U10](10-測試策略與實戰.md) | 測試策略與實戰 | 120 min | 能為新方法撰寫單元測試與 Feature Test | U08 |

> **設計說明**：單元順序遵循「對應專案流程的漸進式推演」——環境確認（U01）→ 假畫面 Level-1 UI（U02）→ 假資料 Level-2 程式（U03）→ 設定環境（U04）→ 資料庫設計 Level-3 資料（U05）→ 串接真實 DB（U06）→ 架構正規化（U07）→ 完善 API（U08）→ 效能優化（U09）→ 測試驗證（U10）。

---

## §6 里程碑檢核

| 里程碑 | 涵蓋單元 | 達成標準 | 未達標補救 |
|--------|----------|----------|-----------|
| **M1：能跑能看** | U01–U03 | 學員可啟動專案、做出假資料頁面與 API、在瀏覽器看到書籍清單 | 補做 U01–U03 的必做練習；講師一對一確認環境問題 |
| **M2：真實 CRUD 循環** | U04–U06 | 學員可串接真實 DB，頁面與 API 顯示 DB 資料，完成新增/查詢/更新/刪除 | 回頭確認 DB 連線設定，重做 U06 的 Eloquent 串接 |
| **M3：正規架構與完整功能** | U07–U10 | 學員有 Service/Resource 分層、統一錯誤處理、Redis 快取、自動化測試 | 優先完成 U07 分層重構；U09、U10 可簡化為閱讀理解 + 跑既有測試 |

---

## §7 每單元教學流程

### 標準流程（以 90 min 為基準）

| 時間 | 活動 | 負責 | 說明 |
|------|------|------|------|
| 10 min | 觀念講解 | 講師 | 核心概念 + 為什麼這樣設計（對應 ①③） |
| 20 min | 講師示範 | 講師 | 基於 Repo 程式碼 live demo |
| 40 min | 學員實作 | 學員為主、助教巡場 | 「必做」練習，助教協助個別卡關 |
| 15 min | Code Review | 講師主持 | 抽 2–3 人分享，討論不同寫法 |
| 5 min | 總結 | 講師 | 重點回顧 + 指派延伸挑戰作為課後作業 |

### 不同時數調整建議

| 時數 | 調整方式 |
|------|----------|
| 60 min（U02、U03、U04） | 壓縮觀念講解至 5 min、講師示範至 10 min、Code Review 至 5 min；學員實作維持 35 min |
| 120 min（U06、U10） | 多出的 30 min 分配至：講師示範 +10 min、學員實作 +20 min |

### 講師與助教分工

- **講師**：觀念講解、live demo、收斂共通問題、主持 Code Review
- **助教**：巡場協助個別卡關的學員、確認所有人環境正常、記錄高頻問題回饋給講師

### 進度異常處理

班級進度落後時，優先縮減（依順序）：總結 → 講師示範 → 觀念講解。**不可壓縮**：學員實作時間是底線。

---

## §8 班級分流建議

| 維度 | 基礎班 | 進階班 | 混合班 |
|------|--------|--------|--------|
| 練習範圍 | 只做必做 | 必做 + 全部延伸挑戰 | 課堂做必做，延伸作為課後作業 |
| 每單元時數 | 偏向 90–120 min | 可壓到 60–90 min | 90 min 為基準 |
| 自學比例 | 低（講師帶做） | 高（自行閱讀 + 實作） | 中等 |
| 可壓縮的單元 | 無（全部必修） | U02、U03 可合併為一堂快速帶過 | 視學員回饋彈性調整 |
| 課後追蹤 | 無延伸作業 | 延伸挑戰在下堂課開場 review | 延伸作業繳交 + 下堂課抽 review |

---

## §9 評量機制

### 課程練習評量

| 項目 | 比重 | 具體說明 |
|------|------|----------|
| 功能完成度 | 40% | 必做練習 100% 完成；能正確執行 CRUD、快取、驗證等核心功能 |
| 程式碼品質 | 20% | 命名規範、分層職責明確、無重複程式碼 |
| 測試覆蓋 | 20% | 具備正確的 Mock 運用與測試斷言；行覆蓋率 ≥ 60% |
| 問題分析 | 20% | 能判讀錯誤 log 並說明修復思路 |

### 結訓驗收專案

> 驗收專案採用與教案**不同的業務主題**（如「員工通訊錄管理系統」），驗證學員是否真正理解開發流程。

**驗收方式**：限時實作（建議 3–4 小時）

| 驗收項目 | 達成條件 |
|----------|----------|
| Migration | 至少一支建表 Migration + 一支 Seeder |
| Model + Resource | Model 對應 DB 表、Form Request 含驗證、Resource 做輸出轉換 |
| Eloquent | 完成基本 CRUD 的資料存取 |
| Service 層 | 業務邏輯集中在 Service |
| REST API | 至少 4 支 API（GET list / GET by ID / POST / DELETE），狀態碼正確 |
| Blade 頁面 | 至少一個列表頁 + 一個新增表單頁 |
| 測試 | 至少 1 支 Service 單元測試 + 1 支 Feature Test |
| 全部可運行 | `php artisan serve` 能啟動、頁面能操作、API 能呼叫、`php artisan test` 全綠 |

### 標準化評語範例

| 等級 | 評語範例 |
|------|----------|
| 優秀 | 功能完整且程式碼結構清晰，測試覆蓋充分，能獨立排查問題並提出合理的設計理由 |
| 達標 | 核心功能完成，分層架構正確，有基本測試，能在提示下排查常見問題 |
| 待加強 | 部分功能缺失或分層不明確，測試不足，排查問題時需要較多協助 |
| 未達標 | 核心 CRUD 流程無法跑通，建議補做指定練習後重新驗收 |

---

## §10 參考文件

| 文件 | 用途 |
|------|------|
| `README.md` | 環境啟動與操作手冊 |
| `docs/requirement/design/` | 各主題細部設計規格 |
| `docs/team/團隊註解撰寫準則.md` | 程式碼註解風格規範 |
| `/api/documentation` | 互動式 API 測試介面（Swagger UI） |

### 外部參考資源

| 主題 | 資源 |
|------|------|
| Laravel 官方文件 | [laravel.com/docs](https://laravel.com/docs) |
| Eloquent ORM | [laravel.com/docs/eloquent](https://laravel.com/docs/eloquent) |
| Blade 模板 | [laravel.com/docs/blade](https://laravel.com/docs/blade) |
| L5-Swagger | [github.com/DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) |
| PHP 官方文件 | [php.net/docs](https://www.php.net/docs.php) |
| Docker Compose 文件 | [docs.docker.com/compose](https://docs.docker.com/compose/) |

---

## §11 延伸學習方向

- **認證授權**：Laravel Sanctum / Passport（API Token、OAuth）
- **進階查詢**：分頁、排序、模糊搜尋（Eloquent 進階）
- **Queue 非同步**：Laravel Queue + Redis 背景任務
- **CI/CD**：GitHub Actions 自動測試與建置
- **雲端部署**：Docker 部署到 AWS / GCP / Azure

---

## §12 維護與版本管理

| 項目 | 值 |
|------|-----|
| 教案版本 | v1.0 |
| 最後更新日期 | 2026-03-09 |
| 對應 Repo 分支 | `main` |
| Laravel 版本 | 12 |
| PHP 版本 | 8.4 |

### 開課前檢查清單

- [ ] §4 的驗證指令是否仍可正常執行？
- [ ] `php artisan test` 是否全部通過？
- [ ] 所有單元列出的檔案路徑是否仍存在且正確？
- [ ] Swagger UI 是否可正常訪問？
- [ ] Docker Compose 是否能一鍵啟動所有服務？
