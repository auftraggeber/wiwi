<?php
namespace de\langner_dev\ui\utils\document;

use Exception;
use function Sodium\add;

/**
 * Eine Klasse, die ein HTML-Element abbilden kann.
 */
abstract class HTMLElement {

    private $elements = array();
    private $attributes = array();
    private $classes = array();

    private $id, $style;

    public function __construct($classes = array(), $attributes = array())
    {
        $this->classes = $classes;
        $this->attributes = $attributes;
    }

    /**
     * Gibt nur die Starttags aus.
     */
    public abstract function printStart();
    /**
     * Gibt nur die End-Tags aus.
     */
    public abstract function printEnd();

    /**
     * Gibt den ganzen HTML-Text des Objekts aus. Dabei werden zwischen den
     * Start- und End-Tags auch die HTML-Texte der {@link #elements} ausgegeben.
     */
    public function printHTMLText() {
        $this->printStart();

        if (isset($this->elements) && !empty($this->elements)) {
            foreach ($this->elements as $e) {
                if (isset($e) && $e instanceof HTMLElement) {
                    $e->printHTMLText();
                }
            }
        }


        $this->printEnd();
    }

    /**
     * Fügt dem Element ein neues Kind hinzu.
     */
    public function addElement(HTMLElement $element) {
        array_push($this->elements, $element);
    }

    /**
     * @return array Das Array mit allen Kinderelementen.
     */
    public function getElements(): array {
        return $this->elements;
    }

    public function setId($id) {
        $this->setAttribute("id", $id);
    }

    public function setStyle($style) {
        $this->setAttribute("style", $style);
    }

    public function setAttribute($key, $value) {
        if ($key == "class") {
            throw new Exception("Cannot set the class attribute use HTMLElement#addClass instead.");
        }

        $this->attributes[$key] = $value;
    }

    public function addClass($class_name) {
        array_push($this->classes, $class_name);
    }

    public function removeClass($class_name) {
        foreach (array_keys($this->classes) as $key) {
            if ($this->classes[$key] == $class_name) {
                unset($this->classes[$key]);
            }
        }
    }

    public function buildStartTag($tag_name) {
        echo "<$tag_name " . $this->getClassHTMLString() . " " . $this->getAttributeHTMLString() . ">";
    }
    
    private function getClassHTMLString(): string {
        $s = "";

        foreach ($this->classes as $c) {
            if ($s != "") {
                $s .= " ";
            }

            $s .= $c;
        }

        if ($s != "") {
            $s = "class=\"$s\"";
        }

        return $s;
    }

    private function getAttributeHTMLString(): string {
        $s = "";

        foreach (array_keys($this->attributes) as $key) {
            $value = $this->attributes[$key];

            if ($s != "") {
                $s .= " ";
            }

            if (isset($value)) {
                $s .= "$key=\"$value\"";
            }
            else {
                $s .= $key;
            }

        }

        return $s;
    }

    public function getId() {
        return $this->attributes['id'];
    }

    public function m_0(): HTMLElement {
        $this->addClass("m-0");

        return $this;
    }

    public function m_1(): HTMLElement {
        $this->addClass("m-1");

        return $this;
    }

    public function m_2(): HTMLElement {
        $this->addClass("m-2");

        return $this;
    }

    public function m_3(): HTMLElement {
        $this->addClass("m-3");

        return $this;
    }

    public function m_4(): HTMLElement {
        $this->addClass("m-4");

        return $this;
    }

    public function m_5(): HTMLElement {
        $this->addClass("m-5");

        return $this;
    }

    public function mx_0(): HTMLElement {
        $this->addClass("mx-0");

        return $this;
    }

    public function mx_1(): HTMLElement {
        $this->addClass("mx-1");

        return $this;
    }

    public function mx_2(): HTMLElement {
        $this->addClass("mx-2");

        return $this;
    }

    public function mx_3(): HTMLElement {
        $this->addClass("mx-3");

        return $this;
    }

    public function mx_4(): HTMLElement {
        $this->addClass("mx-4");

        return $this;
    }

    public function mx_5(): HTMLElement {
        $this->addClass("mx-5");

        return $this;
    }

