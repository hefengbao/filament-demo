<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin') // 运行 php artisan filament:install --panels 时设置的 ID,可自行修改
            ->path('admin') // 路由前缀，可自行修改
            ->authGuard('admin')
            ->brandName('FilamentDemo') // 系统名称
            ->brandLogo(null) // Logo 路径
            ->brandLogoHeight(null) // Logo 高度
            ->login() // 登录页面
            ->registration() // 注册页面
            ->passwordReset() // 找回密码
            ->emailVerification() // 验证邮箱
            //->sidebarCollapsibleOnDesktop() // 桌面端浏览器访问时折叠侧导航栏，但是会显示导航图标
            ->sidebarFullyCollapsibleOnDesktop() // 完全折叠侧导航栏
            ->colors([
                'primary' => Color::Amber, // 主色调
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources') // 资源（Resource）目录，app/Filament/Resources
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages') // 页面（Page）目录, app/Filament/Pages
            ->pages([ // 框架提供的默认页面
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets') // 小部件（Widget）目录，app/Filament/Widgets
            ->widgets([ // 框架提供的默认小部件
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
