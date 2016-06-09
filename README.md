# Wordpress Cures

Wordpress is pretty cool, and flexible as rubber. But everytime you setup a
project you find yourself looking for that little snippet that fixes that
annoying little feature you desperately _not_ need.

I've started to collect all those little hacks and tricks that makes your live
easier. Find them listed here, or download and implement `functions.php` to
just get started.

## 1. Snippets, hacks and tweaks

### 1.1 Optimize image quality

**What's the problem?** Wordpress automatically compresses uploaded images. Which is good in
terms of disk usage and site speed, but not if you don't want your crisp and 
beautiful artwork to get vague.

**How to fix?**

```
add_filter( 'jpeg_quality', function( $arg ) { return 100; });
```

### 1.2 Disable Emojicons

**What's the problem?** For some reason our friends have implemented emojicons
and switched it on by default. Aaargghh!! Wordpress is no Snapchat, right? And
more importantly; it's always better to avoid inline scripting/styling.

**How to fix?**

```php
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
```

### 1.3 Disable admin bar

**What's the problem?** The ever present admin bar on top of the screen. You may
like it and skip this part. I found it totally useless, and as such just a waste
of my screen.

**How to fix?**

```php
show_admin_bar(false);
```

## 2. Other tricks

### 2.1 Changing the Wordpress Site URL [1]

Open up `wp_login.php` and insert these lines of code
after `require( dirname(__FILE__) . '/wp-load.php' );` (_without_ trailing 
slash)

```php
//FIXME: do comment/remove these hack lines. (once the database is updated)
update_option('siteurl', 'http://your.domain.name/the/path' );
update_option('home', 'http://your.domain.name/the/path' );
```

NB: url's of images in posts will not be changed.

_TODO: document the sql query to fix this_

### 2.2 Enable GZIP compression

Google Pagespeed is loves it. And your visitors too, because your site will be faster. Woop woop!

Paste this in your .htaccess

```
# Enable compression
<IfModule mod_deflate.c>
    # Insert output filter by type
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript
    AddOutputFilterByType DEFLATE application/xml application/xhtml+xml application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript application/x-javascript
    AddOutputFilterByType DEFLATE application/x-httpd-php
    AddOutputFilterByType DEFLATE application/cgi-php5
    AddOutputFilterByType DEFLATE application/cgi-php53
    AddOutputFilterByType DEFLATE application/cgi-php54
</IfModule>
# Don't compress images, compressed files, docs nor movies
SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png)$ no-gzip dont-vary
SetEnvIfNoCase Request_URI \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
SetEnvIfNoCase Request_URI \.(?:pdf|doc)$ no-gzip dont-vary
SetEnvIfNoCase Request_URI \.(?:avi|mov|mp3|mp4|rm)$ no-gzip dont-vary

php_flag zlib.output_compression on
```

### 2.3 Compress images [2]

By compressing your images without losing any image quality you can decrease file size, and as such increase your site's speed and PageSpeed scores.

Run `jpegoptim` and `optipng` in a cronjob to automatically compress newly uploaded images. Don't forget to run the command the first time manually without the `-mtime` switch to optimize all older images.

Put these to lines in your crontab (`crontab -e`):

```
0 1 * * 0 lockrun -Q -L .lockjpegoptim -- find /path/to/wordpress/wp-content/uploads/ -mtime -7 -iname *.jpg -exec jpegoptim --strip-all -p {} \; > /dev/null
0 3 * * 0 lockrun -Q -L .lockoptipng -- find /path/to/wordpress/wp-content/uploads/ -mtime -7 -iname *.png -exec optipng -o7 -preserve {} \; > /dev/null
```

If you don't have a crontab or one of those tools available you can use a service like [reSmush.it](http://www.resmush.it/). It has a few downsides though (images may not be larger then 1mb).

Via: [Byte](https://www.byte.nl/blog/image-optimization-using-cronjobs)

### 2.4 Browser caching

Set the default `Expires` headers to a month in the future.

```
# Enable expres headers
ExpiresActive On
ExpiresDefault A0
ExpiresByType image/gif A2592000
ExpiresByType image/png A2592000
ExpiresByType image/jpg A2592000
ExpiresByType image/jpeg A2592000
ExpiresByType image/ico A2592000
ExpiresByType text/css A2592000
ExpiresByType text/javascript A2592000
```

## 3 References

- [1]: Wordpress.org, official documentation: https://codex.wordpress.org/Moving_WordPress#If_You_Have_Accidentally_Changed_your_WordPress_Site_URL
- [2]: Byte Blog: https://www.byte.nl/blog/image-optimization-using-cronjobs
