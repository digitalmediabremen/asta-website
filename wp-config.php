<?php

// Configuration common to all environments
include_once __DIR__ . '/wp-config.common.php';

/**
 * Grundeinstellungen für WordPress
 *
 * Zu diesen Einstellungen gehören:
 *
 * * MySQL-Zugangsdaten,
 * * Tabellenpräfix,
 * * Sicherheitsschlüssel
 * * und ABSPATH.
 *
 * Mehr Informationen zur wp-config.php gibt es auf der
 * {@link https://codex.wordpress.org/Editing_wp-config.php wp-config.php editieren}
 * Seite im Codex. Die Zugangsdaten für die MySQL-Datenbank
 * bekommst du von deinem Webhoster.
 *
 * Diese Datei wird zur Erstellung der wp-config.php verwendet.
 * Du musst aber dafür nicht das Installationsskript verwenden.
 * Stattdessen kannst du auch diese Datei als wp-config.php mit
 * deinen Zugangsdaten für die Datenbank abspeichern.
 *
 * @package WordPress
 */

// ** MySQL-Einstellungen ** //
/**   Diese Zugangsdaten bekommst du von deinem Webhoster. **/

/**
 * Ersetze datenbankname_hier_einfuegen
 * mit dem Namen der Datenbank, die du verwenden möchtest.
 */
define( 'DB_NAME', 'asta-wordpress' );

/**
 * Ersetze benutzername_hier_einfuegen
 * mit deinem MySQL-Datenbank-Benutzernamen.
 */
define( 'DB_USER', 'root' );

/**
 * Ersetze passwort_hier_einfuegen mit deinem MySQL-Passwort.
 */
define( 'DB_PASSWORD', 'root' );

/**
 * Ersetze localhost mit der MySQL-Serveradresse.
 */
define( 'DB_HOST', 'localhost' );

/**
 * Der Datenbankzeichensatz, der beim Erstellen der
 * Datenbanktabellen verwendet werden soll
 */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Der Collate-Type sollte nicht geändert werden.
 */
define('DB_COLLATE', '');

/**#@+
 * Sicherheitsschlüssel
 *
 * Ändere jeden untenstehenden Platzhaltertext in eine beliebige,
 * möglichst einmalig genutzte Zeichenkette.
 * Auf der Seite {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * kannst du dir alle Schlüssel generieren lassen.
 * Du kannst die Schlüssel jederzeit wieder ändern, alle angemeldeten
 * Benutzer müssen sich danach erneut anmelden.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '&4u${)Y/@5PGsHD85#]2%hasPa:Qf)UqK.S&jE1c$gHRt[UL}-Zx6+9AfcUvCZ;r' );
define( 'SECURE_AUTH_KEY',  '(U4r~!2P^@OSiY] #X}xls+Ysr=cDhsp<qEx66K_fN:aL*L;mubOEo]I?gGgk*0@' );
define( 'LOGGED_IN_KEY',    '_Sz&FbrP,5S$&0m}otI#!AG4zx0)Ap[x[+H<|(b# TEDI/#Pb%{yRbVV.U*/&BS<' );
define( 'NONCE_KEY',        'Z7Q:7^?HfKu,/DdMgm]DD5-iNV;8#9^;C{[76<CIjx&4XI:jeUS%W-}=e/(@P84D' );
define( 'AUTH_SALT',        '^VTD$tw,lKP7]!9#Yp8GXMuPcZwJ<BS( .6f-sx+L/!c6:-k@/4e=RG=.hOX6Rd:' );
define( 'SECURE_AUTH_SALT', '^HheWtY*[ga-oN0/+#_)@`i^%`AW7{o&Hy(|)~}`EPX(YzA)>n;OM:~DA%a;2mCu' );
define( 'LOGGED_IN_SALT',   'r-j@3W$^Ec/E)?D(4X>a&mpR{JY6jJiQRh?8K|GbKHGPpK6^_]z(UPd)5/OgwF!=' );
define( 'NONCE_SALT',       'y3Kc8v5ba@*hy(HNUyhk/@a:Do 0^7yHW`4M):7S4J@g^:ksdX0,!b*T=va?5y>y' );

/**#@-*/

/**
 * WordPress Datenbanktabellen-Präfix
 *
 * Wenn du verschiedene Präfixe benutzt, kannst du innerhalb einer Datenbank
 * verschiedene WordPress-Installationen betreiben.
 * Bitte verwende nur Zahlen, Buchstaben und Unterstriche!
 */
$table_prefix = 'wp_';

/**
 * Für Entwickler: Der WordPress-Debug-Modus.
 *
 * Setze den Wert auf „true“, um bei der Entwicklung Warnungen und Fehler-Meldungen angezeigt zu bekommen.
 * Plugin- und Theme-Entwicklern wird nachdrücklich empfohlen, WP_DEBUG
 * in ihrer Entwicklungsumgebung zu verwenden.
 *
 * Besuche den Codex, um mehr Informationen über andere Konstanten zu finden,
 * die zum Debuggen genutzt werden können.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

define('VP_ENVIRONMENT', 'dev-jelko');
/* Das war’s, Schluss mit dem Bearbeiten! Viel Spaß. */
/* That's all, stop editing! Happy publishing. */

/** Der absolute Pfad zum WordPress-Verzeichnis. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Definiert WordPress-Variablen und fügt Dateien ein.  */
require_once( ABSPATH . 'wp-settings.php' );
