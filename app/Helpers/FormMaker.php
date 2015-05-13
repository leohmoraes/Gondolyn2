<?php namespace App\Helpers;

use View, Schema, DB, Validation;

/**
 * FormMaker helper to make table and object form mapping easy
 */
class FormMaker {

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
                $errorMessage = false;
                $errors = Validation::errors('array');
                $input = FormMaker::inputMaker($column, $field, $column, $class, $reformatted, $populated);

                $formBuild .= FormMaker::formBuilder($view, $errors, $field, $column, $input);
            }
        }

        return $formBuild;
    }

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

    public static function formBuilder($view, $errors, $field, $column, $input)
    {
        $formBuild = '';

        if (is_null($view)) {
            if (isset($errors[$column])) {
                $errorHighlight = ' has-error';
                $errorMessage = $errors[$column];
            } else {
                $errorHighlight = '';
                $errorMessage = false;
            }

            if (isset($field['type']) && (stristr($field['type'], 'radio') || stristr($field['type'], 'checkbox'))) {
                $formBuild .= '<div class="'.$errorHighlight.'">';
                $formBuild .= '<div class="'.$field['type'].'"><label>'.$input.FormMaker::cleanString(FormMaker::columnLabel($field, $column, true)).'</label>'.FormMaker::errorMessage($errorMessage).'</div>';
            } else {
                $formBuild .= '<div class="form-group '.$errorHighlight.'">';
                $formBuild .= '<label class="control-label" for="'.ucfirst($column).'">'.FormMaker::cleanString(FormMaker::columnLabel($field, $column, true)).'</label>'.$input.FormMaker::errorMessage($errorMessage);
            }

            $formBuild .= '</div>';
        } else {
            $formBuild .= View::make($view, array(
                'label' => FormMaker::columnLabel($field, $column),
                'input' => $input,
                'errorMessage' => FormMaker::errorMessage($errorMessage),
                'errorHighlight' => $errorHighlight
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
        $stringInputs   = ['string', 'email', 'varchar'];
        $integerInputs  = ['number', 'integer', 'float', 'decimal'];
        $textInputs     = ['text', 'textarea'];
        $selectInputs   = ['select'];
        $passwordInputs = ['password'];
        $fileInputs     = ['file', 'image'];
        $dateInputs     = ['datetime', 'date'];
        $checkboxInputs = ['checkbox', 'checkbox-inline'];
        $radioInputs    = ['radio', 'radio-inline'];

        $inputs = Validation::inputs();

        $inputString = '';

        $objectName     = (isset($object->$name)) ? $object->$name: $object;
        $placeholder    = FormMaker::columnLabel($field, $name);

        // If validation inputs are available lets prepopulate the fields!
        if (isset($inputs[$name])) {
            $populated = true;
            $objectName = $inputs[$name];
        }

        if ($reformatted) {
            $objectName     = FormMaker::cleanString($objectName);
            $placeholder    = FormMaker::cleanString(FormMaker::columnLabel($field, $name));
        }

        $fieldType      = ( ! isset($field['type'])) ? $field : $field['type'];

        switch ($fieldType) {
            case in_array($fieldType, $stringInputs):
                $population = ($populated) ? 'value="'.$objectName.'"' : '';
                $inputString .= '<input id="'.ucfirst($name).'" class="'.$class.'" type="text" name="'.$name.'" '.$population.' placeholder="'.$placeholder.'">';
                break;

            case in_array($fieldType, $integerInputs):
                $population = ($populated) ? 'value="'.$objectName.'"' : '';
                $floatingNumber = ($fieldType === 'float' || $fieldType === 'decimal') ? 'step="any"': '';
                $inputString .= '<input id="'.ucfirst($name).'" class="'.$class.'" type="number" '.$floatingNumber.' name="'.$name.'" '.$population.' placeholder="'.$placeholder.'">';
                break;

            case in_array($fieldType, $textInputs):
                $population = ($populated) ? $objectName : '';
                $inputString .= '<textarea id="'.ucfirst($name).'" class="'.$class.'" name="'.$name.'" placeholder="'.$placeholder.'">'.$population.'</textarea>';
                break;

            case in_array($fieldType, $selectInputs):
                $options = '';
                foreach ($field['options'] as $key => $value) {
                    $selected = ($objectName === $value) ? 'selected': '';
                    $options .= '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
                }
                $inputString .= '<select id="'.ucfirst($name).'" class="'.$class.'" name="'.$name.'">'.$options.'</select>';
                break;

            case in_array($fieldType, $passwordInputs):
                $inputString .= '<input id="'.ucfirst($name).'" class="'.$class.'" type="password" name="'.$name.'" placeholder="'.$placeholder.'">';
                break;

            case in_array($fieldType, $dateInputs):
                $inputString .= '<input id="'.ucfirst($name).'" class="'.$class.'" type="date" name="'.$name.'" placeholder="'.$placeholder.'">';
                break;

            case in_array($fieldType, $fileInputs):
                $multiple = (isset($field['multiple'])) ? 'multiple': '';
                $inputString .= '<input id="'.ucfirst($name).'" class="'.$class.'" '.$multiple.' type="file" name="'.$name.'" value="'.$objectName.'" placeholder="'.$placeholder.'">';
                break;

            case in_array($fieldType, $checkboxInputs):
                $checked = (isset($inputs[$name]) || isset($field['selected'])) ? 'checked': '';
                $inputString .= '<input id="'.ucfirst($name).'" '.$checked.' type="checkbox" name="'.$name.'">';
                break;

            case in_array($fieldType, $radioInputs):
                $selected = (isset($inputs[$name]) || isset($field['selected'])) ? 'selected': '';
                $inputString .= '<input id="'.ucfirst($name).'" '.$selected.' type="radio" name="'.$name.'">';
                break;

            default:
                $inputString .= '<input id="'.ucfirst($name).'" class="'.$class.'" type="text" name="'.$name.'" value="'.$objectName.'" placeholder="'.$placeholder.'">';
                break;
        }

        return $inputString;
    }

    /**
     * Generate the error message for the input
     * @param  string $message Error message
     * @return string
     */
    public static function errorMessage($message)
    {
        $realErrorMessage = '';

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
     * Clean the string for the column name swap
     * @param  string $string Original column name
     * @return string
     */
    public static function cleanString($string)
    {
        return preg_replace( "/[^a-z0-9 ]/i", "", ucwords(str_replace('_', ' ', $string)));
    }
}