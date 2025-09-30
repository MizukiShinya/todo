# laravel-docker-template

# Laravel TODO アプリケーション

このプロジェクトはDockerを使用したLaravel TODOアプリケーションです。

## 必要な環境

- Docker Desktop
- Git

## セットアップ手順

### 1. リポジトリをクローン

```bash
git clone [リポジトリURL]
cd todo
```

### 2. MySQLの設定ファイルを準備

MySQLコンテナを正常に起動させるため、設定ファイルを確認・修正します。

```bash
# my.cnfファイルの内容を確認
cat docker/mysql/my.cnf
```

以下の内容になっていることを確認し、不足している行があれば追加してください：

```ini
[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
default-time-zone = 'Asia/Tokyo'
lower_case_table_names = 2
```

**特に`lower_case_table_names = 2`の行が必須です。** 
この設定がないとMySQLコンテナが起動しません。

### 3. 既存のDockerリソースをクリーンアップ

初回セットアップまたは再セットアップ時は、必ず以下のクリーンアップを実行してください：

```bash
# 既存のコンテナを停止・削除
docker compose down

# MySQLデータディレクトリをクリーンアップ
rm -rf ./docker/mysql/data/*

# Dockerボリュームも念のため削除（存在する場合）
docker volume prune -f
```

### 4. Dockerコンテナを起動して動作確認

```bash
# コンテナを起動
docker compose up -d

# 15秒待ってMySQLの初期化を待つ
sleep 15

# すべてのコンテナが正常に動作していることを確認
docker compose ps
```

**重要**: 
この時点で4つのコンテナ（nginx、php、mysql、phpmyadmin）がすべて「Up」状態になっていることを確認してください。

MySQLコンテナが表示されない、または「Exited」状態の場合は以下を実行：

```bash
# MySQLのログを確認
docker compose logs mysql

# 問題があれば手順3からやり直し
```

### 5. Laravelの初期セットアップを実行

すべてのコンテナが正常に起動したら、Laravelアプリケーションのセットアップを行います。

#### 5-1. Composerで依存関係をインストール

```bash
docker compose exec php composer install
```

#### 5-2. 環境設定ファイルを作成

```bash
# .envファイルを作成
docker compose exec php cp .env.example .env
```

#### 5-3. アプリケーションキーを生成

```bash
docker compose exec php php artisan key:generate
```

#### 5-4. データベース接続設定を更新

```bash
# 以下のコマンドを順番に実行
docker compose exec php sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env
docker compose exec php sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel_db/' .env
docker compose exec php sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel_user/' .env
docker compose exec php sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=laravel_pass/' .env
```

#### 5-5. データベースマイグレーションを実行

```bash
docker compose exec php php artisan migrate
```

### 6. アプリケーションの動作確認

```bash
# アプリケーションにアクセスできるか確認
curl -I http://localhost/
```

正常にセットアップが完了していれば、`HTTP/1.1 200 OK`が返ってきます。

## アクセス方法

セットアップが完了したら、以下のURLでアクセスできます：

- **TODOアプリケーション**: http://localhost/
- **phpMyAdmin**: http://localhost:8080/

## クイックセットアップ（一括実行）

上記の手順をすべて一括で実行する場合は、以下のコマンドを使用できます：

```bash
# MySQLの設定確認とクリーンアップ
docker compose down && \
rm -rf ./docker/mysql/data/* && \
docker volume prune -f && \
# コンテナ起動
docker compose up -d && \
sleep 15 && \
# Laravel初期設定
docker compose exec php composer install && \
docker compose exec php cp .env.example .env && \
docker compose exec php php artisan key:generate && \
docker compose exec php sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env && \
docker compose exec php sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel_db/' .env && \
docker compose exec php sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel_user/' .env && \
docker compose exec php sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=laravel_pass/' .env && \
docker compose exec php php artisan migrate && \
echo "セットアップ完了！ http://localhost/ にアクセスしてください。"
```

**注意**: このコマンドを実行する前に、必ず`docker/mysql/my.cnf`に`lower_case_table_names = 2`の設定があることを確認してください。


## データベース接続情報

- **ホスト**: mysql
- **データベース名**: laravel_db
- **ユーザー名**: laravel_user
- **パスワード**: laravel_pass
- **ルートパスワード**: root