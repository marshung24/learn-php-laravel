# 基底映像：PHP 8.3 FPM（官方映像）
FROM php:8.3-fpm

# 安裝系統依賴與 PHP 擴充套件
#   git, curl, unzip       — 基本開發工具
#   tini                   — 容器內的 init process，確保子程序能正常結束
#   iproute2, net-tools    — 網路除錯（ip, ifconfig, netstat 等指令）
#   vim, bash-completion   — 終端機編輯與自動補全
#   default-mysql-client   — 可在容器內直接用 mysql 指令連資料庫除錯
#   redis-tools            — 可在容器內直接用 redis-cli 指令連 Redis 除錯
#   libpng-dev, libjpeg-dev, libfreetype6-dev — GD 圖片處理擴充套件所需
#   libzip-dev             — zip 擴充套件所需
#   libicu-dev             — intl 國際化擴充套件所需
#   libpq-dev              — PostgreSQL 擴充套件所需（備用）
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl ca-certificates unzip bash tini sudo \
    iproute2 iputils-ping net-tools \
    vim \
    bash-completion \
    default-mysql-client redis-tools \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
  && rm -rf /var/lib/apt/lists/* \
  && echo 'alias ll="ls -l"' >> /etc/bash.bashrc \
  && echo 'alias la="ls -la"' >> /etc/bash.bashrc

# 安裝 PHP 擴充套件
#   pdo_mysql      — MySQL 資料庫連線
#   redis          — Redis 快取
#   gd             — 圖片處理
#   zip            — 壓縮/解壓縮
#   intl           — 國際化（Laravel 多語系）
#   opcache        — PHP 加速
#   pcntl          — Laravel Horizon、Queue Worker 需要
#   bcmath         — 高精度數學運算
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    gd \
    zip \
    intl \
    opcache \
    pcntl \
    bcmath

# 安裝 Redis 擴充套件（透過 PECL）
RUN pecl install redis && docker-php-ext-enable redis

# 安裝 Composer（PHP 套件管理工具）
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 安裝 Node.js 與 npm（Laravel 前端資源編譯需要）
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get install -y nodejs \
  && rm -rf /var/lib/apt/lists/*

# 設定容器時區為台北
RUN ln -sf /usr/share/zoneinfo/Asia/Taipei /etc/localtime \
  && echo "Asia/Taipei" > /etc/timezone

# 本機專案目錄會透過 bind mount 掛載進容器，這行讓 git 信任掛載進來的目錄
RUN git config --global --add safe.directory '*'

WORKDIR /workspaces

# tini 作為 PID 1，負責處理信號轉發與回收子程序
ENTRYPOINT ["/usr/bin/tini","--"]
CMD ["bash","-lc","sleep infinity"]
