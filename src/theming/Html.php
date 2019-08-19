<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use Yihai;
use yii\helpers\ArrayHelper;

class Html extends BaseHtml
{

    /**
     * Renders Bootstrap static form control.
     *
     * By default value will be HTML-encoded using [[encode()]], you may control this behavior
     * via 'encode' option.
     * @param string $value static control value.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. There are also a special options:
     *
     * - encode: bool, whether value should be HTML-encoded or not.
     *
     * @return string generated HTML
     * @see http://getbootstrap.com/css/#forms-controls-static
     */
    public static function staticControl($value, $options = [])
    {
        static::addCssClass($options, 'form-control-static');
        $value = (string) $value;
        if (isset($options['encode'])) {
            $encode = $options['encode'];
            unset($options['encode']);
        } else {
            $encode = true;
        }
        return static::tag('p', $encode ? static::encode($value) : $value, $options);
    }

    /**
     * Generates a Bootstrap static form control for the given model attribute.
     * @param \yii\base\Model $model the model object.
     * @param string $attribute the attribute name or expression. See [[getAttributeName()]] for the format
     * about attribute expression.
     * @param array $options the tag options in terms of name-value pairs. See [[staticControl()]] for details.
     * @return string generated HTML
     * @see staticControl()
     */
    public static function activeStaticControl($model, $attribute, $options = [])
    {
        if (isset($options['value'])) {
            $value = $options['value'];
            unset($options['value']);
        } else {
            $value = static::getAttributeValue($model, $attribute);
        }
        return static::staticControl($value, $options);
    }

    /**
     * {@inheritdoc}
     * @since 2.0.8
     */
    public static function radioList($name, $selection = null, $items = [], $options = [])
    {
        if (!isset($options['item'])) {
            $itemOptions = ArrayHelper::remove($options, 'itemOptions', []);
            $encode = ArrayHelper::getValue($options, 'encode', true);
            $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions, $encode) {
                $options = array_merge([
                    'label' => $encode ? static::encode($label) : $label,
                    'value' => $value
                ], $itemOptions);
                return '<div class="radio">' . static::radio($name, $checked, $options) . '</div>';
            };
        }

        return parent::radioList($name, $selection, $items, $options);
    }

    /**
     * {@inheritdoc}
     * @since 2.0.8
     */
    public static function checkboxList($name, $selection = null, $items = [], $options = [])
    {
        if (!isset($options['item'])) {
            $itemOptions = ArrayHelper::remove($options, 'itemOptions', []);
            $encode = ArrayHelper::getValue($options, 'encode', true);
            $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions, $encode) {
                $options = array_merge([
                    'label' => $encode ? static::encode($label) : $label,
                    'value' => $value
                ], $itemOptions);
                return '<div class="checkbox">' . Html::checkbox($name, $checked, $options) . '</div>';
            };
        }

        return parent::checkboxList($name, $selection, $items, $options);
    }

    /**
     * {@inheritdoc}
     * @since 2.0.8
     */
    public static function error($model, $attribute, $options = [])
    {
        if (!array_key_exists('tag', $options)) {
            $options['tag'] = 'p';
        }
        if (!array_key_exists('class', $options)) {
            $options['class'] = 'help-block help-block-error';
        }
        return parent::error($model, $attribute, $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    public static function icon($name, $options = [])
    {

        $iconMap = [
            'data' => 'far fa-dashboard',
            'system' => 'far fa-gear',
            'menu-header' => 'far fa-folders',
            'menu-group' => 'far fa-folder',
            'menu-item' => 'far fa-file',
            'settings' => 'far fa-cogs',
            'setting' => 'far fa-cog',
            'roles-permissions' => 'far fa-user-lock',
            'reports' => 'far fa-file-invoice',
            'password' => 'far fa-key',
        ];
        if(isset($iconMap[$name])){
            $name = $iconMap[$name];
            $class = $name;
        }else{
            $classPrefix = ArrayHelper::remove($options, 'prefix', 'fa fa-');
            $class = $classPrefix.$name;
        }
        $tag = ArrayHelper::remove($options, 'tag', 'span');
        $size = ArrayHelper::remove($options, 'size', 0);
        if($size !== 0){
            $class .= ' fa-'.$size.'x';
        }
        static::addCssClass($options, $class);
        return static::tag($tag, '', $options);
    }
}