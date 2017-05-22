<?php
/**
 * @link https://github.com/timurmelnikov/yii2-loading-overlay
 * @copyright Copyright (c) 2017 Timur Melnikov
 * @license MIT
 */
namespace timurmelnikov\widgets;

use Yii;
use yii\widgets\Pjax;

/**
 * @author Timur Melnikov <melnilovt@gmail.com>
 */
class LoadingOverlayPjax extends Pjax
{
 
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
    * Альтернативный DOM элемент наложения LoadingOverlay
    */
    public $elementOverlay = '';
    /**
    * @var string
    * ID JavaScript экземпляра PjaxLoadingOverlay
    */
    public $idJS;

    public function init()
    {

        parent::init();

        $bundle = LoadingOverlayAsset::register($this->getView());
        if ($this->image == '') {
            $this->image =  $bundle->baseUrl.'/src/loading.gif';
        }
        if ($this->fontawesome != '') {
            $this->image = '';
        }
        $this->fade =  json_encode($this->fade);

        //Обработка разнотипных переменных
        if (gettype($this->maxSize) == 'string') {
            $this->maxSize = '"'.$this->maxSize.'"';
        }
        if (gettype($this->minSize) == 'string') {
            $this->minSize = '"'.$this->minSize.'"';
        }
        if (gettype($this->size) == 'string') {
            $this->size = '"'.$this->size.'"';
        }

        $this->idJS = $this->id;
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
 * Метод регистрации скрипта jQuery LoadingOverlay в представлении
 */
    private function registerLoader() //Нужна доработка...
    {


        $script = <<< JS
    $(document).on('pjax:send', function(event) {
        if ("{$this->idJS}" === event.target.id) {
            if ("{$this->elementOverlay}" === "") {
                $("#"+"{$this->idJS}").LoadingOverlay("show");
            } else {
                $("$this->elementOverlay").LoadingOverlay("show");
            }
        }
    })
    $(document).on('pjax:complete', function(event) {
        if ("{$this->idJS}" === event.target.id) {
            if ("{$this->elementOverlay}" === "") {
                $("#"+"{$this->idJS}").LoadingOverlay("hide");
            } else {
                $("$this->elementOverlay").LoadingOverlay("hide");
            }
        }
    })
JS;
        Yii::$app->view->registerJs($script, yii\web\View::POS_READY, $this->idJS);
    }
}