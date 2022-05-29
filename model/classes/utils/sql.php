<?php

namespace de\langner_dev\wiwi\model\utils;

$path = (defined("PATH")) ? PATH : "";
require_once $path . 'utils/configuration.php';
require_once $path . 'utils/functions.php';

/**
 * Verwaltet eine Verbindung zur Datenbank
 * @author Jonas Langner
 * @version 0.1.0
 * @since 0.1.0
 */
class SQL
{

    /**
     * Wandelt einen String in einen sicheren um.
     * @param string $string Der unsicher String.
     * @param bool $wrap_symbols Gibt an, ob die Anführungszeichen noch hinzugefügt werden sollen.
     * @param string $symbols Die String-Symbole in der SQL-Abfrage.
     * @return string Der sichere String.
     */
    public static function toSQLString($string, bool $wrap_symbols = true, string $symbols = "'"): string {
        if (!isset($string))
            return "null";

        $str = str_replace($symbols, "\\" . $symbols, str_replace("\\", "\\\\",$string));

        if ($wrap_symbols) {
            $str = $symbols . $str . $symbols;
        }

        return $str;
    }

    public static function date($time) {
        if ($time == null)
            return null;

        return date("Y-m-d", $time);
    }

    /**
     * Schließt die Verbindung zur DB
     * @return bool Verbindung mit Erfolg geschlossen
     */
    public static function closeDBConnection(): bool
    {

        /* überprüfen, ob verbindung schon besteht */
        if (isset($GLOBALS['db_connection'])) {
            // verbindung existiert -> diese schließen

            mysqli_close($GLOBALS['db_connection']);

            unset($GLOBALS['db_connection']);

            return true;
        }
        return false; // Standard
    }

    /**
     * Wandelt eine Rückgabe in eine einzelne um.
     * @param string|array $response Die Rückgabe.
     * @return array Der einzige Datensatz.
     */
    public static function getSingleResponse($response): array {

        if (is_array($response) && !empty($response)) {

            if (is_array($response[0]))
                return $response[0];

            return $response;
        }

        return array();
    }

    /* Verbindung - wird für Anweisungen/Anfragen benötigt */
    private $connection;

    /* Datenbankinformationen */
    private $host = DATABASE_HOST;
    private $user = DATABASE_USER;
    private $password = DATABASE_PASSWORD;
    /*
     * Datenbanken hier eintragen
     *
     * zum hinzufügen: einfach Namen mit in Anführungszeichen eintragen (mit Komma zum Vorherigen Namen trennen
     *
     * DATABASE_INDEX muss den Wert des Indexes von dem Namen haben:
     *
     *      1. Datenbankname: DATABASE_INDEX = 0
     *      5. Datenbankname: DATABASE_INDEX = 4
     */
    private $database_name = DATABASE_NAME;
    private $port = DATABASE_PORT;

    /**
     * SQL constructor.
     * @param bool $global Gibt an, ob die Connection global ist.
     */
    public function __construct(bool $global = true) {

        /* überprüfen, ob verbindung schon besteht */
        if (isset($GLOBALS['db_connection']) && $global) {
            // verbindung existiert -> diese verwenden
            $this->connection = $GLOBALS['db_connection'];
        }
        else {
            // keine Verbindung gefunden

            /* verbindung erstellen */
            $this->connection = mysqli_connect(
                $this->host,
                $this->user,
                $this->password,
                $this->database_name,
                $this->port)
            or die ("Verbindung zur Datenbank konnte nicht aufgebaut werden! Info: " . mysqli_connect_error());

            /* verbindung speichern */
            if ($global)
                $GLOBALS['db_connection'] = $this->connection;
        }

    }