    public function my_0(): HTMLElement {
        $this->addClass("my-0");

        return $this;
    }

    public function my_1(): HTMLElement {
        $this->addClass("my-1");

        return $this;
    }

    public function my_2(): HTMLElement {
        $this->addClass("my-2");

        return $this;
    }

    public function my_3(): HTMLElement {
        $this->addClass("my-3");

        return $this;
    }

    public function my_4(): HTMLElement {
        $this->addClass("my-4");

        return $this;
    }

    public function my_5(): HTMLElement {
        $this->addClass("my-5");

        return $this;
    }

    public function ms_0(): HTMLElement {
        $this->addClass("ms-0");

        return $this;
    }

    public function ms_1(): HTMLElement {
        $this->addClass("ms-1");

        return $this;
    }

    public function ms_2(): HTMLElement {
        $this->addClass("ms-2");

        return $this;
    }

    public function ms_3(): HTMLElement {
        $this->addClass("ms-3");

        return $this;
    }

    public function ms_4(): HTMLElement {
        $this->addClass("ms-4");

        return $this;
    }

    public function ms_5(): HTMLElement {
        $this->addClass("ms-5");

        return $this;
    }

    public function mt_0(): HTMLElement {
        $this->addClass("mt-0");

        return $this;
    }

    public function mt_1(): HTMLElement {
        $this->addClass("mt-1");

        return $this;
    }

    public function mt_2(): HTMLElement {
        $this->addClass("mt-2");

        return $this;
    }

    public function mt_3(): HTMLElement {
        $this->addClass("mt-3");

        return $this;
    }

    public function mt_4(): HTMLElement {
        $this->addClass("mt-4");

        return $this;
    }

    public function mt_5(): HTMLElement {
        $this->addClass("mt-5");

        return $this;
    }

    public function me_0(): HTMLElement {
        $this->addClass("me-0");

        return $this;
    }

    public function me_1(): HTMLElement {
        $this->addClass("me-1");

        return $this;
    }

    public function me_2(): HTMLElement {
        $this->addClass("me-2");

        return $this;
    }

    public function me_3(): HTMLElement {
        $this->addClass("me-3");

        return $this;
    }

    public function me_4(): HTMLElement {
        $this->addClass("me-4");

        return $this;
    }

    public function me_5(): HTMLElement {
        $this->addClass("me-5");

        return $this;
    }

    public function mb_0(): HTMLElement {
        $this->addClass("mb-0");

        return $this;
    }

    public function mb_1(): HTMLElement {
        $this->addClass("mb-1");

        return $this;
    }

    public function mb_2(): HTMLElement {
        $this->addClass("mb-2");

        return $this;
    }

    public function mb_3(): HTMLElement {
        $this->addClass("mb-3");

        return $this;
    }

    public function mb_4(): HTMLElement {
        $this->addClass("mb-4");

        return $this;
    }

    public function mb_5(): HTMLElement {
        $this->addClass("mb-5");

        return $this;
    }

    public function p_0(): HTMLElement {
        $this->addClass("p-0");

        return $this;
    }

    public function p_1(): HTMLElement {
        $this->addClass("p-1");

        return $this;
    }

    public function p_2(): HTMLElement {
        $this->addClass("p-2");

        return $this;
    }

    public function p_3(): HTMLElement {
        $this->addClass("p-3");

        return $this;
    }

    public function p_4(): HTMLElement {
        $this->addClass("p-4");

        return $this;
    }

    public function p_5(): HTMLElement {
        $this->addClass("p-5");

        return $this;
    }

    public function px_0(): HTMLElement {
        $this->addClass("px-0");

        return $this;
    }

    public function px_1(): HTMLElement {
        $this->addClass("px-1");

        return $this;
    }

    public function px_2(): HTMLElement {
        $this->addClass("px-2");

        return $this;
    }

    public function px_3(): HTMLElement {
        $this->addClass("px-3");

        return $this;
    }

    public function px_4(): HTMLElement {
        $this->addClass("px-4");

        return $this;
    }

    public function px_5(): HTMLElement {
        $this->addClass("px-5");

        return $this;
    }

