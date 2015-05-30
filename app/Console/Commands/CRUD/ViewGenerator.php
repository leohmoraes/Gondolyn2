<?php namespace App\Console\Commands\CRUD;

use Config;
use Illuminate\Support\Str;
use Mitul\Generator\CommandData;
use Mitul\Generator\Generators\GeneratorProvider;
use Mitul\Generator\Generators\Scaffold\ViewGenerator;

class ResponsiveViewGenerator extends ViewGenerator
{
    /** @var  CommandData */
    private $commandData;

    private $path;

    private $viewsPath;

    function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_views', base_path('resources/views')) . '/' . $this->commandData->modelNamePluralCamel . '/';
        $this->viewsPath = "Scaffold/Views";
    }

    public function generate()
    {
        if(!file_exists($this->path))
            mkdir($this->path, 0755, true);

        $this->commandData->commandObj->comment("\nViews created: ");
        $this->generateFields();
        $this->generateIndex();
        $this->generateShow();
        $this->generateCreate();
        $this->generateEdit();
    }

    private function generateFields()
    {
        $fieldTemplate = $this->commandData->templatesHelper->getTemplate("field.blade", $this->viewsPath);

        $fieldsStr = "";

        foreach($this->commandData->inputFields as $field)
        {
            $singleFieldStr = str_replace('$FIELD_NAME_TITLE$', Str::title(str_replace("_", " ", $field['fieldName'])), $fieldTemplate);
            $singleFieldStr = str_replace('$FIELD_NAME$', $field['fieldName'], $singleFieldStr);
            $fieldsStr .= $singleFieldStr . "\n\n";
        }

        $templateData = $this->commandData->templatesHelper->getTemplate("fields.blade", $this->viewsPath);

        $templateData = str_replace('$FIELDS$', $fieldsStr, $templateData);

        $fileName = "fields.blade.php";

        $path = $this->path . $fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->info("field.blade.php created");
    }

    private function generateShow()
    {
        $fieldTemplate = $this->commandData->templatesHelper->getTemplate("show.blade", $this->viewsPath);

        $fileName = "show.blade.php";

        $path = $this->path . $fileName;

        $this->commandData->fileHelper->writeFile($path, $fieldTemplate);
        $this->commandData->commandObj->info("show.blade.php created");
    }

    private function generateCreate()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate("create.blade", $this->viewsPath);

        $templateData = $this->fillTemplate($templateData);

        $fileName = "create.blade.php";

        $path = $this->path . $fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->info("create.blade.php created");
    }

    private function generateEdit()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate("edit.blade", $this->viewsPath);

        $templateData = $this->fillTemplate($templateData);

        $fileName = "edit.blade.php";

        $path = $this->path . $fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->info("edit.blade.php created");
    }

    private function fillTemplate($templateData)
    {
        $templateData = str_replace('$MODEL_NAME$', $this->commandData->modelName, $templateData);
        $templateData = str_replace('$MODEL_NAME_PLURAL$', $this->commandData->modelNamePlural, $templateData);

        $templateData = str_replace('$MODEL_NAME_CAMEL$', $this->commandData->modelNameCamel, $templateData);
        $templateData = str_replace('$MODEL_NAME_PLURAL_CAMEL$', $this->commandData->modelNamePluralCamel, $templateData);

        return $templateData;
    }

    private function generateIndex()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate("index.blade", $this->viewsPath);

        if($this->commandData->useSearch)
        {
            $searchLayout = $this->commandData->templatesHelper->getTemplate("search.blade", $this->viewsPath);
            $templateData = str_replace('$SEARCH$', $searchLayout, $templateData);

            $fieldTemplate = $this->commandData->templatesHelper->getTemplate("field.blade", $this->viewsPath);

            $fieldsStr = "";

            foreach($this->commandData->inputFields as $field)
            {
                $singleFieldStr = str_replace('$FIELD_NAME_TITLE$', Str::title(str_replace("_", " ", $field['fieldName'])), $fieldTemplate);
                $singleFieldStr = str_replace('$FIELD_NAME$', $field['fieldName'], $singleFieldStr);
                $fieldsStr .= "\n\n" . $singleFieldStr . "\n\n";
            }

            $templateData = str_replace('$FIELDS$', $fieldsStr, $templateData);
        }
        else
        {
            $templateData = str_replace('$SEARCH$', '', $templateData);
        }

        $templateData = $this->fillTemplate($templateData);

        $fileName = "index.blade.php";

        $headerFields = "";
        $n = 0;

        foreach($this->commandData->inputFields as $field)
        {
            if ($n === 0) {
                $headerFields .= "<th>" . Str::title(str_replace("_", " ", $field['fieldName'])) . "</th>\n\t\t\t";
            } else {
                $headerFields .= "<th class=\"raw-m-hide\">" . Str::title(str_replace("_", " ", $field['fieldName'])) . "</th>\n\t\t\t";
            }
            $n++;
        }

        $headerFields = trim($headerFields);

        $templateData = str_replace('$FIELD_HEADERS$', $headerFields, $templateData);

        $tableBodyFields = "";

        $i = 0;
        foreach($this->commandData->inputFields as $field)
        {
            if ($i === 0) {
                $tableBodyFields .= "<td>{!! $" . $this->commandData->modelNameCamel . "->" . $field['fieldName'] . " !!}</td>\n\t\t\t\t\t";
            } else {
                $tableBodyFields .= "<td class=\"raw-m-hide\">{!! $" . $this->commandData->modelNameCamel . "->" . $field['fieldName'] . " !!}</td>\n\t\t\t\t\t";
            }
            $i++;
        }

        $tableBodyFields = trim($tableBodyFields);

        $templateData = str_replace('$FIELD_BODY$', $tableBodyFields, $templateData);

        $path = $this->path . $fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->info("index.blade.php created");
    }

}
