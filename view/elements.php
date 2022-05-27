<?php
namespace de\langner_dev\ui\utils\document;

use function Sodium\add;

/**
 * Eine klassische Navigationsleiste.
 */
class NavBar extends HTMLElement {

    private $title;
    private $title_a;

    /**
     * Erstellt eine Navigationsleiste am oberen Bildschirmrand.
     * @param string $title Der Haupttitel der Navigationleiste.
     * @param string $title_a Der Link, der aufgerufen wird, wenn man auf den Titel klickt.
     */
    public function __construct(string $title, string $title_a = "index")
    {
        parent::__construct(
            array(
                "navbar",
                "navbar-expand-lg",
                "navbar-dark",
                "bg-dark",
                "fixed-top"
            )
        );
        $this->title = $title;
        $this->title_a = $title_a;
    }

    public function printStart() {
        $this->buildStartTag('nav');
        ?>
            <div class="container-fluid px-3">
                <a class="navbar-brand" href="<?php echo $this->title_a; ?>"><?php echo $this->title; ?></a>
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
                    aria-label="Navigationsleiste umschalten>">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="main-nav">
            <?php
    }

    public function printEnd() {
        ?>
                </div>

            </div>
        </nav>
        <?php
    }

} 

/**
 * Ein Navigationlisten-Element, welches verschiedene {@link NavBarListItem}s halten kann.
 */
class NavBarList extends HTMLElement {

    public function __construct() {
        parent::__construct(array(
            "navbar-nav",
            "me-auto",
            "mt-2",
            "mt-lg-0"
        ));
    }

    public function printStart() {
        $this->buildStartTag('ul');
    }

    public function printEnd() {
        ?>
        </ul>
        <?php
    }

}

/**
 * Ein Item der {@link NavBar}, welches zu einem bestimmten Teil der Seite verlinkt.
 */
class NavBarListItem extends HTMLElement {

    private $name;
    private $link;
    private $active;

    /**
     * Erstellt ein neues Item
     * @param string $name Der Name des Items.
     * @param string $link Der Link, der aufgerufen wird, wenn man auf das Item klickt.   
     */
    public function __construct($name = null, string $link = "#")
    {
        parent::__construct(
            array(
                "nav-item"
            )
        );
        $this->link = $link;
        $this->name = $name;
        $this->active = false;
    }

    public function printStart() {
        $active = ($this->active) ? " active" : "";
        
        $this->buildStartTag('li');
        ?>
            <a class="nav-link <?php echo $active; ?>" href="<?php echo $this->link; ?>">
        <?php

        if ($this->name != null) {
            echo $this->name;
        }
    }

    public function printEnd() {
        ?>
        </a>
        </li>
        <?php
    }

    public function setActive(bool $a){
        $this->active = $a;
    }

}

/**
 * Ein Item der {@link NavBar}, welches zu einem bestimmten Teil der Seite verlinkt.
 */
class NavBarDropDownTriggerListItem extends HTMLElement {

    private $name;
    private $link;
    private $active;

    /**
     * Erstellt ein neues Item
     * @param string $name Der Name des Items.
     * @param string $link Der Link, der aufgerufen wird, wenn man auf das Item klickt.
     */
    public function __construct($name = null, string $link = "#")
    {
        parent::__construct(
            array(
                "nav-item"
            )
        );
        $this->link = $link;
        $this->name = $name;
        $this->active = false;
    }

    public function printStart() {
        $active = ($this->active) ? " active" : "";

        $this->buildStartTag('li');
        ?>
            <a class="nav-link <?php echo $active; ?>" href="<?php echo $this->link; ?>">
        <?php

        if ($this->name != null) {
            echo $this->name;
        }
    }

    public function printEnd() {
        ?>
        </a>
        </li>
        <?php
    }

    public function setActive(bool $a){
        $this->active = $a;
    }

}

/**
 * Eine klassische Select-Box mit mehreren Optionen.
 */
class Select extends HTMLElement {

    private $values;
    private $use_display_value;

    /**
     * Erstellt ein neues Select-Objekt.
     * @param array $values Die Verschiedenen Optionen.
     * @param bool $use_display_values Wenn dies WAHR ist, werden die angezeigten Werte auch für das Formular verwendet. Wenn dies auf FALSCH ist, werden die keys verwendet.
     */
    public function __construct(array $values, $use_display_value = false)
    {
        parent::__construct(array(
            "form-select"
        ));

        $this->values = $values;
        $this->use_display_value;
    }

