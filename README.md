# アプリケーション名

模擬案件初級_フリマアプリ

## 環境構築

**Dockerビルド**<br>
1. git clone git@github.com:matono-yutaka/flea-market-app.git

2. 以下のコマンドで Docker コンテナをビルド・起動

```
docker compose up -d --build
```

**Laravel環境構築**<br>
1. docker-compose exec php bash

2. composer install

3. env.example ファイルから.env を作成し、環境変数を変更

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

4. アプリケーションキーの作成

```
php artisan key:generate
```

5. マイグレーションの実行

```
php artisan migrate
```

6. シーディングの実行

```
php artisan db:seed
```

7. シンボリックリンク作成

```
php artisan storage:link
```

## 使用技術(実行環境)

・PHP 7.4.9<br>
・Laravel 8.83.29<br>
・MySQL 8.0.26<br>
・Docker / Docker Compose<br>
・phpMyAdmin<br>
・Stripe<br>
・MailHog<br>

## ER 図

[ER図スプレッドシート](https://docs.google.com/spreadsheets/d/1OUY9B2MLe_3QxS4dV_vYmI4MEvD9cv_S_CWObJk2vgc/edit?usp=sharing)


## URL

- 環境開発： http://localhost/
- phpMyAdmin： http://localhost:8080/
- MailHog： http://localhost:8025/
- Stripe： https://dashboard.stripe.com/test/dashboard

