# 實驗室網站
這是一個使用原生 PHP 打造的研究室網站後端 API，包含成員、權限管理、文件檔案上傳、下載功能，。

# 功能介紹
本專案採用 Postman 進行測試，若欲查看 API 詳細資訊，請使用 Postman，並複製下方連結進行匯入。

[Postman 連結](
https://www.getpostman.com/collections/d5e74fe839abf336754c
"Postman 連結"
)
![image](https://user-images.githubusercontent.com/57283718/172524287-e7186c53-2f77-4f19-9ddf-6aed894a1503.png)


## 安裝前提
* [WAMP PHP 8.1↑, MySQL 8.0↑](https://www.wampserver.com/en/download-wampserver-64bits/)
* [MySQL 8.0](https://dev.mysql.com/doc/relnotes/mysql/8.0/en/)
* [Composer](https://getcomposer.org/)

## 如何安裝
開啟終端機(Terminal)，Clone 這個專案
```
git clone https://github.com/GXiang314/jacklab.git
```

進入資料夾，載入 authload, dotenv 套件
```
cd jacklab
composer init
composer require vlucas/phpdotenv
```

將.env.example 文件，複製一份並重新命名為.env，並依照您的環境配置.env檔案
```
copy .env.example .env
```

MySQL 匯入資料庫

[web.zip](https://github.com/GXiang314/jacklab/files/8858135/web.zip)


