<!-- resources/views/auth/custom-login.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UTM Login') }}</title>

    <!-- スタイルシート -->
    <style>
        /* ベーススタイル */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            background-position: center;
        }

        /* ログインカード */
        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        /* ロゴ */
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            max-width: 80%;
            height: auto;
        }

        /* フォーム要素 */
        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #8b1c44;
            outline: none;
        }

        /* ログインボタン */
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #8b1c44; /* UTMの色を使用 */
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #741938;
        }

        /* リンク */
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #8b1c44;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        /* クッキー通知 */
        .cookie-notice {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }

        .cookie-notice a {
            margin-left: 5px;
            color: #8b1c44;
            text-decoration: none;
        }

        /* エラーメッセージ */
        .error-message {
            color: #8b1c44;
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* レスポンシブ */
        @media (max-width: 480px) {
            .login-card {
                max-width: 90%;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- ロゴ部分 -->
        <div class="logo-container">
            <img src="{{ asset('images/UTM-logo.png') }}" alt="UTM Logo" class="logo">
        </div>

        <!-- エラーメッセージの表示 -->
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- ログインフォーム -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- ユーザー名/メールアドレス入力 -->
            <div class="form-group">
                <input type="text" class="form-control" id="email" name="email"
                       value="{{ old('email') }}" placeholder="ユーザー名" required autofocus>
            </div>

            <!-- パスワード入力 -->
            <div class="form-group">
                <input type="password" class="form-control" id="password"
                       name="password" placeholder="パスワード" required>
            </div>

            <!-- ログインボタン -->
            <button type="submit" class="login-btn">ログイン</button>
        </form>

        <!-- パスワードを忘れた場合のリンク -->
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-password">
                パスワードをお忘れですか？
            </a>
        @endif

        <!-- クッキー通知 -->
        <div class="cookie-notice">
            <span>Cookies notice</span>
            <a href="#">詳細</a>
        </div>
    </div>
</body>
</html>
