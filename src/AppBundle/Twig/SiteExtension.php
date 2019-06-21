<?php

namespace AppBundle\Twig;

use Kalamu\DynamiqueConfigBundle\Container\ParameterContainer;

class SiteExtension extends \Twig_Extension
{

    /**
     * @var ParameterContainer
     */
    protected $dynamiqueConfig;

    public function __construct( ParameterContainer $dynamiqueConfig )
    {
        $this->dynamiqueConfig = $dynamiqueConfig;
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('dynamique_config', array($this, 'getDynamiqueConfig')),
            new \Twig_SimpleFunction('kalamu_id_class', array($this, 'getkalamuIdClass'), ['is_safe' => ['html']]),
        );
    }

    public function getDynamiqueConfig($parameter){
        if($this->dynamiqueConfig->has($parameter)){
            return $this->dynamiqueConfig->get($parameter);
        }
    }

    /**
     * Generate id and class attributs for kalamuDashboard elements
     *
     * @param array $responsive
     * @param array $complement
     */
    public function getkalamuIdClass(array $responsive, array $complement = []){
        $str_attr = '';
        $id = isset($complement['id']) ? $complement['id'] : (isset($responsive['id']) ? $responsive['id'] : '');
        if($id){
            $str_attr .= ' id="'.htmlspecialchars($id).'" ';
        }

        $class = array_merge($this->generateBootstrapClass($responsive),
                isset($responsive['class']) ? explode(' ', $responsive['class']) : [],
                isset($complement['class']) ? explode(' ', $complement['class']) : []);

        $class = array_filter(array_map('trim', $class));
        if(count($class)){
            $str_attr .= ' class="'.htmlspecialchars(implode(' ', array_unique($class))).'" ';
        }

        return $str_attr;
    }

    public function getName()
    {
        return 'site_extension';
    }

    /**
     * Generate bootstrap blass for elements visibility
     *
     * @param array $responsive
     */
    protected function generateBootstrapClass(array $responsive){
        $class = [];
        if(count($responsive['visible']) != 4){
            if(count($responsive['visible']) > 2 ){
                foreach(array_diff(['lg','md', 'sm', 'xs'], $responsive['visible']) as $size){
                    $class[] = 'hidden-'.$size;
                }
            }else{
                foreach($responsive['visible'] as $size){
                    $class[] = 'visible-'.$size;
                }
            }
        }
        if(isset($responsive['size'])){
            foreach($responsive['visible'] as $size){
                $class[] = 'col-'.$size.'-'.$responsive['size'][$size];
            }
        }

        return $class;
    }
}