<?php

namespace de\langner_dev\ui\utils\localization;

/**
 * Der Localizer ermittelt fpr jede Sprache die Strings.
 * Die String-Arrays müssen in einer JSON-Datei gespeichert werden, sodass dieser Localizer die Strings richtig parsen kann.
 * Die Datei muss <Sprachkürzel>.json heißen.
 */
class Localizer {

    private static $strings_dir_url = "strings";
    private static $shared;

    /**
     * Gibt den Pfad zu dem Ordener mit den JSON-Dateien an.
     * Dort sollen alle Sprachen mit deren Strings gespeichert sein.
     */
    public static function setStringsDirUrl(string $strings_dir_url) {
        self::$strings_dir_url = $strings_dir_url;
    }

    public static function getSharedLocalizer(): Localizer {
        if (self::$shared == null) {
            self::$shared = new Localizer();
        }
    
        return self::$shared;
    }

    private $resources = array();

    /**
     * Ermittelt ein String-Array mit den Key-Value paaren für eine bestimmte Sprache.
     * @param Language $language Die Sprache, für die das Array herausgesucht werden soll.
     * @return array|null Das gefundene Array mit den Strings für die angagebene Sprache.
     */
    private function getResources(Language $language) {
        if (!isset($this->resources[$language->getLanguageKey()])) {
        
            $file_path = self::$strings_dir_url . "/" . $language->getLanguageKey() . ".json";

            if (file_exists($file_path)) {
                $this->resources[$language->getLanguageKey()] = json_decode(file_get_contents($file_path), true);
            }
            else return null;
        }

        return $this->resources[$language->getLanguageKey()];
    }

    /**
     * Ermittelt den String für die geforderte Sprache.
     * @return string|null
     */
    public function getString(string $key, Language $language) {
        $r = $this->getResources($language);
        if ($r != null) {
            return $r[$key];
        }

        return null;
    }

}

/**
 * Identifiziert eine Sprache anhand des Kürzels und dem Namen.
 * Das Kürzel wird für die Localization verwendet.
 */
class Language {

    private static $current_language;

    public static function setCurrentLanguage(Language $l) {
        self::$current_language = $l;
    }

    public static function getCurrentLanguage(): Language {
        if (self::$current_language == null) {
            self::$current_language = new Language("en", "English");
        }

        return self::$current_language;
    }


    private $language_key;
    private $name;

    public function __construct(string $language_key, string $name)
    {
        $this->language_key = $language_key;
        $this->name = $name;
    }

    public function getLanguageKey(): string {
        return $this->language_key;
    }

    public function getName(): string {
        return $this->name;
    }

}

/**
 * Baut einen sprachenabhängigen String.
 * Dafür wird der {@link Localizer#getSharedLocaizer()} und die {@link Language#getCurrentLanguage()} verwendet.
 * @param string $key Der Identifikator des Strings.
 * @return string Der gefundene String oder ein Platzhalter, wenn kein String gefunden werden konnte.
 */
function l_str(string $key, array $params = array()): string {
    $s =  Localizer::getSharedLocalizer()->getString($key, Language::getCurrentLanguage());

    if ($s == null) {
        $s = "##" . strtoupper($key);
    }

    $i = 1;
    foreach ($params as $param) {
        $s = str_replace("%_" . $i . "%", $param, $s);

        $i++;
    }

    return $s;
}
