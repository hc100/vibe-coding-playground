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
backend/
├── app/
│   ├── Models/
│   └── Http/Controllers/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
└── tests/
```

---

## 🐳 Docker 開発環境（php-8.4 / nginx-1.28 / mysql-8.0）

- 構成: `backend/` に Laravel 本体、`infra/docker/` に Dockerfile 群。
- 起動: プロジェクト直下で `docker-compose up --build`。
- アクセス: http://localhost:8080
  - メール確認（MailHog）: http://localhost:8025

初回セットアップ（別ターミナルで）:

```
docker compose exec app php artisan migrate
# 必要に応じて
docker compose exec app php artisan db:seed
```

補足:
- `backend/.env` は Docker 用に `DB_HOST=db`、DB 名/ユーザー/パスワードは `laravel/laravel` に設定済みです。
- メールは MailHog（SMTP）を使用するよう設定済みです（`MAIL_MAILER=smtp`、`MAIL_HOST=mailhog`、`MAIL_PORT=1025`）。
- PHP コンテナは `vendor/` が無い場合に `composer install` を実行し、`APP_KEY` 未設定の場合は自動生成します。
- フロント資産は `cd backend && npm ci && npm run build` でビルドしてください（Nginx は `public/` を配信）。

---

## 🚀 デプロイ（GitHub Actions → AWS Lightsail Containers）

- 自動デプロイ: `main` ブランチへ push で実行。
- ワークフロー: `.github/workflows/deploy-lightsail.yml`

事前準備（GitHub Secrets）:
- `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY`: Lightsail への push 権限を持つ IAM ユーザーのキー。
- `AWS_REGION`: 例 `ap-northeast-1`。
- `LIGHTSAIL_SERVICE_NAME`: 作成・更新対象の Lightsail コンテナサービス名。
- `APP_URL`: 公開 URL（例 `https://example.com`）。
- `DB_HOST`, `DB_PORT`(任意), `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: 本番用 DB 接続情報（Lightsail マネージド DB 推奨）。

ワークフローの流れ:
- Node で `backend` のアセットをビルド。
- 本番用 Docker イメージ（`infra/docker/*/Dockerfile.prod`）をビルド。
- `aws lightsail push-container-image` で `app` / `web` の 2 つのラベルとして登録。
- `create-container-service-deployment` で `web` を公開エンドポイント(80/tcp)、`app` は内部向け PHP-FPM として配置。

メモ:
- 初回はサービスが無ければ自動で `power=small, scale=1` で作成します（必要に応じて調整）。
- マイグレーションは自動実行していません。デプロイ後にワンショットで実行する運用（例: メンテナンス用ジョブ）をご検討ください。

### IAM 権限（AccessDenied の対処）

`aws lightsail push-container-image` で `lightsail:RegisterContainerImage` の権限不足が出る場合、CI が Assume するロールに Lightsail のコンテナ関連権限を付与してください。サンプルポリシーを `infra/aws/iam/lightsail-deploy-policy.json` に用意しています。

適用例（AWS 管理コンソール または IaC でロールにアタッチ）:

- 許可アクション（抜粋）:
  - `lightsail:RegisterContainerImage`
  - `lightsail:CreateContainerServiceDeployment`
  - `lightsail:CreateContainerService`
  - `lightsail:UpdateContainerService`
  - `lightsail:GetContainerServices` ほか参照系

最小権限で運用する場合は、`Resource` を対象サービスに絞ることを推奨します。
