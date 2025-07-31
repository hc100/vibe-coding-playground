# SkillLog カレンダー投稿アプリ（Laravel）

このアプリケーションは、スキルログをカレンダー形式で記録・管理するための Laravel ベースの Web アプリケーションです。
ローカル開発では SQLite を使用し、テスト環境にも対応しています。

---

## 📦 動作環境

- PHP 8.4 以上
- Composer
- SQLite（ローカル開発・テスト用）
- Laravel 11.x（想定）

### 必要な PHP 拡張モジュール

| 拡張モジュール     | 用途                                     |
|----------------------|------------------------------------------|
| `pdo_sqlite`         | SQLite データベース使用（テスト用）     |
| `xml`                | Laravel Pint の実行に必要               |
| `curl`               | HTTPリクエスト・Composerのパフォーマンス |
| `mbstring`           | マルチバイト文字列の処理               |
| `zip`                | ZIP 圧縮ファイルの処理                  |
| `bcmath`             | 任意精度計算（Laravelの一部で使用）     |
| `mysql`              | MySQL を使用する場合に必要              |

Ubuntu などでは以下のようにインストール可能です：

```bash
sudo apt install php8.4-sqlite3 php8.4-pdo php8.4-xml php8.4-curl php8.4-mbstring php8.4-zip php8.4-bcmath php8.4-mysql
```

---

## 🔧 セットアップ手順

### 1. リポジトリをクローン

```bash
git clone git@github.com:hc100/vibe-coding-playground.git
cd vibe-coding-playground
```

### 2. `.env` ファイルを作成・設定

```bash
cp .env.example .env
```

SQLite を使用する場合、`.env` の内容を以下のように修正してください：

```env
DB_CONNECTION=sqlite
DB_DATABASE=${PWD}/database/database.sqlite
```

必要に応じて MySQL などに変更可能です。

---

### 3. Composer パッケージのインストール

```bash
composer update
```

---

### 4. 必要なディレクトリの作成

```bash
mkdir -p tests/Unit
mkdir -p database && touch database/database.sqlite
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R 775 storage bootstrap/cache
```

---

### 5. マイグレーションの実行

```bash
php artisan migrate
```

---

## 🧪 テストの実行

```bash
php artisan test
```

---

## 💅 コード整形（Laravel Pint）

このプロジェクトでは [Laravel Pint](https://laravel.com/docs/pint) によるコード整形を使用しています：

```bash
vendor/bin/pint
```

---

## 📁 プロジェクト構成（抜粋）

```
app/
├── Models/
├── Http/
│   └── Controllers/
database/
├── migrations/
├── seeders/
tests/
└── Unit/
```
