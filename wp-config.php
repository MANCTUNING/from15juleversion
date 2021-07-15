<?php

/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'mancpetya_tuning' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'mancpetya_tuning' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', 'NR5fmN4NR5fmN4' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'J`H^D9.Q2>e[T<kJpj%a.Vyk9As-uYrk`j 0!UG`chzgcJFe6rR{IJem8hxJ9+At' );
define( 'SECURE_AUTH_KEY',  'vv<*P:A^!SN!1H.$+#@| NO$2M[f&a%P?o3Odrbn6tQm;IR|;n0298h(mMJK7y!l' );
define( 'LOGGED_IN_KEY',    'h7kAcKpl-K+A;+Vrmyr7KQ(:auByk3PEuF2vI; .7:=D#t>0!Q!2BUC+]%[$.iW6' );
define( 'NONCE_KEY',        ' i9reLgZd{EbJiSd1*+PmiG2FXqF>g,QMx[qX.AHZjNHO#fiLW+OJo6M~rQ)YHUR' );
define( 'AUTH_SALT',        'Z<x!yVKz0Ca=f-U<XH+`L>;Cc^#7IUZl066ulj&oW)?h]rrQvLsJI2l*C$h]?wj3' );
define( 'SECURE_AUTH_SALT', ':;JVTUE<0xT/$zRE}7?qyiuXr<%kjOuq|/N|E0!7qLWHhFw[M:d;#+DPd!2NKjpV' );
define( 'LOGGED_IN_SALT',   ',[S/iWQN9m&VDvdd+Y${WwE([IHU-Ys@#fI(p|ZSOD!g6-mgI;z1XMA?B.4`RC{o' );
define( 'NONCE_SALT',       'IAq:n;6+LRFRO(,hOFaV,s*VEg)RUOwVZX;B3(.$CWb)x`]Yi@&mf tD3r`inlW;' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', 1 );
define( 'WP_DEBUG_LOG', 1 );

#define( 'WP_HOME', 'http://mancpetya.beget.tech' );
#define( 'WP_SITEURL', 'http://mancpetya.beget.tech' );
#define( 'WP_HOME', 'https://manctuning.ru' );
#define( 'WP_SITEURL', 'https://manctuning.ru' );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';