    public function printStart()
    {
        $this->buildStartTag('select');
        ?>
        <?php

        foreach (array_keys($this->values) as $key) {
            $value = $this->values[$key];

            $html_value = ($this->use_display_value) ? $value : $key;

            ?>
            <option value="<?php echo $html_value;?>"><?php echo $value; ?></option>
            <?php
        }
    }

    public function printEnd()
    {
        ?>
        </select>
        <?php
    }
}

/**
 * Ein klassischer Button.
 */
class Button extends HTMLElement {
    
    private $outline = false;
    private $type = BS5_BUTTON_TYPE_PRIMARY;
    private $content;

    public function __construct(string $content, string $type = BS5_BUTTON_TYPE_PRIMARY, bool $outline = false)
    {  
        parent::__construct(array(
            "btn"
        ), array(
            "type" => "button"
        ));

        $this->content = $content;
        $this->type = $type;
        $this->outline = $outline;

        $this->addClass($this->getButtonTypeClassName());
    }

    public function setContent(string $content) {
        $this->content = $content;
    }

    private function getButtonTypeClassName(): string {
        $c_name = "btn-";

        if ($this->outline) {
            $c_name .= "outline-";
        }

        $c_name .= $this->type;

        return $c_name;
    }

    public function printStart()
    {
        $this->buildStartTag('button');
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</button>";
    }

}

/**
 * Ein klassisches Div.
 */
class Div extends HTMLElement {

    private $content;

    public function __construct(string $content = "", array $classes = array(), array $attributes = array())
    {
        parent::__construct($classes, $attributes);
        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag('div');
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</div>";
    }

}

/**
 * Ein klassisches Span.
 */
class Span extends HTMLElement {

    private $content;

    public function __construct(string $content = "", array $classes = array(), array $attributes = array())
    {
        parent::__construct($classes, $attributes);
        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag('span');
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</span>";
    }

}

/**
 * Eine Klasse, die bestimmte Abstände hat.
 */
class Spacer extends HTMLElement {

    public function __construct(int $top = 0, int $right = 2, int $bottom = 0, int $left = 2) {
        
        parent::__construct(array(
            "ms-$left",
            "me-$right",
            "mt-$top",
            "mb-$bottom"
        ));

        if ($left == $right) {
            $this->removeClass("ms-$left");
            $this->removeClass("me-$left");

            $this->addClass("mx-$left");
        }

        if ($top == $bottom) {
            $this->removeClass("mt-$top");
            $this->removeClass("mb-$top");

            $this->addClass("my-$top");

            if ($left == $right && $left == $top) {
                $this->removeClass("mx-$top");
                $this->removeClass("my-$top");

                $this->addClass("m-$top");
            }
        }
    }

    public function printStart()
    {
        $this->buildStartTag('div');
    }

    public function printEnd()
    {
        echo '</div>';
    }

}

/**
 * Ein klassisches HTML-Element, welches den Hauptteil darstellen soll.
 */
class Section extends Div {

    private $type;

    public function __construct(string $title, string $htmlTitleTag = "h1", string $type = "container-fluid")
    {
        parent::__construct("<$htmlTitleTag>$title</$htmlTitleTag>", array(
            "bg-light",
            $type
        ));

        $this->type = $type;
    }

}

/**
 * Standard HTML-Img-Element.
 */
class Img extends HTMLElement {

    private $src;
    private $alt;

    public function __construct(string $src, string $alt)
    {
        $this->src = $src;
        $this->alt = $alt;
    }

    public function printStart()
    {
        $this->buildStartTag('img');
    }

    public function printEnd()
    {
        // IMG hat kein End-Tag.
    }


}

/**
 * Eine Karte, die sich vom Rest des Dokuments abhebt, um Informationen zu bündeln.
 */
class Card extends HTMLElement {

    private $image;

    public function __construct( bool $small = true)
    {
        parent::__construct(
            array("card")
        );

        if ($small) {
            $this->setStyle("width: 18rem;");
        }
    }

    public function setImage(Img $image) {
        $image->addClass("card-img-top");
        $this->image = $image;
    }