    /**
     * Erstellt eine Abfrage
     * @param string $query SQL-Syntax der Abfrage
     * @return mixed "_" wenn Anweisung, Array wenn Abfrage
     */
    public function query(string $query) {
        /* Abfrage erstellen */
        $sql_return = mysqli_query($this->connection, $query);

        /* überprüfen, ob es eine Abfrage war (wenn ja: $sql_return ist kein bool) */
        if (!is_bool($sql_return)) {
            // war eine Abfrage
            $return = array(); // Rückgabe Format: Eine Zelle: array(Zeile1,Zeile2,Zeile3,...) Mehrere Zellen: array(array(Zeile 0 Zelle 0, Zeile 0 Zelle 1,...), array(Zeile 1 Zelle 0, Zeile 1 Zelle 1,...), ...)
            $i = 0; // Zähler (Zeile)

            while ($data = mysqli_fetch_array($sql_return)) { // data ist ein array der Spalten/Zellen der aktuellen Zeile ; Anzahl durchläufe = Anzahl Zeilen

                /* Überprüfen, wie viele Spalten */
                if (count($data) == 2) {
                    // eine Spalte -- jede Spalte wird doppelt zurückgegeben
                    $return[$i] = $data[0];
                }else {
                    // mehrere Spalten
                    $subarray = array(); // halbes data (ohne Text-Keys)

                    /* für jeden Schlüssel */
                    foreach (array_keys($data) as $key) {

                        /* überprüfen, ob int (noch nicht doppelt) */
                        if (is_int($key)) {
                            // nicht doppelt

                            /* subarray füllen mit jeweiliger Zahl */
                            $subarray[$key] = $data[$key];
                        }
                    }

                    /* Rückgabewert definieren */
                    $return[$i] = $subarray;
                }

                $i++; // zählt +1
            }

            if (count($return) == 1 && is_array($return[0]))
                $return = $return[0];

            return $return;
        }

        return "_"; // Standard
    }

    public function close() {
        if (isset($GLOBALS['db_connection']) && $GLOBALS['db_connection'] == $this->connection)
            unset($GLOBALS['db_connection']);

        mysqli_close($this->connection);
    }

}

class Statement {

    private $sql_query;
    private $params = array();

    public function __construct(string $query)
    {
        $this->sql_query = $query;
    }

    public function set(int $pos, $value) {
        $this->params[$pos] = SQL::toSQLString($value, $value != null && is_string($value));
    }

    public function execute(... $params) {
        if (!empty($params)) {
            for ($i = 0; $i < count($params); $i++) {
                $this->set($i, $params[$i]);
            }
        }

        if (is_array($this->params) && !empty($params)) {
            $splitted = explode('?', $this->sql_query);

            $str = '';

            for ($i = 0; $i < max(count($this->params), count($splitted)); $i++) {
                if (count($splitted) > $i && isset($splitted[$i])) {
                    $str .= $splitted[$i];
                }

                if (count($params) > $i && isset($this->params[$i])) {
                    $str .= $this->params[$i];
                }
            }

            $this->sql_query = $str;
        }

        // echo $this->sql_query;

        if (strpos($this->sql_query, "insert into") === 0) {
            $sql = new SQL(false);
            $sql->query($this->sql_query);

            $ret = $sql->query("select last_insert_id()");

            $sql->close();

            return $ret;
        }
        return (new SQL())->query($this->sql_query);
    }

}

$sql = new SQL();

/*
 * Tabellen erstellen ´
 */
$sql->query("create table if not exists `machine`(
    `id_machine` integer primary key auto_increment, 
    `name` varchar(16) not null unique, 
    `available_from` date not null, 
    `available_to` date, 
    `capacity_day` decimal(3,1) not null
)");

$sql->query("create table if not exists `good`(
    `id_good` integer primary key auto_increment,
    `name` varchar(16) not null unique,
    `main_good_id` integer default null,
    `amount_for_main_good` integer not null default 1 check ( `amount_for_main_good` >= 1 ),
    foreign key (`main_good_id`) references `good`(`id_good`) on delete set null on update cascade 
)");

$sql->query("create table if not exists `order`(
    `id_order` integer primary key auto_increment,
    `name` varchar(16) not null unique,
    `min_start` date not null
)");

$sql->query("create table if not exists `order_contains_good`(
    `order_id` integer not null,
    `good_id` integer not null,
    `machine_id` integer not null,
    `amount` integer not null check ( `amount` > 0 ),
    `position` integer not null check ( `position` >= 0 ),
    primary key (`order_id`, `good_id`, `machine_id`),
    foreign key (`order_id`) references `order`(`id_order`) on delete cascade on update cascade,
    foreign key (`good_id`) references `good`(`id_good`) on delete cascade on update cascade,
    foreign key (`machine_id`) references `machine`(`id_machine`) on delete cascade on update cascade 
)");

$sql->query("create table if not exists `schedule`(
    `good_id` integer not null,
    `order_id` integer not null,
    `machine_id` integer not null,
    `position` integer not null check ( `position` >= 0 ),
    `date` date not null,
    primary key (`good_id`, `order_id`, `machine_id`),
    foreign key (`good_id`) references `good`(`id_good`) on delete cascade on update cascade,
    foreign key (`order_id`) references `order`(`id_order`) on delete cascade on update cascade,
    foreign key (`machine_id`) references `machine`(`id_machine`) on delete cascade on update cascade 
)");