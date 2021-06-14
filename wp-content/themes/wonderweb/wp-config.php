<?php
$src = '/home/dr319515/wonder-web.com.ua/wordpress'; // без слеша в конце
$host = 'dr319515.mysql.tools';
$login = 'dr319515_wordpress';
$password = '0j+8fFgU@8';
$db_name = 'dr319515_wordpress';



/** Имя базы данных для WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', $src.'/wp-content/plugins/wp-super-cache/' );
define( 'DB_NAME', $db_name );
/** Имя пользователя MySQL */
define( 'DB_USER', $login );
/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', $password );
/** Имя сервера MySQL */
define( 'DB_HOST', $host );
/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );
/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );
/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'eUD<gyw<q80)6?jr{VU@|JB2tkM;#~7SbnSL`X<.RZ0N8Y.i=?UX{&S?<Kgu@+Ib' );
define( 'SECURE_AUTH_KEY',  '`GFa3@5%@RdNBHo6T]B;.tSN7nxen^bWvS@j-w!q:F!]d?87(m7DS*TyB-0 5dzo' );
define( 'LOGGED_IN_KEY',    'D57jRcz@[uYfo}D^y^p!2L]PJ{0NN<{W%4;q<UO/Lch!)R$Sc7A0!S!jWZ|#O+_Q' );
define( 'NONCE_KEY',        '1-Z!/]Tn7.Sw7^mUT[z/{sFF]y&qum.,JP#^;rca>6SQv2x)h628dXGF8=T)d?h;' );
define( 'AUTH_SALT',        'L6`0P2}#K7#@!~^Wz_g&V0/Jw.q4qr>}g2?,b7euNg(Ux;^HZ7{Z{ew]Oh$g?!8H' );
define( 'SECURE_AUTH_SALT', '?JnK@uQrx|/jep;,j_1SZJa0FI }w^x&d+J0+e&`BC!;R?iXD0G[tHIL&A4h,2L.' );
define( 'LOGGED_IN_SALT',   '!M3I`N0pec0ODPQ@GG[T,ISC0}gZr3pm:W89`Z%M&#pD6PeMah|&}KO;jseA5P0s' );
define( 'NONCE_SALT',       'mjdmLPa sS%@v3}JYBi|8$RbZgGqSTr(Z{Q!a)ym(k/0}7po#]}oSs$5Dm52iNrQ' );
/**#@-*/
/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wwdawp_';
/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
/* Это всё, дальше не редактируем. Успехов! */
/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
