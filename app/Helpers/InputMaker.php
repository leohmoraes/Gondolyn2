<?php namespace App\Helpers;

use Config;

/**
 * InputMaker helper to make an HTML input
 */
class InputMaker
{
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
    public static function create($name, $field, $object, $class, $reformatted = false, $populated)
    {
        $config = array();

        $config['populated'] = $populated;
        $config['name']      = $name;
        $config['class']     = $class;
        $config['field']     = $field;

        if (isset($field['class'])) {
            $config['class']     = $class.' '.$field['class'];
        }

        $config['inputTypes'] = Config::get('form.maker');

        $config['inputs'] = Validation::inputs();

        $config['objectName']     = (isset($object->$name)) ? $object->$name : $name;
        $config['placeholder']    = InputMaker::placeholder($field, $name);

        // If validation inputs are available lets prepopulate the fields!
        if (isset($config['inputs'][$name])) {
            $config['populated'] = true;
            $config['objectName'] = $config['inputs'][$name];
        }

        if ($reformatted) {
            $config['placeholder'] = InputMaker::cleanString(InputMaker::placeholder($field, $name));
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

        $inputString = InputMaker::inputStringGenerator($config);

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
                $inputString = InputMaker::makeHTMLInputString($config);
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
}