<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */
namespace Franchise\Form\Validator;

use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;
use Zend\Validator\Identical;
use Zend\Validator\EmailAddress;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\AbstractValidator;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\File\Size;
use Zend\Validator\File\MimeType;
use Zend\Filter\File\RenameUpload;
use Zend\InputFilter\FileInput;
use Zend\Filter\File\LowerCase;
use Zend\Validator\Digits;
use Zend\I18n\Validator\Int;
use Zend\InputFilter\EmptyContextInterface;
use Zend\Form\Annotation\Required;

class ProductAddValidator extends InputFilter
{

    protected $opcionesAlnum = array(
        'allowWhiteSpace' => true,
        'messages' => array(
            'notAlnum' => "El valor no es alfanumerico"
        )
    );
    
    protected $opcionesAlnum2 = array(
        'allowWhiteSpace' => false,
        'messages' => array(
            'notAlnum' => "Solo numeros, letras y sin espacio "
        )
    );
    protected $opcionesAlpha2 = array(
        'allowWhiteSpace' => false,
        'messages' => array(
            'notAlpha' => "Solo numeros, letras y sin espacio "
        )
    );
    
    protected $filterGeneric = array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            );
    

    public function __construct()
    {      
        $logo = new FileInput('image');
        $logo->setRequired(false)
                     ->setAllowEmpty(false);
        
        $logo->getValidatorChain()
            ->attach(new Size(array(
            'messageTemplates' => array(
                
                Size::TOO_BIG => 'The file TOO_BIG',
                Size::TOO_SMALL => 'The file TOO_SMALL',
                Size::NOT_FOUND => 'The NOT_FOUND',
                NotEmpty::IS_EMPTY => 'Mail no debe ser vacía.',
            ),
            'options' => array(
                'max' => 40000
                
            )
        )));           
        // Validator File Type //
        $mimeType = new MimeType();
        $mimeType->setMimeType(array('image/gif', 'image/jpg','image/jpeg','image/png'));
        $logo->getValidatorChain()->attach($mimeType);

        /** Move File to Uploads/product **/
        $nameFile = sprintf("%simg_%s",'./public/img/catalog/product/', time());
        $rename = new RenameUpload($nameFile);
        //$rename->setTarget($nameFile);
        $rename->setUseUploadExtension(true);
        //$rename->setUseUploadName(true);
        $rename->setRandomize(true);
        $rename->setOverwrite(true);
              
        $logo->getFilterChain()->attach($rename);       
        $this->add($logo);
        
        $logo = new FileInput('image');
        $logo->setRequired(false)
                     ->setAllowEmpty(false);
        
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            )
        ));
         $this->add(array(
            'name' => 'option',
            'required' => false,
        ));
         $this->add(array(
            'name' => 'option_id',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'option_require',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'franchise_id',
            'required' => false,
        ));
       
    }
}
