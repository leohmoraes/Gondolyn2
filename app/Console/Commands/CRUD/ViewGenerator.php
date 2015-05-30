<?php namespace App\Console\Commands\CRUD;

use Mitul\Generator\Generators\Scaffold\ViewGenerator;

class ResponsiveViewGenerator extends ViewGenerator
{
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
