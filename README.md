# 實驗室網站
這是一個使用原生 PHP 打造的研究室網站後端 API，包含成員、權限管理、文件檔案上傳、下載功能。

# 功能介紹
本專案採用 Postman 進行測試，若欲查看 API 詳細資訊，請使用 Postman，並複製下方連結進行匯入。

[Postman 連結](
https://www.getpostman.com/collections/d5e74fe839abf336754c
"Postman 連結"
)
![image](https://user-images.githubusercontent.com/57283718/172524287-e7186c53-2f77-4f19-9ddf-6aed894a1503.png)


## 安裝前提
* [WAMP PHP 8.1↑, MySQL 8.0↑](https://www.wampserver.com/en/download-wampserver-64bits/)
* [Composer](https://getcomposer.org/)

## 如何安裝
開啟終端機(Terminal)，Clone 這個專案
```
git clone https://github.com/GXiang314/jacklab.git
```

進入資料夾，更新套件
```
cd jacklab
composer install
```

將.env.example 文件，複製一份並重新命名為.env，並依照您的環境配置.env檔案
```
copy .env.example .env
```

MySQL 匯入資料庫



Apache 設定 virtualhost
```
<VirtualHost *:80>
  ServerName localhost
  DocumentRoot "${INSTALL_DIR}/www/jacklab/public"
  <Directory "${INSTALL_DIR}/www/jacklab/public">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```

## 啟動伺服器
開啟 Wampserver
![wamp](https://user-images.githubusercontent.com/57283718/172529276-6689a840-bee5-46bc-b217-a830cf4b286d.png)

當 Wamp 工作列圖示轉為綠色時，輸入網址即可使用 API 
```
http://localhost/api/?
```