    public function py_0(): HTMLElement {
        $this->addClass("py-0");

        return $this;
    }

    public function py_1(): HTMLElement {
        $this->addClass("py-1");

        return $this;
    }

    public function py_2(): HTMLElement {
        $this->addClass("py-2");

        return $this;
    }

    public function py_3(): HTMLElement {
        $this->addClass("py-3");

        return $this;
    }

    public function py_4(): HTMLElement {
        $this->addClass("py-4");

        return $this;
    }

    public function py_5(): HTMLElement {
        $this->addClass("py-5");

        return $this;
    }

    public function ps_0(): HTMLElement {
        $this->addClass("ps-0");

        return $this;
    }

    public function ps_1(): HTMLElement {
        $this->addClass("ps-1");

        return $this;
    }

    public function ps_2(): HTMLElement {
        $this->addClass("ps-2");

        return $this;
    }

    public function ps_3(): HTMLElement {
        $this->addClass("ps-3");

        return $this;
    }

    public function ps_4(): HTMLElement {
        $this->addClass("ps-4");

        return $this;
    }

    public function ps_5(): HTMLElement {
        $this->addClass("ps-5");

        return $this;
    }

    public function pt_0(): HTMLElement {
        $this->addClass("pt-0");

        return $this;
    }

    public function pt_1(): HTMLElement {
        $this->addClass("pt-1");

        return $this;
    }

    public function pt_2(): HTMLElement {
        $this->addClass("pt-2");

        return $this;
    }

    public function pt_3(): HTMLElement {
        $this->addClass("pt-3");

        return $this;
    }

    public function pt_4(): HTMLElement {
        $this->addClass("pt-4");

        return $this;
    }

    public function pt_5(): HTMLElement {
        $this->addClass("pt-5");

        return $this;
    }

    public function pe_0(): HTMLElement {
        $this->addClass("pe-0");

        return $this;
    }

    public function pe_1(): HTMLElement {
        $this->addClass("pe-1");

        return $this;
    }

    public function pe_2(): HTMLElement {
        $this->addClass("pe-2");

        return $this;
    }

    public function pe_3(): HTMLElement {
        $this->addClass("pe-3");

        return $this;
    }

    public function pe_4(): HTMLElement {
        $this->addClass("pe-4");

        return $this;
    }

    public function pe_5(): HTMLElement {
        $this->addClass("pe-5");

        return $this;
    }

    public function pb_0(): HTMLElement {
        $this->addClass("pb-0");

        return $this;
    }

    public function pb_1(): HTMLElement {
        $this->addClass("pb-1");

        return $this;
    }

    public function pb_2(): HTMLElement {
        $this->addClass("pb-2");

        return $this;
    }

    public function pb_3(): HTMLElement {
        $this->addClass("pb-3");

        return $this;
    }

    public function pb_4(): HTMLElement {
        $this->addClass("pb-4");

        return $this;
    }

    public function pb_5(): HTMLElement {
        $this->addClass("pb-5");

        return $this;
    }

    public function container(): HTMLElement {
        $this->addClass("container");

        return $this;
    }

    public function container_sm(): HTMLElement {
        $this->addClass("container-sm");

        return $this;
    }

    public function container_md(): HTMLElement {
        $this->addClass("container-md");

        return $this;
    }

    public function container_lg(): HTMLElement {
        $this->addClass("container-lg");

        return $this;
    }

    public function container_xl(): HTMLElement {
        $this->addClass("container-xl");

        return $this;
    }

    public function container_xxl(): HTMLElement {
        $this->addClass("container-xxl");

        return $this;
    }

    public function container_fluid(): HTMLElement {
        $this->addClass("container-fluid");

        return $this;
    }

    public function bg_primary(): HTMLElement {
        $this->addClass("bg-primary");

        return $this;
    }

    public function bg_secondary(): HTMLElement {
        $this->addClass("bg-secondary");

        return $this;
    }

    public function bg_success(): HTMLElement {
        $this->addClass("bg-success");

        return $this;
    }

    public function bg_danger(): HTMLElement {
        $this->addClass("bg-danger");

        return $this;
    }

