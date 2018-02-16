# 长者论坛 后端部分

## 目录结构
* `api`：后端接口处理文件
  * `forum`：论坛核心接口
  * `user`：用户接口
* `common`：公用函数库
* `config`：公用配置文件
  * `databases.php`：数据库配置文件
  * `routes.php`：路由配置文件
  * `template_*.json`：填写登录/注册/修改个人信息时的模板
* `vendor` 和 `composer.lock`、`composer.json`：composer相关文件
* `router.php`：论坛URL路由处理文件


## nginx 配置指南
```nginx
server {
        # ...
        # php-fpm 配置，因人而异
        location ~* \.php$ {
                try_files $uri =404;
                fastcgi_index index.php;
                fastcgi_pass unix:/run/php/php7.0-fpm.sock;
                include /usr/local/nginx/conf/fastcgi.conf;
        }
        # /api/ 打头：调用接口； /forum/ 打头：调用前端静态模板
        location ~ ^(/api/|/forum/) {
                rewrite ^(.*)$ /router.php last;
        }
        # /common/ 或 /config/ 打头，以及composer相关文件：禁止访问
        location ~ ^(/common/|/config/|/vendor/|/composer) {
                return 403;
        }
        # ...
}
```
