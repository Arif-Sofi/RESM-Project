import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/Types';

interface WelcomeProps extends PageProps {
  canLogin: boolean;
  canRegister: boolean;
}

export default function Welcome({ auth, canLogin, canRegister }: WelcomeProps) {
  return (
    <>
      <Head title="Welcome" />

      <div className="flex min-h-screen">
        {/* Left Panel */}
        <div className="w-full md:w-2/5 p-8 flex flex-col justify-center relative overflow-hidden bg-gradient-to-br from-amber-500 to-amber-100">
          {/* Logo */}
          <div className="mb-6 mt-6 flex justify-center">
            <Link href="/">
              <img
                src="/images/SKSU-logo.png"
                alt="SKSU Logo"
                className="w-32 h-32 object-contain"
              />
            </Link>
          </div>

          <h1 className="text-4xl md:text-5xl font-bold mb-8 text-gray-800 text-center">
            立入禁止区域
          </h1>

          {/* Nav Links */}
          {canLogin && (
            <div className="nav-links space-y-4 max-w-sm mx-auto w-full">
              {auth.user ? (
                <Link
                  href={route('dashboard')}
                  className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
                >
                  ダッシュボード
                </Link>
              ) : (
                <>
                  <Link
                    href={route('login')}
                    className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
                  >
                    関係者以外立ち入り禁止
                  </Link>
                  {canRegister && (
                    <Link
                      href={route('register')}
                      className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
                    >
                      新規登録
                    </Link>
                  )}
                </>
              )}

              <Link
                href={route('events.index')}
                className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
              >
                廃イベント記録表（破損あり）
              </Link>

              <a
                href="https://laravel.com/docs"
                target="_blank"
                rel="noopener noreferrer"
                className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
              >
                最後の通信
              </a>

              <a
                href="https://laracasts.com"
                target="_blank"
                rel="noopener noreferrer"
                className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
              >
                Outcasts
              </a>

              <a
                href="https://cloud.laravel.com"
                target="_blank"
                rel="noopener noreferrer"
                className="block w-full px-4 py-3 bg-white rounded-lg text-center font-medium text-gray-800 hover:bg-gray-800 hover:text-white transition duration-200 shadow-md"
              >
                Assimilate
              </a>
            </div>
          )}

          {/* Decorative elements */}
          <div className="absolute top-[10%] left-[10%] w-36 h-36 rounded-full bg-white/30 -z-10"></div>
          <div className="absolute top-1/2 right-[15%] w-20 h-20 rounded-full bg-white/30 -z-10"></div>
          <div className="absolute bottom-[10%] left-[20%] w-28 h-28 rounded-full bg-white/30 -z-10"></div>
          <div className="absolute top-[20%] right-[20%] w-16 h-16 rounded-full bg-white/30 -z-10"></div>
        </div>

        {/* Right Panel */}
        <div
          className="hidden md:block w-3/5 bg-cover bg-center"
          style={{
            backgroundImage: "url('/images/background.PNG')",
          }}
          role="img"
          aria-label="学校の背景画像"
        ></div>
      </div>
    </>
  );
}
