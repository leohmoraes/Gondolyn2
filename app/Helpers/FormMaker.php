<?php namespace App\Helpers;

use View;
use Schema;
use DB;
use App\Helpers\Validation;
use App\Helpers\InputMaker;
use Config;

/**
 * FormMaker helper to make table and object form mapping easy
 */
class FormMaker
{

    /**
     * Generate a form from a table
     * @param  string  $table       Table name
     * @param  string  $view        View to use - for custom form layouts
     * @param  array   $columns     Array of columns and details regarding them see config/forms.php for examples
     * @param  string  $class       Class names to be given to the inputs
     * @param  boolean $reformatted Corrects the table column names to clean words if no columns array provided
     * @param  boolean $populated   Populates the inputs with the column names as values
     * @return string
     */
    public static function fromTable($table, $view = null, $columns = null, $class = 'form-control', $reformatted = true, $populated = false)
    {
        $formBuild = "";

        $tableColumns = Schema::getColumnListing($table);

        $tableTypeColumns = [];

        if (is_null($columns)) {
            foreach ($tableColumns as $column) {
                $type = DB::connection()->getDoctrineColumn($table, $column)->getType()->getName();
                $tableTypeColumns[$column] = $type;
            }
        } else {
            $tableTypeColumns = $columns;
        }

        foreach ($tableTypeColumns as $column => $field) {
            if (in_array($column, $tableColumns)) {
                $errors = Validation::errors('array');
                $input = InputMaker::create($column, $field, $column, $class, $reformatted, $populated);

                $formBuild .= FormMaker::formBuilder($view, $errors, $field, $column, $input);
            }
        }

        return $formBuild;
    }

    /**
     * Build the form from the an object
     * @param  array  $columns      Columns desired and specified
     * @param  string  $view        A template to use for the rows
     * @param  object  $object      An object to base the form off
     * @param  string  $class       Default input class
     * @param  boolean $populated   Is content populated
     * @param  boolean $reformatted Are column names reformatted
     * @return string
     */
    public static function fromObject($columns, $view = null, $object, $class = 'form-control', $populated = true, $reformatted = false)
    {
        $formBuild = "";

        $tableColumns = array_keys($object['attributes']);

        foreach ($columns as $column => $field) {
            if (in_array($column, $tableColumns)) {
                $errors = Validation::errors('array');
                $input = InputMaker::create($column, $field, $object, $class, $reformatted, $populated);
                $formBuild .= FormMaker::formBuilder($view, $errors, $field, $column, $input);
            }
        }

        return $formBuild;
    }

    /**
     * Constructs HTML forms
     * @param  string $view   View template
     * @param  array $errors Array of errors
     * @param  array $field  Array of field values
     * @param  string $column Column name
     * @param  string $input  Input string
     * @return string
     */
    public static function formBuilder($view, $errors, $field, $column, $input)
    {
        $formBuild = '';

        if (isset($errors[$column])) {
            $errorHighlight = ' has-error';
            $errorMessage = $errors[$column];
        } else {
            $errorHighlight = '';
            $errorMessage = false;
        }

        if (is_null($view)) {
            if (isset($field['type']) && (stristr($field['type'], 'radio') || stristr($field['type'], 'checkbox'))) {
                $formBuild .= '<div class="'.$errorHighlight.'">';
                $formBuild .= '<div class="'.$field['type'].'"><label>'.$input.InputMaker::cleanString(FormMaker::columnLabel($field, $column)).'</label>'.FormMaker::errorMessage($errorMessage).'</div>';
            } else if (isset($field['type']) && (stristr($field['type'], 'hidden'))) {
                $formBuild .= '<div class="form-group '.$errorHighlight.'">';
                $formBuild .= $input;
            } else {
                $formBuild .= '<div class="form-group '.$errorHighlight.'">';
                $formBuild .= '<label class="control-label" for="'.ucfirst($column).'">'.InputMaker::cleanString(FormMaker::columnLabel($field, $column)).'</label>'.$input.FormMaker::errorMessage($errorMessage);
            }

            $formBuild .= '</div>';
        } else {
            $formBuild .= View::make($view, array(
                'labelFor' => ucfirst($column),
                'label' => FormMaker::columnLabel($field, $column),
                'input' => $input,
                'errorMessage' => FormMaker::errorMessage($errorMessage),
                'errorHighlight' => $errorHighlight,
            ));
        }

        return $formBuild;
    }

    /**
     * Generate the error message for the input
     * @param  string $message Error message
     * @return string
     */
    public static function errorMessage($message)
    {
        if ( ! $message) {
            $realErrorMessage = '';
        } else {
            $realErrorMessage = '<div><p class="text-danger">'.$message.'</p></div>';
        }

        return $realErrorMessage;
    }

    /**
     * Create the column label
     * @param  array  $field  Field from Column Array
     * @param  string $column Column name
     * @return string
     */
    public static function columnLabel($field, $column)
    {
        return (isset($field['alt_name'])) ? $field['alt_name'] : ucfirst($column);
    }

    /**
     * Get Table Columns
     * @param  string $table Table name
     * @return array
     */
    public static function getTableColumns($table)
    {
        $tableColumns = Schema::getColumnListing($table);

        $tableTypeColumns = [];

        foreach ($tableColumns as $column) {
            if ( ! in_array($column, array('id', 'created_at', 'updated_at'))) {
                $type = DB::connection()->getDoctrineColumn($table, $column)->getType()->getName();
                $tableTypeColumns[$column]['type'] = $type;
            }
        }

        return $tableTypeColumns;
    }
}