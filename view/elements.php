<?php
namespace de\langner_dev\ui\utils\document;

use de\langner_dev\wiwi\model\Machine;
use de\langner_dev\wiwi\model\Order;
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
    private $disabled_keys = array();
    private $selected_key = null;

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
        $this->use_display_value = $use_display_value;
    }

    public function printStart()
    {
        $this->buildStartTag('select');
        ?>
        <?php

        foreach (array_keys($this->values) as $key) {
            $value = $this->values[$key];

            $html_value = ($this->use_display_value) ? $value : $key;

            $disabled = (in_array($key, $this->disabled_keys)) ? " disabled" : "";

            $selected = (isset($this->selected_key) && $this->selected_key == $key) ? " selected" : "";

            ?>
            <option value="<?php echo $html_value;?>" <?php echo $disabled . $selected; ?>><?php echo $value; ?></option>
            <?php
        }
    }

    public function printEnd()
    {
        ?>
        </select>
        <?php
    }

    public function addDisabledKey($key): Select {
        array_push($this->disabled_keys, $key);

        return $this;
    }


    public function setSelectedKey($selected_key): void
    {
        $this->selected_key = $selected_key;
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

    public function __construct($content_or_element = "", $attributes = array())
    {
        parent::__construct(array(), $attributes);

        if ($content_or_element instanceof HTMLElement) {
            $this->addElement($content_or_element);
        }
        else $this->content = $content_or_element;
    }

    public function printStart()
    {
        $this->buildStartTag("td");
        echo $this->content != null ? $this->content : "";
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

    public function __construct($content_or_element, $href = null)
    {

        if ($content_or_element instanceof HTMLElement) {
            $this->addElement($content_or_element);
        }
        else
            $this->content = $content_or_element;

        $attr = array();

        if ($href != null)
            $attr['href'] = $href;
        else {
            $this->addClass("text-decoration-none");
        }


        parent::__construct(array(), $attr);
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

    public function __construct(string $name,  $id = null, ?string $placeholder = "", string $type = "text", $value = null, array $attributes = array())
    {
        $attributes["name"] = $name;
        $attributes["type"] = $type;

        if ($placeholder != null)
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
class DateInput extends Input {

    public function __construct(string $name, $id = null, $value = null)
    {
        $attr = array();

        parent::__construct($name, $id, null, "date", $value, $attr);
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

class Chart extends HTMLElement {

    public function __construct(string $id)
    {
        parent::__construct(array(), array());
        $this->setId($id);
    }

    public function printStart()
    {
        echo '<div class="chartCard"><div class="chartBox"><canvas id="' . $this->getId() . '"></canvas></div></div>';
    }

    public function printEnd()
    {

    }
}

class HorizontalScheduleBarChart extends Chart {

    private $data = array();
    private $labels = array();
    private $title = "";
    private $ticks = array();

    public function __construct(string $id)
    {
        parent::__construct($id);
    }

    public function addLabel(string $name) {
        if (in_array($name, $this->labels))
            return;

        array_push($this->labels, $name);
    }

    public function addDataset(string $name, array $data, int $count) {
        if (!isset($this->data[$name]))
            $this->data[$name] = array();

        $r_arr = array();

        while ($count > count($this->data[$name])) {
            array_push($this->data[$name] , array(0,0));
        }

        $this->data[$name] = array_merge($this->data[$name], $data);
    }

    public function addTick(string $tick) {
        array_push($this->ticks, $tick);
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getIndexOfLabel(string $label): int {
        return is_int(array_search($label, $this->labels)) ? array_search($label, $this->labels) : -1;
    }

    private function getData(): array {
        $array = array();

        foreach (array_keys($this->data) as $key) {

            $data_arr = $this->data[$key];

            array_push($array, array("label" => $key, "data" => $data_arr));
        }

        return $array;
    }

    public function printEnd()
    {
        parent::printEnd(); // TODO: Change the autogenerated stub

        ?>
        <script>
            // setup
            const ticks = JSON.parse('<?php echo json_encode($this->ticks); ?>')

            const data = {
                labels: JSON.parse('<?php echo json_encode(($this->labels)); ?>'),
                datasets: JSON.parse('<?php echo json_encode($this->getData()); ?>')
            };

            console.log(data.datasets);

            // config
            const config = {
                type: 'bar',
                data: data,
                options: {
                    indexAxis: 'y',
                    // Elements options apply to all of the options unless overridden in a dataset
                    // In this case, we are setting the border of each horizontal bar to be 2px wide
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: '<?php echo $this->title; ?>'
                        }

                    },
                    scales: {
                        xAxis: {
                            ticks: {
                                callback: function(value, index, t) {
                                    return ticks[parseInt(value)];
                                }
                            }
                        }
                    }
                },
            };

            // render init block
            new Chart(
                document.getElementById('<?php echo $this->getId();?>'),
                config
            );
        </script>
        <?php
    }
}


class StackedGroupBarChart extends Chart {

    private $data = array();
    private $labels = array();
    private $title = "";
    private $stacks = array();
    private $order_names = array();
    private $background_colors = array();
    private $background_color_index = 0;
    private static $AVAILABLE_BACKGROUND_COLORS =
        array(
            "rgb(255,0,0)",
            "rgb(0,255,0)",
            "rgb(0,0,255)"
        );

    public function __construct(string $id)
    {
        parent::__construct($id);
    }

    public function addLabel(string $name) {
        if (in_array($name, $this->labels))
            return;

        array_push($this->labels, $name);
    }

    public function addDataset(Order $order, Machine $machine, string $date, float $data) {
        $name = $machine->getName() . " - " . $order->getName();

        if (!isset($this->stacks[$name]))
            $this->stacks[$name] = "Machine #" . $machine->getId();

        if (!isset($this->order_names[$name]))
            $this->order_names[$name] = $order;

        $array = $this->data[$name] ?? array();

        $date = date("Y-m-d", strtotime($date));
        $array[$date] = $data;

        $this->data[$name] = $array;
    }


    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getIndexOfLabel(string $label): int {
        return is_int(array_search($label, $this->labels)) ? array_search($label, $this->labels) : -1;
    }

    private function getData(): array {
        $array = array();

        foreach (array_keys($this->data) as $key) {
            $data = array();
            $min = strtotime("2022-01-01") / (3600 * 24);
            $max = strtotime("2022-12-31") / (3600 * 24);
            $order_data = $this->data[$key];

            for ($i = $min; $i <= $max; $i++) {
                $time = $i * 3600 * 24;
                $date = date("Y-m-d", $time);

                $date_data = $order_data[$date] ?? null;
                array_push($data, $date_data);
            }

            array_push($array,
                array(
                    "label" => $key,
                    "data" => $data,
                    "stack" => $this->stacks[$key] ?? "undefined",
                    "backgroundColor" => $this->getBackgroundColor($key)
                )
            );
        }

        return $array;
    }

    private function getBackgroundColor(string $datasetname) : string {
        $order = $this->order_names[$datasetname];

        if ($order != null && $order->exists()) {
            if (!isset($this->background_colors[$order->getId()])) {
                $this->background_colors[$order->getId()] = self::$AVAILABLE_BACKGROUND_COLORS[$this->background_color_index++ % count(self::$AVAILABLE_BACKGROUND_COLORS)];
            }
            return $this->background_colors[$order->getId()];
        }

        return "gray";
    }

    public function printStart()
    {
        ?>
        <div class="form-group">
            <label for="chart-date-start">Start:</label>
            <input type="date" class="form-control" id="chart-date-start" onchange="reCalcChart()" min="2022-01-01" value="2022-01-01">
        </div>
        <div class="form-group">
            <label for="chart-date-end">Ende:</label>
            <input type="date" class="form-control" id="chart-date-end" max="2022-12-31" value="2022-12-31" onchange="reCalcChart()">
        </div>

        <?php
        parent::printStart(); // TODO: Change the autogenerated stub
    }

    public function printEnd()
    {
        parent::printEnd(); // TODO: Change the autogenerated stub

        $dates = array();

        $min = strtotime("2022-01-01") / (3600 * 24);
        $max = strtotime("2022-12-31") / (3600 * 24);

        $c = 0;

        for ($i = $min; $i <= $max; $i++) {
            $time = $i * 3600 * 24;

            $dates[date("Y-m-d", $time)] = $c;
            $c++;
        }

        ?>
        <script>
            const dateIndeces = JSON.parse('<?php echo json_encode($dates); ?>');
            const allLabels = JSON.parse('<?php echo json_encode(($this->labels)); ?>');
            const allDataSets = JSON.parse('<?php echo json_encode($this->getData()); ?>');
            console.log(allDataSets);
            let chart = null;

            function getSelectedIndex(inputId) {
                let input = document.getElementById(inputId);

                return dateIndeces[input.value];
            }

            function getFilteredLabels() {
                let startIndex = getSelectedIndex("chart-date-start");
                let endIndex = getSelectedIndex("chart-date-end");

                let dateIndecesLength = Object.keys(dateIndeces).length;

                if (startIndex == null)
                    startIndex = 0;
                if (endIndex == null) {
                    endIndex = dateIndecesLength - 1
                }

                let filtered = []

                for (let i = startIndex; i <= endIndex; i++) {
                    filtered.push(allLabels[i]);
                }

                return filtered;
            }
            
            function getFilteredDataSets() {
                let startIndex = getSelectedIndex("chart-date-start");
                let endIndex = getSelectedIndex("chart-date-end");

                let dateIndecesLength = Object.keys(dateIndeces).length;

                if (startIndex == null)
                    startIndex = 0;
                if (endIndex == null) {
                    endIndex = dateIndecesLength - 1
                }

                let dataSets = []
                
                for (let i = 0; i < allDataSets.length; i++) {
                    let arr = {}
                    let allArr = allDataSets[i]
                    let data = allDataSets[i]['data']
                    let keys = Object.keys(allArr)

                    for (let k = 0; k < keys.length; k++) {
                        let key = keys[k]
                        if (key !== 'data') {
                            arr[key] = allArr[key]
                        }
                    }

                    let arrData = []

                    for (let j = startIndex; j <= endIndex; j++) {
                        arrData.push(data[j]);
                    }


                    arr['data'] = arrData
                    dataSets.push(arr)
                    console.log(arr)
                }

                return dataSets
            }

            function reCalcChart() {
                if (chart != null)
                    chart.destroy();


                // setup
                const data = {
                    labels: getFilteredLabels(),
                    datasets: getFilteredDataSets()

                };

                // config
                const config = {
                    type: 'bar',
                    data: data,
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'Chart.js Bar Chart - Stacked'
                            },
                        },
                        responsive: true,
                        interaction: {
                            intersect: true,
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    },
                };

                chart = new Chart(
                    document.getElementById('<?php echo $this->getId();?>'),
                    config
                );
            }

            reCalcChart()

        </script>
        <?php
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
