/**
* Fill object from localized array
*
*/
public function fromLocalizedArray($array)
{
    <?php
        foreach ($setterMethods as $key => $method) {
            $line = "   
            if (isset(\$array['$key'])) {
                \$this->$method(\$array['$key']);
            }";
            echo $line;
        }
    ?>
}
