<?php namespace App\Helpers;

use View;
use Schema;
use DB;
use App\Helpers\Validation;
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
                $input = FormMaker::inputMaker($column, $field, $column, $class, $reformatted, $populated);

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
                $input = FormMaker::inputMaker($column, $field, $object, $class, $reformatted, $populated);
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
                $formBuild .= '<div class="'.$field['type'].'"><label>'.$input.FormMaker::cleanString(FormMaker::columnLabel($field, $column)).'</label>'.FormMaker::errorMessage($errorMessage).'</div>';
            } else if (isset($field['type']) && (stristr($field['type'], 'hidden'))) {
                $formBuild .= '<div class="form-group '.$errorHighlight.'">';
                $formBuild .= $input;
            } else {
                $formBuild .= '<div class="form-group '.$errorHighlight.'">';
                $formBuild .= '<label class="control-label" for="'.ucfirst($column).'">'.FormMaker::cleanString(FormMaker::columnLabel($field, $column)).'</label>'.$input.FormMaker::errorMessage($errorMessage);
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
     * Create the input HTML
     * @param  string   $name        Column/ Field name
     * @param  array    $field       Array of config info for item
     * @param  object   $object      Object or Table Object
     * @param  string   $class       CSS class
     * @param  boolean  $reformatted Clean the labels and placeholder values
     * @param  boolean  $populated   Set the value of the input to the object's value
     * @return string
     */
    public static function inputMaker($name, $field, $object, $class, $reformatted = false, $populated)
    {
        $config = array();

        $config['populated'] = $populated;
        $config['name']      = $name;
        $config['class']     = $class;
        $config['field']     = $field;

        $config['inputTypes'] = Config::get('form.maker');

        $config['inputs'] = Validation::inputs();

        $config['objectName']     = (isset($object->$name)) ? $object->$name : $name;
        $config['placeholder']    = FormMaker::placeholder($field, $name);

        // If validation inputs are available lets prepopulate the fields!
        if (isset($config['inputs'][$name])) {
            $config['populated'] = true;
            $config['objectName'] = $config['inputs'][$name];
        }

        if ($reformatted) {
            $config['placeholder'] = FormMaker::cleanString(FormMaker::placeholder($field, $name));
        }

        if ( ! isset($field['type'])) {
            if (is_array($field)) {
                $config['fieldType'] = 'string';
            } else {
                $config['fieldType'] = $field;
            }
        } else {
            $config['fieldType'] = $field['type'];
        }

        $inputString = FormMaker::inputStringGenerator($config);

        return $inputString;
    }

    /**
     * The input string generator
     * @param  array $config  Config
     * @return string
     */
    public static function inputStringGenerator($config)
    {
        $textInputs     = ['text', 'textarea'];
        $selectInputs   = ['select'];
        $hiddenInputs   = ['hidden'];
        $checkboxInputs = ['checkbox', 'checkbox-inline'];
        $radioInputs    = ['radio', 'radio-inline'];

        $checkType      = (in_array($config['fieldType'], $checkboxInputs)) ? 'checked' : 'selected';
        $selected       = (isset($config['inputs'][$config['name']]) || isset($config['field']['selected']) || $config['objectName'] === 'on') ? $checkType : '';
        $custom         = (isset($config['field']['custom'])) ? $config['field']['custom'] : '';

        switch ($config['fieldType']) {
            case in_array($config['fieldType'], $hiddenInputs):
                $population = ($config['populated']) ? $config['objectName'] : '';
                $inputString = '<input '.$custom.' id="'.ucfirst($config['name']).'" name="'.$config['name'].'" type="hidden" value="'.$population.'">';
                break;

            case in_array($config['fieldType'], $textInputs):
                $population = ($config['populated']) ? $config['objectName'] : '';
                $inputString = '<textarea '.$custom.' id="'.ucfirst($config['name']).'" class="'.$config['class'].'" name="'.$config['name'].'" placeholder="'.$config['placeholder'].'">'.$population.'</textarea>';
                break;

            case in_array($config['fieldType'], $selectInputs):
                $options = '';
                foreach ($config['field']['options'] as $key => $value) {
                    $selected = ($config['objectName'] === $value) ? 'selected' : '';
                    $options .= '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
                }
                $inputString = '<select '.$custom.' id="'.ucfirst($config['name']).'" class="'.$config['class'].'" name="'.$config['name'].'">'.$options.'</select>';
                break;

            case in_array($config['fieldType'], $checkboxInputs):
                $inputString = '<input '.$custom.' id="'.ucfirst($config['name']).'" '.$selected.' type="checkbox" name="'.$config['name'].'">';
                break;

            case in_array($config['fieldType'], $radioInputs):
                $inputString = '<input '.$custom.' id="'.ucfirst($config['name']).'" '.$selected.' type="radio" name="'.$config['name'].'">';
                break;

            default:
                // Pass along the config
                $config['type'] = $config['inputTypes'][$config['fieldType']];
                $inputString = FormMaker::makeHTMLInputString($config);
                break;
        }

        return $inputString;
    }

    /**
     * Generate a standard HTML input string
     * @param  array $config        Config array
     * @return string
     */
    public static function makeHTMLInputString($config)
    {
        $custom             = (isset($config['field']['custom'])) ? $config['field']['custom'] : '';
        $multiple           = (isset($config['field']['multiple'])) ? 'multiple' : '';
        $multipleArray      = (isset($config['field']['multiple'])) ? '[]' : '';
        $floatingNumber     = ($config['fieldType'] === 'float' || $config['fieldType'] === 'decimal') ? 'step="any"' : '';
        if (is_array($config['objectName']) && $config['type'] === 'file') {
            $population = '';
        } else {
            $population = ($config['populated'] && $config['name'] !== $config['objectName']) ? 'value="'.$config['objectName'].'"' : '';
        }

        $inputString        = '<input '.$custom.' id="'.ucfirst($config['name']).'" class="'.$config['class'].'" type="'.$config['type'].'" name="'.$config['name'].$multipleArray.'" '.$floatingNumber.' '.$multiple.' '.$population.' placeholder="'.$config['placeholder'].'">';
        return $inputString;
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
     * Create the placeholder
     * @param  array  $field  Field from Column Array
     * @param  string $column Column name
     * @return string
     */
    public static function placeholder($field, $column)
    {
        $alt_name = (isset($field['alt_name'])) ? $field['alt_name'] : ucfirst($column);
        $placeholder = (isset($field['placeholder'])) ? $field['placeholder'] : $alt_name;
        return $placeholder;
    }

    /**
     * Clean the string for the column name swap
     * @param  string $string Original column name
     * @return string
     */
    public static function cleanString($string)
    {
        return preg_replace("/[^a-z0-9 ]/i", "", ucwords(str_replace('_', ' ', $string)));
    }

    public static function getGenerateColumnsList($table)
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