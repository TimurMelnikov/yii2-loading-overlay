<?php
/**
 * @link https://github.com/timurmelnikov/yii2-loading-overlay
 * @copyright Copyright (c) 2017 Timur Melnikov
 * @license MIT
 */
namespace timurmelnikov\widgets;

use Yii;
use yii\base\Widget;

/**
 * @author Timur Melnikov <melnilovt@gmail.com>
 */
class LoadingOverlay extends Widget
{
    
    /*
    * Режим Pjax
    */
    const MODE_PJAX = 'modePjax';
    /*
    * Режим подключения jQuery LoadingOverlay
    */
    const MODE_REGISTER_BUNDLE = 'modeRegisterBundle';

    /**
    * @var string Свойство CSS background-color в формате rgba()
    */
    public $color = 'rgba(255, 255, 255, 0.8)';
    /**
    * @var array Управление появлением / затуханием
    * Boolean/Integer/String/Array
    */
    public $fade = [];
    /**
    * @var string Классы иконок Font Awesome
    */
    public $fontawesome = '';
    /**
    * @var string URL картинки
    */
    public $image = '';
    /**
    * @var string Свойство CSS background-position, для настройки расположения изображения
    */
    public $imagePosition = 'center center';
    /**
    * @var integer string Максимальный размер в пикселях
    */
    public $maxSize = '80px';
    /**
    * @var integer string Минимальный размер в пикселях
    */
    public $minSize = '20px';
    /**
    * @var integer string Размер изображения в процентах
    */
    public $size = '50%';
    /**
    * @var integer Свойство CSS z-index
    */
    public $zIndex = 9999;
    /**
    * @var string
    * Он-же Идентификатор экземпляра...
    */
    public $elementOverlay; //Нужна доработка...

    public function init()
    {
        $bundle = LoadingOverlayAsset::register($this->getView());
        if ($this->image == '') {
            $this->image =  $bundle->baseUrl.'/src/loading.gif';
        }
        if ($this->fontawesome != '') {
            $this->image = '';
        }
        $this->fade =  json_encode($this->fade);

//Вот так, буду обрабатывать "разнотипные" переменные
        if (gettype($this->maxSize) == 'string') {
            $this->maxSize = '"'.$this->maxSize.'"';
        }
        if (gettype($this->minSize) == 'string') {
            $this->minSize = '"'.$this->minSize.'"';
        }
        if (gettype($this->size) == 'string') {
            $this->size = '"'.$this->size.'"';
        }



        $this->setDefaults();
        $this->registerLoader();
    }

/**
 * Метод сборки скрипта установки начальных настроек лоадера и регистрация его в представлении
 */
    private function setDefaults() //Нужна доработка...
    {
        $script = <<<JS
  $.LoadingOverlaySetup({
     color           : "{$this->color}",
     fade            :  {$this->fade},
     fontawesome     : "{$this->fontawesome}",
     image           : "{$this->image}",
     imagePosition   : "{$this->imagePosition}",
     maxSize         :  {$this->maxSize},
     minSize         :  {$this->minSize},
     size            :  {$this->size},
     zIndex          :  {$this->zIndex}
  });
JS;
        Yii::$app->view->registerJs($script);
    }

/**
 * Метод регистрации скрипта лоадера в представлении
 */
    private function registerLoader() //Нужна доработка...
    {

//Перехват любого AJAX запроса...
//         $script = <<<JS
// $(document).ajaxSend(function(event, jqxhr, settings){
//     $("{$this->elementOverlay}").LoadingOverlay("show");
// });
// $(document).ajaxComplete(function(event, jqxhr, settings){
//     $("{$this->elementOverlay}").LoadingOverlay("hide");
// });
// JS;


// Перехват Pjax...
        $script = <<< JS
var elementOverlay = "{$this->elementOverlay}";        
$(document).on('pjax:send', function() {
    if (elementOverlay != "") {
        $("{$this->elementOverlay}").LoadingOverlay("show"); //На элемент
    } else {
        $.LoadingOverlay("show"); //На всю страницу
    }
})
$(document).on('pjax:complete', function() {
    if (elementOverlay != "") {
        $("{$this->elementOverlay}").LoadingOverlay("hide", true); //На элемент
    } else {
        $.LoadingOverlay("hide"); //На всю страницу
    }
})
JS;
        Yii::$app->view->registerJs($script, yii\web\View::POS_READY, $this->elementOverlay);
    }
}
