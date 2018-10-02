<?php

namespace Ypsylon\Propel\Behavior\I18NVersionable;

use Propel\Generator\Behavior\I18n\I18nBehaviorObjectBuilderModifier;
use Propel\Generator\Model\Column;
use Propel\Generator\Model\PropelTypes;

class I18NVersionableBehaviorObjectBuilderModifier extends I18nBehaviorObjectBuilderModifier
{
    protected function addGetTranslation()
    {
        $plural = false;
        $i18nTable = $this->behavior->getI18nTable();
        $fk = $this->behavior->getI18nForeignKey();

        return $this->behavior->renderTemplate('objectGetTranslation', [
            'i18nTablePhpName' => $this->builder->getClassNameFromBuilder($this->builder->getNewStubObjectBuilder($i18nTable)),
            'defaultLocale'    => $this->behavior->getDefaultLocale(),
            'i18nListVariable' => $this->builder->getRefFKCollVarName($fk),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
            'i18nQueryName'    => $this->builder->getClassNameFromBuilder($this->builder->getNewStubQueryBuilder($i18nTable)),
            'i18nSetterMethod' => $this->builder->getRefFKPhpNameAffix($fk, $plural),
            'foreignKeyFilterMethod' => $this->behavior->getForeignColumnFilterName(),
        ]);
    }
}