    public function printStart()
    {
        $this->buildStartTag('div');

        if ($this->image != null) {
            $this->image->printHTMLText();
        }

        
    }

    public function printEnd()
    {
        echo "</div>";
    }

}

/**
 * Der Textkörper für eine {@link Card}.
 */
class CardBody extends HTMLElement {
    private $title;
    private $subtitle;
    private $text;

    public function __construct(string $title, string $text, $subtitle = null)
    {
        parent::__construct(array("card-body"));

        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->text = $text;
    }

    public function printStart()
    {
        $this->buildStartTag('div');
        echo "<h5 class=\"card-title\">$this->title</h5>";

        if ($this->subtitle != null) {
            echo "<h6 class=\"card-subtitle mb-2 text-muted\">$this->subtitle</h6>";
        }

        echo "<p class=\"card-text\">$this->text</p>";

    }

    public function printEnd()
    {
        echo "</div>";
    }
}

/**
 * Eine Liste für eine {@link Card}.
 */
class CardList extends HTMLElement {

    public function __construct()
    {
        parent::__construct(array("list-group", "list-group-flush"));
    }

    public function printStart()
    {
        $this->buildStartTag('ul');
    }

    public function printEnd()
    {
        echo "</ul>";
    }

}

/**
 * Ein Listenitem für eine {@link CardList}.
 */
class CardListItem extends HTMLElement {

    private $content = "";

    public function __construct(string $content = "")
    {
        parent::__construct(array("list-group-item"));

        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag('li');
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</li>";
    }

}

/**
 * Ein spezielles {@link CardListItem}, welches Informationen visualisieren soll.
 */
class InformationCardListItem extends CardListItem {

    public function __construct(string $title, string $value)
    {
        parent::__construct("");

        $this->addElement(new Span($title, array("fw-bolder"), array("style" => "width: 60%; overflow: hidden; white-space: nowrap; display: inline-block;")));
        $this->addElement(new Span($value, array("text-end", "text-muted"), array("style" => "float: right; width: calc(40% - 5px); overflow: hidden; white-space: nowrap;")));
    }

}

/**
 * Ein Responsives HTML-Element, in dem sich bestimmte {@link GridItem}s in {@link GridRow}s nebeneinander anpassen können.
 */
class Grid extends HTMLElement {

    public function __construct(string $container_type = "conainer-fluid") {
        parent::__construct(array($container_type));
    }

    public function printStart()
    {
        $this->buildStartTag('div');
    }

    public function printEnd()
    {
        echo "</div>";
    }
}

/**
 * Eine Zeile im {@link Grid}. Wenn der Bildschirm zu klein ist, wird sie automatisch angepasst.
 */
class GridRow extends HTMLElement {

    public function __construct()
    {
        parent::__construct(array("row"));
    }

    public function printStart()
    {
        $this->buildStartTag('div');
    }

    public function printEnd()
    {
        echo "</div>";
    }
}

/**
 * Ein Item des {@link Grid}s. Muss einer {@link GridRow} zugeordnet werden.
 */
class GridItem extends HTMLElement {

    private $content;

    public function __construct(string $content = "", string $col_type = "col-md")
    {
        parent::__construct(array($col_type));

        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag('div');
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</div>";
    }
}

/**
 * Eine Tabelle.
 */
class Table extends HTMLElement {

    public function __construct($classes = array(), $attributes = array(), bool $striped = true)
    {
        parent::__construct($classes, $attributes);
        $this->addClass("table");

        if ($striped)
            $this->addClass("table-striped");
    }

    public function printStart()
    {
        $this->buildStartTag('table');
    }

    public function printEnd()
    {
        echo '</table>';
    }
}

/**
 * Die Kopfzeile einer {@link Table}.
 */
class TableHead extends HTMLElement {

    public function __construct()
    {
        parent::__construct(array(), array());
    }

    public function printStart()
    {
        $this->buildStartTag("thead");
    }

    public function printEnd()
    {
        echo "</thead>";
    }
}

/**
 *  Der Körper einer {@link Table}.
 */
class TableBody extends HTMLElement
{

    public function __construct()
    {
        parent::__construct(array(), array());
    }

    public function printStart()
    {
        $this->buildStartTag("tbody");
    }

