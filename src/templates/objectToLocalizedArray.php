/**
* Fill object from localized array
*
* @return array
*/
public function toLocalizedArray()
{
    $result = [];
<?php
foreach ($getterMethods as $key => $method) {
    $line = "\$result['$key'] = \$this->$method(); \n";
    echo $line;
    echo "\n";
}
?>
    return $result;
}