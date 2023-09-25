# 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。

## 目录说明

```
-+
 |- app/
 |  |- Models/ 模型文件夹
 |  |- Admin/  平台管理后台
 |  |- Seller/ 加盟商后台
 |  |- Shop/   店铺前台（H5、微信共用）
 |  |  | Controllers
 |  |  |  |- Weapp
 |- resources/
 |- public
 |  |- build 编译后的 css 和 封装的 js 库
 |  |- vendor 
 |- routes
 |  |- admin.php   平台管理后台路由
 |  |- seller.php  卖家后台路由
 |  |- shop.php    店铺前台路由
```

## 技术架构

- 后端
  - PHP 8.2+
  - Laravel 10+
  - MySQL/PostgreSQL/SQLServer（不支持 SQLite）
  - Redis（可选）
  - Octane（可选）
  -  工具
    - `php-cs-fixer` 格式化 `PHP` 代码。不用 `pint`，`php-cs-fixer` 结合 `PHPStorm` 用起来更方便。
- 前端
  - `Vue3` 组合式 API
  - `ElementPlus` 完整引入（极大提高预览的加载速度）
  - `TailwindCSS` （扩充 ElementPlus 细节控制，布局、边距、文字）
    - 在 scss 中用 @apply 引用 tailwind 制作自定义组件
  - `Tinymce`
  - 工具
    - `Vite` 编译
    - `pnpm` 管理 `npm` 包
    - `eslint` 格式化 js 代码，并整合 `PHPStorm` 的 `eslint` 工具，勾选保存时运行 `eslint --fix`。
    - 主要图标使用 `iconfont` 字体图标，图标库在 `iconfont.cn` 图标项目维护
      - 图标风格必须统一
      - 图标必须在图标项目里调整统一尺寸
      - 外部图标可上传 svg 到图标项目
    - 一些工具图标可用 `@element-plus/icons-vue` 图标

## 地图&标点
腾讯地图，便于跟小程序整合。