    public function printEnd()
    {
        echo "</tbody>";
    }
}

/**
 * Eine Zeile in einer {@link Table}.
 */
class TableRow extends HTMLElement {

    public function __construct()
    {
        parent::__construct(array(), array());
    }

    public function printStart()
    {
        $this->buildStartTag("tr");
    }

    public function printEnd()
    {
        echo "</tr>";
    }

    public function addTH(string $content, $scope = "col") {
        $this->addElement(new TableHeadItem($content, $scope));
    }

    public function addTD(string $content) {
        $this->addElement(new TableBodyItem($content, array()));
    }

}

/**
 * Ein Item für {@link TableHead}.
 */
class TableHeadItem extends HTMLElement {

    private $content;

    public function __construct(string $content = "", $scope = "col")
    {
        $arr = array();

        if ($scope != null)
            $arr['scope'] = $scope;

        parent::__construct(array(), $arr);
        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag("th");
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</th>";
    }
}

/**
 * Ein Item für {@link TableBody}.
 */
class TableBodyItem extends HTMLElement {

    private $content;

    public function __construct(string $content = "", $attributes = array())
    {
        parent::__construct(array(), $attributes);
        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag("td");
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</td>";
    }
}

/**
 * Ein klassischer HTML-Link.
 */
class Link extends HTMLElement {

    private $content;

    public function __construct($content_or_element, $href)
    {
        if (is_string($content_or_element))
            $this->content = $content_or_element;

        if ($content_or_element instanceof HTMLElement) {
            $this->addElement($content_or_element);
        }

        parent::__construct(array(), array("href" => $href));
    }

    public function printStart()
    {
        $this->buildStartTag("a");

        if ($this->content != null)
            echo $this->content;
    }

    public function printEnd()
    {
        echo "</a>";
    }
}

/**
 * Eine klassische HTML-Form.
 */
class Form extends HTMLElement {

    public function __construct(string $action, string $method = FORM_METHOD_POST)
    {
        parent::__construct(array("bg-body", "border", "rounded", "p-3"), array("action" => $action, "method" => $method));
    }

    public function printStart()
    {
        $this->buildStartTag("form");
    }

    public function printEnd()
    {
        echo "</form>";
    }
}

/**
 * Ein Wrapper-Item eines {@link Form}s.
 */
class FormDivItem extends Div {

    public function __construct()
    {
        parent::__construct("", array("mb-3"), array());
    }
}

/**
 * Ein klassisches Label.
 */
class Label extends HTMLElement {

    private $content;

    public function __construct(string $content = "", $for = null)
    {
        $arr = array();

        if ($for != null) {
            $arr['for'] = $for;
        }

        parent::__construct(array(), $arr);
        $this->content = $content;
    }

    public function printStart()
    {
        $this->buildStartTag("label");
        echo $this->content;
    }

    public function printEnd()
    {
        echo "</label>";
    }
}

/**
 * Ein {@link Label} für ein {@link FormDivItem}.
 */
class FormLabel extends Label {


    public function __construct(string $content = "", $for = null)
    {
        parent::__construct($content, $for);
        $this->addClass("form-label ms-1");
    }

    public function col_form_label(): HTMLElement {

        $this->addClass("col-form-label");

        return $this;
    }
}

/**
 * Ein Beschreibungstext für ein {@link FormDivItem}.
 */
class FormText extends Div {

    public function __construct(string $content = "", array $classes = array(), array $attributes = array())
    {
        array_push($classes, "form-text", "ms-1");
        parent::__construct($content, $classes, $attributes);
    }

}

/**
 * Ein klassisches HTML-Input-Element.
 */
class Input extends HTMLElement {

    public function __construct(string $name,  $id = null, string $placeholder = "", string $type = "text", $value = null, array $attributes = array())
    {
        $attributes["name"] = $name;
        $attributes["type"] = $type;
        $attributes["placeholder"] = $placeholder;

        if ($value != null) {
            $attributes['value'] = $value;
        }
        else if (isset($attributes['value'])){
            unset($attributes['value']);
        }

        parent::__construct(array("form-control"), $attributes);

        if (isset($id)) {
            $this->setId($id);
        }
    }

    public function printStart()
    {
        $this->buildStartTag("input");
    }

    public function printEnd()
    {

    }

