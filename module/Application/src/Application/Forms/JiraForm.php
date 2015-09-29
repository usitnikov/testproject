<?php
/**
 * Created by PhpStorm.
 * User: bagira74
 * Date: 01.10.2015
 * Time: 22:56
 */

namespace Application\Forms;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;

class JiraForm extends Form {
    protected $host;
    protected $login;
    protected $password;
    protected $send;
    protected $filter;

    public function __construct($name = null, $options = array()) {
        parent::__construct($name, $options);
        $this->filter = new InputFilter();
        $this->host = array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'host',
            'options' => array(
                'label' => 'Host',
                'label_attributes'          => array(
                    'class'         => 'col-sm-2 control-label',
                ),
            ),
            'attributes'    => array(
                'class'         => 'form-control',
                'id'            => 'host',
            ),
        );
        $this->filter->add(array(
            'name' => 'host',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'not_empty',
                ),
                array(
                    'name' => 'string_length',
                    'options' => array(
                        'min' => 12
                    ),
                ),
            ),
        ));
        $this->add($this->host);
        $this->login = array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'login',
            'options' => array(
                'label' => 'Login',
                'label_attributes'          => array(
                    'class'         => 'col-sm-2 control-label',
                ),
            ),
            'attributes'    => array(
                'class'         => 'form-control',
                'id'            => 'login',
            ),
        );
        $this->filter->add(array(
            'name' => 'login',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'not_empty',
                ),
                array(
                    'name' => 'string_length',
                    'options' => array(
                        'min' => 4
                    ),
                ),
            ),
        ));
        $this->add($this->login);
        $this->password = array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
                'label_attributes'          => array(
                    'class'         => 'col-sm-2 control-label',
                ),
            ),
            'attributes'    => array(
                'class'         => 'form-control',
                'id'            => 'password',
            ),
        );
        $this->filter->add(array(
            'name' => 'password',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'not_empty',
                ),
                array(
                    'name' => 'string_length',
                    'options' => array(
                        'min' => 4
                    ),
                ),
            ),
        ));
        $this->add($this->password);
        $this->send = array(
            'type' => "Zend\Form\Element\Submit",
            'name' => "send",
            'attributes' => array(
                'class' => "btn btn-default",
                'id' => "send",
                'value' => 'get data',
            ),
        );
        $this->add($this->send);
    }
} 