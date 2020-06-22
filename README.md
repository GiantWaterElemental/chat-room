<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://img.shields.io/badge/php-%5E7.2.27-blue" alt="PHP Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Background

<p align="center"><img src="https://github.com/GiantWaterElemental/chat-room/blob/master/20200621175000_1.png?raw=true"></p>

This is a personal practice on multi-person real-time communicating system, aka chat room, based on : 

- [PHP](https://www.php.net/). Best language in the world.
- [MySQL](https://www.mysql.com/). The world's most popular open source database.
- [Laravel](https://www.laravel.com/). The PHP Framework For Web Artisans.
- [Swoole](https://www.swoole.com/). Coroutine based Async PHP programming framework.
- [LaravelS](https://github.com/hhxsv5/laravel-s). An out-of-the-box adapter between Swoole and Laravel/Lumen.
- [nginx](http://nginx.org/). An HTTP and reverse proxy server.
- [Bootstrap](https://getbootstrap.com/). The world’s most popular front-end open source toolkit.
- [jQuery](https://jquery.com/). A fast, small, and feature-rich JavaScript library.

## Start

This project is currently installed on my own Aliyun web server, please feel free to access [home](http://139.224.15.38/home), register and start chatting with others.

## Sequence Diagram

```sequesnce

participant user
participant browser/websocket client
participant nginx
participant web service
participant redis
participant mysql
participant websocket server

user->browser/websocket client: enter chat room
browser/websocket client->nginx: request for chat room info
nginx->web service: forward to
web service->redis: add user to chat room user set
web service->mysql: get chat room info
mysql-->web service: return chat room info
web service-->browser/websocket client: response chat room info
browser/websocket client->nginx: handshake
nginx->websocket server: forward to
websocket server->redis: add fd to chat room fd set
websocket server-->browser/websocket client:101 switching protocols
browser/websocket client-->user: display chat room
user->browser/websocket client: send message
browser/websocket client->nginx: send data
nginx->websocket server: forward to
websocket server->mysql: insert message data
websocket server->redis: get all fd from chat room fd set
redis-->websocket server: return fd list
websocket server->browser/websocket client: send data to all fd in chat room
browser/websocket client->user: display message

```

## Todo

- fix bugs
- add image & emoji support
- a backstage management system(should be a new project)