    public function bg_warning(): HTMLElement {
        $this->addClass("bg-warning");

        return $this;
    }

    public function bg_info(): HTMLElement {
        $this->addClass("bg-info");

        return $this;
    }

    public function bg_light(): HTMLElement {
        $this->addClass("bg-light");

        return $this;
    }

    public function bg_dark(): HTMLElement {
        $this->addClass("bg-dark");

        return $this;
    }

    public function bg_body(): HTMLElement {
        $this->addClass("bg-body");

        return $this;
    }

    public function bg_white(): HTMLElement {
        $this->addClass("bg-white");

        return $this;
    }

    public function bg_transparent(): HTMLElement {
        $this->addClass("bg-transparent");

        return $this;
    }

    public function border(): HTMLElement {
        $this->addClass("border");

        return $this;
    }

    public function border_start(): HTMLElement {
        $this->addClass("border-start");

        return $this;
    }

    public function border_end(): HTMLElement {
        $this->addClass("border-end");

        return $this;
    }

    public function border_top(): HTMLElement {
        $this->addClass("border-top");

        return $this;
    }

    public function border_bottom(): HTMLElement {
        $this->addClass("border-bottom");

        return $this;
    }

    public function border_0(): HTMLElement {
        $this->addClass("border-0");

        return $this;
    }

    public function border_start_0(): HTMLElement {
        $this->addClass("border-start-0");

        return $this;
    }

    public function border_end_0(): HTMLElement {
        $this->addClass("border-end-0");

        return $this;
    }

    public function border_top_0(): HTMLElement {
        $this->addClass("border-top-0");

        return $this;
    }

    public function border_bottom_0(): HTMLElement {
        $this->addClass("border-bottom-0");

        return $this;
    }

    public function border_primary(): HTMLElement {
        $this->addClass("border border-primary");

        return $this;
    }

    public function border_secondary(): HTMLElement {
        $this->addClass("border border-secondary");

        return $this;
    }

    public function border_success(): HTMLElement {
        $this->addClass("border border-success");

        return $this;
    }

    public function border_danger(): HTMLElement {
        $this->addClass("border border-danger");

        return $this;
    }

    public function border_warning(): HTMLElement {
        $this->addClass("border border-warning");

        return $this;
    }

    public function border_info(): HTMLElement {
        $this->addClass("border border-info");

        return $this;
    }

    public function border_light(): HTMLElement {
        $this->addClass("border border-light");

        return $this;
    }

    public function border_dark(): HTMLElement {
        $this->addClass("border border-dark");

        return $this;
    }

    public function border_white(): HTMLElement {
        $this->addClass("border border-white");

        return $this;
    }

    public function border_1(): HTMLElement {
        $this->addClass("border border-1");

        return $this;
    }

    public function border_2(): HTMLElement {
        $this->addClass("border border-2");

        return $this;
    }

    public function border_3(): HTMLElement {
        $this->addClass("border border-3");

        return $this;
    }

    public function border_4(): HTMLElement {
        $this->addClass("border border-4");

        return $this;
    }

    public function border_5(): HTMLElement {
        $this->addClass("border border-5");

        return $this;
    }

    public function rounded(): HTMLElement {
        $this->addClass("rounded");

        return $this;
    }

    public function rounded_top(): HTMLElement {
        $this->addClass("rounded-top");

        return $this;
    }

    public function rounded_end(): HTMLElement {
        $this->addClass("rounded-end");

        return $this;
    }

    public function rounded_bottom(): HTMLElement {
        $this->addClass("rounded-bottom");

        return $this;
    }

    public function rounded_start(): HTMLElement {
        $this->addClass("rounded-start");

        return $this;
    }

    public function rounded_circle(): HTMLElement {
        $this->addClass("rounded-circle");

        return $this;
    }

    public function rounded_pill(): HTMLElement {
        $this->addClass("rounded-pill");

        return $this;
    }

    public function text_start(): HTMLElement {
        $this->addClass("text-start");

        return $this;
    }

    public function text_center(): HTMLElement {
        $this->addClass("text-center");

        return $this;
    }

    public function text_end(): HTMLElement {
        $this->addClass("text-end");

        return $this;
    }

