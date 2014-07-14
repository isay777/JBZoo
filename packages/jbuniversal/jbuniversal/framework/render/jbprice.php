<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBPriceRenderer
 */
class JBPriceRenderer extends PositionRenderer
{
    /**
     * @var JBCartElementHelper
     */
    protected $_jbcartelement;
    /**
     * @var JBCartPositionHelper
     */
    protected $_jbposition;

    /**
     * @var JBModelConfig
     */
    protected $_jbconfig;

    /**
     * @param App $app
     * @param null $path
     */
    public function __construct($app, $path = null)
    {
        parent::__construct($app, $path);

        $this->_jbposition  = $app->jbcartposition;
        $this->_cartelement = $app->jbcartelement;
        $this->_jbconfig    = JBModelConfig::model();
    }

    /**
     * @param string $position
     * @return bool|void
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $key => $data) {
            if ($element = $this->_cartelement->create($data['type'], $data['group'])) {

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $key;

                if ($element->canAccess() && $element->hasValue($this->app->data->create($data))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $layout
     * @param array $args
     * @return string
     */
    public function render($layout, $args = array())
    {
        $result = null;
        $this->addPath(array(
                $this->app->path->path('jbtmpl:catalog/'),
                'jbprice.' . $layout
            )
        );

        $result .= parent::render('jbprice.' . $layout, $args);

        return $result;
    }

    /**
     * @param string $position
     * @param array $args
     * @return string|void
     */
    public function renderPosition($position = null, $args = array())
    {
        // init vars
        $elements = array();
        $output   = array();

        // get style
        $style = isset($args['style']) ? $args['style'] : 'jbblock';


        // store layout
        $layout = $this->_layout;

        foreach ($this->_getConfigPosition($position) as $key => $data) {
            if ($element = $this->_cartelement->create($data['type'], $data['group'])) {

                if (!$element->canAccess()) {
                    continue;
                }

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $key;
                $data['altlabel']  = ' ';

                // set params
                $params = array_merge($data, $args);

                if ($element->hasValue($this->app->data->create($params))) {
                    $elements[] = compact('element', 'params');
                }
            }
        }

        foreach ($elements as $i => $data) {
            $params = array_merge(array('first' => ($i == 0), 'last' => ($i == count($elements) - 1)), $data['params']);
            $this->addPath(array(
                $this->app->path->path('jbtmpl:catalog/'),
                'element.' . $style
            ));

            $output[$i] = parent::render('element.' . $style, array('element' => $data['element'], 'params' => $params));
        }

        $this->_layout = $layout;

        return implode("\n", $output);
    }

    /**
     * @param $position
     * @return mixed
     */
    public function _getConfigPosition($position)
    {
        $positions = $this->_getPositions();
        $position  = $positions->get($this->_layout . '.' . $position);

        return $position;
    }

    /**
     * @param null $dir
     * @return AppParameter|mixed
     */
    public function getConfig($dir = null)
    {
        $priceparams = $this->_jbconfig->getGroup('cart.priceparams');
        return $priceparams->get('list');
    }

    /**
     * @return JSONData
     */
    public function _getPositions()
    {
        $layouts = $this->_jbconfig->getGroup('cart.jbpricetmpl');
        return $layouts;
    }

    /**
     * @param string $dir
     * @return array
     */
    public function getLayouts($dir)
    {
        // init vars
        $layoutList = array();
        $parts      = explode('.', $dir);
        $path       = implode('/', $parts);

        // parse positions xml
        if ($xml = simplexml_load_file($this->_getPath($path . '/' . $this->_xml_file))) {

            $layouts = $xml->xpath('positions[@layout]');

            foreach ($layouts as $layout) {

                $name = (string)$layout->attributes()->layout;

                $layoutList[$name] = $name;
            }

        }

        return $layoutList;
    }

}
