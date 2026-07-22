<?php

namespace Ypsylon\Propel\Behavior\I18NVersionable;

use Propel\Generator\Behavior\I18n\I18nBehaviorQueryBuilderModifier;

class I18NVersionableBehaviorQueryBuilderModifier extends I18nBehaviorQueryBuilderModifier
{
    /**
     * @return string
     */
    protected function addJoinI18n(): string
    {
        $fk = $this->behavior->getI18nForeignKey();

        return $this->behavior->renderTemplate('queryJoinI18n', [
            'queryClass' => $this->builder->getQueryClassName(),
            'defaultLocale' => $this->behavior->getDefaultLocale(),
            'i18nRelationName' => $this->builder->getRefFKPhpNameAffix($fk),
            'localeColumn' => $this->behavior->getLocaleColumn()->getPhpName(),
        ], __DIR__ . '/templates/');
    }

    /**
     * @return string
     */
    protected function addJoinWithI18n(): string
    {
        $fk = $this->behavior->getI18nForeignKey();

        return $this->behavior->renderTemplate('queryJoinWithI18n', [
            'queryClass' => $this->builder->getQueryClassName(),
            'defaultLocale' => $this->behavior->getDefaultLocale(),
            'i18nRelationName' => $this->builder->getRefFKPhpNameAffix($fk),
        ], __DIR__ . '/templates/');
    }

    /**
     * @return string
     */
    protected function addUseI18nQuery(): string
    {
        $i18nTable = $this->behavior->getI18nTable();
        $fk = $this->behavior->getI18nForeignKey();

        return $this->behavior->renderTemplate('queryUseI18nQuery', [
            'queryClass' => $this->builder->getClassNameFromBuilder($this->builder->getNewStubQueryBuilder($i18nTable)),
            'namespacedQueryClass' => $this->builder->getNewStubQueryBuilder($i18nTable)->getFullyQualifiedClassName(),
            'defaultLocale' => $this->behavior->getDefaultLocale(),
            'i18nRelationName' => $this->builder->getRefFKPhpNameAffix($fk),
            'localeColumn' => $this->behavior->getLocaleColumn()->getPhpName(),
        ], __DIR__ . '/templates/');
    }
}