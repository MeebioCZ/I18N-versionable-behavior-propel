<?php

namespace Ypsylon\Propel\Behavior\I18NVersionable;

use Propel\Generator\Behavior\I18n\I18nBehavior;
use Propel\Generator\Behavior\I18n\I18nBehaviorObjectBuilderModifier;
use Propel\Generator\Behavior\Versionable\VersionableBehavior;
use Propel\Generator\Exception\EngineException;
use Propel\Generator\Model\Column;
use Propel\Generator\Model\ForeignKey;
use Propel\Generator\Model\PropelTypes;

class I18NVersionableBehavior extends I18nBehavior
{
    // default parameters value
    protected $parameters = [
        'i18n_table' => '%TABLE%_i18n',
        'i18n_phpname' => '%PHPNAME%I18n',
        'i18n_columns' => '',
        'i18n_pk_column' => null,
        'locale_column' => 'locale',
        'locale_length' => 5,
        'default_locale' => null,
        'locale_alias' => '',
        'versionable' => 'true',
        'log_created_at' => 'false',
        'log_created_by' => 'false',
        'log_comment' => 'false'
    ];

    protected function addLocaleColumnToI18n(): void
    {
        //parent::addLocaleColumnToI18n();
        $localeColumnName = $this->getLocaleColumnName();

        if (!$this->i18nTable->hasColumn($localeColumnName)) {
            $this->i18nTable->addColumn([
                'name' => $localeColumnName,
                'type' => PropelTypes::VARCHAR,
                'size' => $this->getParameter('locale_length') ? (int)$this->getParameter('locale_length') : 5,
                'default' => $this->getDefaultLocale(),
            ]);
        }
    }

    protected function relateI18nTableToMainTable(): void
    {
        $table = $this->getTable();
        $i18nTable = $this->i18nTable;

        // First add normal primary key
        if (!$i18nTable->hasPrimaryKey()) {
            $i18nTable->addColumn(['primaryKey' => true, 'name' => 'id', 'autoIncrement' => 'true', 'type' => 'INTEGER']);
        }

        $pks = $this->getTable()->getPrimaryKey();

        if (count($pks) > 1) {
            throw new EngineException('The i18n behavior does not support tables with composite primary keys');
        }

        // Now add foreign key column
        $column = $pks[0];
        $i18nColumn = clone $column;
        $i18nColumn->setPrimaryKey(false);
        $i18nColumn->setName($this->getForeignColumnName());
        $i18nColumn->setPhpName($this->getForeignColumnPhpName());

        if ($this->getParameter('i18n_pk_column')) {
            // custom i18n table pk name
            $i18nColumn->setName($this->getParameter('i18n_pk_column'));
        } else if (in_array($table->getName(), $i18nTable->getForeignTableNames())) {
            // custom i18n table pk name not set, but some fk already exists
            return;
        }

        if (!$i18nTable->hasColumn($i18nColumn->getName())) {
            $i18nColumn->setAutoIncrement(false);
            $i18nTable->addColumn($i18nColumn);
        }

        $fk = new ForeignKey();
        $fk->setForeignTableCommonName($table->getCommonName());
        $fk->setForeignSchemaName($table->getSchema());
        $fk->setDefaultJoin('LEFT JOIN');
        $fk->setOnDelete(ForeignKey::CASCADE);
        $fk->setOnUpdate(ForeignKey::NONE);
        $fk->addReference($i18nColumn->getName(), $column->getName());

        $i18nTable->addForeignKey($fk);
    }

    public function modifyTable(): void
    {
        parent::modifyTable();

        if ($this->getParameter('versionable')) {
            $versionBehavior = new VersionableBehavior();
            $versionBehavior->setName('versionable');

            $versionParams = $versionBehavior->getParameters();
            $versionParams['log_created_at'] = $this->getParameter('log_created_at');
            $versionParams['log_create_by'] = $this->getParameter('log_created_by');
            $versionParams['log_comment'] = $this->getParameter('log_comment');

            $versionBehavior->setParameters($versionParams);
            $this->i18nTable->addBehavior($versionBehavior);
        }
    }

    public function getForeignColumnName(): string
    {
        return $this->getTable()->getName() . '_id';
    }

    public function getForeignColumnPhpName(): string
    {
        return $this->getTable()->getPhpName() . 'Id';
    }

    public function getForeignColumnFilterName(): string
    {
        return 'filterBy' . $this->getForeignColumnPhpName();
    }

    public function getObjectBuilderModifier(): ?I18nBehaviorObjectBuilderModifier
    {
        if (null === $this->objectBuilderModifier) {
            $this->objectBuilderModifier = new I18NVersionableBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    protected function getAllTableColumnsPhpNames(): array
    {
        $names = [];
        foreach ($this->getTable()->getColumns() as $column) {
            $names[] = $column->getPhpName();
        }

        foreach ($this->getI18nColumns() as $column) {
            $names[] = $column->getPhpName();
        }

        return $names;
    }

    public function getAllTableColumnsGetters(): array
    {
        $result = [];
        $names = $this->getAllTableColumnsPhpNames();
        foreach ($names as $name) {
            $result[$name] = 'get' . $name;
        }

        return $result;
    }

    public function getAllTableColumnsSetters(): array
    {
        $result = [];
        $names = $this->getAllTableColumnsPhpNames();
        foreach ($names as $name) {
            $result[$name] = 'set' . $name;
        }

        return $result;
    }
}