    public function text_muted(): HTMLElement {
        $this->addClass("text-muted");

        return $this;
    }

    public function font_monospace(): HTMLElement {
        $this->addClass("font-monospace");

        return $this;
    }

    public function text_primary(): HTMLElement {
        $this->addClass("text-primary");

        return $this;
    }

    public function text_secondary(): HTMLElement {
        $this->addClass("text-secondary");

        return $this;
    }

    public function text_success(): HTMLElement {
        $this->addClass("text-success");

        return $this;
    }

    public function text_danger(): HTMLElement {
        $this->addClass("text-danger");

        return $this;
    }

    public function text_warning(): HTMLElement {
        $this->addClass("text-warning");

        return $this;
    }

    public function text_info(): HTMLElement {
        $this->addClass("text-info");

        return $this;
    }

    public function text_light(): HTMLElement {
        $this->addClass("text-light");

        return $this;
    }

    public function text_dark(): HTMLElement {
        $this->addClass("text-dark");

        return $this;
    }

    public function text_body(): HTMLElement {
        $this->addClass("text-body");

        return $this;
    }

    public function text_white(): HTMLElement {
        $this->addClass("text-white");

        return $this;
    }

    public function text_black_50(): HTMLElement {
        $this->addClass("text-black-50");

        return $this;
    }

    public function text_white_50(): HTMLElement {
        $this->addClass("text-white-50");

        return $this;
    }

    public function row(): HTMLElement {
        $this->addClass("row");

        return $this;
    }

    public function col($size = null): HTMLElement {
        if (!isset($size))
            $this->addClass("col");
        else
            $this->addClass("col-$size");

        return $this;
    }

    public function col_sm($size = null): HTMLElement {
        if (!isset($size))
            $this->addClass("col-sm");
        else
            $this->addClass("col-sm-$size");

        return $this;
    }

    public function col_md($size = null): HTMLElement {
        if (!isset($size))
            $this->addClass("col-md");
        else
            $this->addClass("col-md-$size");

        return $this;
    }

    public function col_lg($size = null): HTMLElement {
        if (!isset($size))
            $this->addClass("col-lg");
        else
            $this->addClass("col-lg-$size");

        return $this;
    }

    public function col_xl($size = null): HTMLElement {
        if (!isset($size))
            $this->addClass("col-xl");
        else
            $this->addClass("col-xl-$size");

        return $this;
    }

    public function col_xxl($size = null): HTMLElement {
        if (!isset($size))
            $this->addClass("col-xxl");
        else
            $this->addClass("col-xxl-$size");

        return $this;
    }
}

/**
 * Ein spezielles {@link HTMLElement}, welches das Dokument aufbaut.
 */
class HTMLDocument extends HTMLElement {

    private $title;
    private $icon;
    private $script;

    public function __construct($title, $icon) 
    {
        $this->title = $title;
        $this->icon = $icon;   
    }
    
    public function printStart() {
        ?>
        <!doctype html>
        <html lang="en">

        <head>
            <title><?php echo ($this->title != null) ? $this->title : "Unnamed"; ?></title>
            <!-- Required meta tags -->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <?php
            if ($this->icon != null) {
                ?>
                <meta rel="icon" href="<?php echo $this->icon; ?>">
                <?php
            }
            ?>

            <!-- Bootstrap CSS v5.0.2 -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
                integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

            <script type="text/javascript" src="js/chart.min.js"></script>

            <script type="text/javascript">

                function setUrl(title, url) {
                    window.history.replaceState(null, title, url);
                }

                <?php
                    if (isset($this->script))
                        echo $this->script;
                ?>

            </script>

        </head>

        <body style="padding-top: 60px; padding-left: 5px; padding-right: 5px;">
            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </symbol>
                <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </symbol>
                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </symbol>
            </svg>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
            <div class="wrapper bg-light container-fluid pb-2">
            
        <?php
    }

    public function printEnd() {
        ?>
        </div>
        </body>

        </html>
        <?php
    }

    public function setUrl($url) {
        if (!isset($this->script))
            $this->script = "";

        $this->script .= "setUrl(\"$this->title\", \"$url\");";
    }

}
?>