    public function required(): Input {
        $this->setAttribute('required', null);

        return $this;
    }

    public function value($string = null): Input {
        if (isset($string)) {
            $this->setAttribute("value", $string);
        }
        else if (isset($attributes['value'])){
            unset($attributes['value']);
        }

        return $this;
    }

    public function max_length($lenght = null): Input {
        $this->setAttribute("maxlength", $lenght);

        return $this;
    }

    public function placeholder(?string $p): Input {
        $this->setAttribute("placeholder", $p);

        return $this;
    }
}

/**
 * Ein klassisches HTML-Input-Element, in welches man Text eingeben kann.
 */
class TextInput extends Input {

    public function __construct(string $name, $id = null, string $placeholder = "", $value = null)
    {
        parent::__construct($name, $id, $placeholder, "text", $value, array());
    }
}

/**
 * Ein klassisches HTML-Input-Element, in welches man Zahlen eingeben kann.
 */
class NumberInput extends Input {

    public function __construct(string $name, $min = null, $max = null, $step = null, $id = null, string $placeholder = "", $value = null)
    {
        $attr = array();

        if (isset($min))
            $attr['min'] = $min;

        if (isset($max))
            $attr['max'] = $max;

        if (isset($step))
            $attr['step'] = $step;

        parent::__construct($name, $id, $placeholder, "number", $value, $attr);
    }
}


/**
 * Ein klassisches HTML-Input-Element, in welches man Zahlen eingeben kann.
 */
class HiddenInput extends Input {

    public function __construct(string $name, string $value, $id = null)
    {
        parent::__construct($name, $id, "", "hidden", $value, array());
    }
}

/**
 * Ein {@link Button} zum Absenden von Formularen.
 */
class SubmitButton extends Button {

    public function __construct(string $content, string $type = BS5_BUTTON_TYPE_PRIMARY, bool $outline = false)
    {
        parent::__construct($content, $type, $outline);

        $this->setAttribute("type", "submit");
    }

}

/**
 * Ein Fenster, welches eine Information herausheben soll.
 */
class Alert extends HTMLElement {

    public function __construct($classes = array(), $attributes = array())
    {
        array_push($classes, "alert", "my-4");
        $attributes['role'] = "alert";
        parent::__construct($classes, $attributes);
    }

    public function printStart()
    {
        $this->buildStartTag("div");
    }

    public function printEnd()
    {
        echo "</div>";
    }
}

/**
 * Ein {@link Alert} für Bestärigungen.
 */
class SuccessAlert extends Alert {

    private $content;

    public function __construct($content = "")
    {
        $this->content = '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg><div>' . $content . "</div>";
        parent::__construct(array("alert-success d-flex align-items-center"), array());
    }

    public function printStart()
    {
        parent::printStart();
        echo $this->content;
    }
}

/**
 * Ein {@link Alert} für Fehler.
 */
class ErrorAlert extends Alert {

    private $content;

    public function __construct($content = "")
    {
        $this->content = '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg><div>' . $content . "</div>";
        parent::__construct(array("alert-danger d-flex align-items-center"), array());
    }

    public function printStart()
    {
        parent::printStart();
        echo $this->content;
    }

}

/**
 * Ein {@link Alert} für Warnungen.
 */
class WarningAlert extends Alert {

    private $content;

    public function __construct($content = "")
    {
        $this->content = '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg><div>' . $content . "</div>";
        parent::__construct(array("alert-warning d-flex align-items-center"), array());
    }

    public function printStart()
    {
        parent::printStart();
        echo $this->content;
    }

}

define("BS5_BUTTON_TYPE_PRIMARY", "primary");
define("BS5_BUTTON_TYPE_SECONDARY", "secondary");
define("BS5_BUTTON_TYPE_SUCCESS", "success");
define("BS5_BUTTON_TYPE_DANGER", "danger");
define("BS5_BUTTON_TYPE_WARNING", "warning");
define("BS5_BUTTON_TYPE_INFO", "info");
define("BS5_BUTTON_TYPE_LIGHT", "light");
define("BS5_BUTTON_TYPE_DARK", "dark");
define("BS5_BUTTON_TYPE_LINK", "link");

define("FORM_METHOD_GET", "get");
define("FORM_METHOD_POST", "post");
