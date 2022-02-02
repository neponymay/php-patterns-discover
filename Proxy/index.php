<?php

/*
 * Суть паттерна в том, что мы создаём объект("заместитель") иначе говоря, дублёра оригинального объекта("сервис").
 * Например мы хотип получить доступ к объекту базы данных в коде, но не хотим тащить его каждый раз, когда это нужно.
 * Вместо этого мы создадим объект к которому будем обращаться как объекту базы, а он в свою очередь уже пообщается с
 * объектом базы и даст нам результат. Получается некоторая прослойка.
 * Заместитель как бы притворяется базой данных, но может иметь свои фишечки
 */



//Игрушечный пример

/*
 * Предствим такую картину: Есть некоторый класс, который загружает видео с ютуба(ThirdPartyYoutubeClass).
 * Пользователь обращается к нему, он загружает видео и отдаёт пользователю.
 * Но вот незадача! Каждый раз, когда мы запрашиваем у него видео, то он загружает всё по-новой! А интернет нынче дорог
 * Решим это проблему так: Создадим класс "заместитель"(CachedYouTubeClass), который будет уметь всё то же что и оригинальный класс ютуба,
 * но ещё он будет уметь кэшировать видосы и отдавать нам кэш вместо повторной загрузки.
 */



//Интерфейс самой библиотеки ютуба
interface ThirdPartyYouTubeLib{
    function listVideos();
    function getVideoInfo($id);
    function downloadVideo($id);
}


//Реализация интерфеса этой библиотеки
class ThirdPartyYoutubeClass implements ThirdPartyYouTubeLib{

    /*
     * И вот на этом месте можно задаться вопросом: Так почему бы просто не дописать в класс "ThirdPartyYoutubeClass"
     * нужный нам функционал кэширования?
     * Банальный вопрос - анальный ответ.
     * Хоть в нашем примере этот интерфейс и реализуется нами, в реальности же его нельзя редактировать, так как это часть
     * сторонней библиотеки и его код может быть нам не доступен. Вот именно для того, чтобы разширить его функциональность
     * Мы и унаследуем от него класс "заместитель" и будем работать уже с ним.
     */


    public function listVideos()
    {
        // TODO: Implement listVideos() method.
    }

    public function getVideoInfo($id)
    {
        // TODO: Implement getVideo() method.
    }

    public function downloadVideo($id)
    {
        // TODO: Implement downloadVideo() method.
    }
}

//Тот самый класс "заместитель".
//Мы делаем его наследником, потому что согласно принципу полиморфизма
//теперь его можно передавать туда, где ждут оригинальный объект
//Он будет его братом близнецом, но со своим тёмным прошлым и своими тараканами.
class CachedYouTubeClass implements ThirdPartyYouTubeLib{

    private ThirdPartyYouTubeLib $_service;
    private $_listCache;
    private $_videoCache;
    public bool $needReset;


    private function downloadVideoExists() : bool{
        return rand(10000000) % 2 == 0;
    }


    public function __construct(ThirdPartyYouTubeLib $service){
        $this->_service = $service;
    }

    public function listVideos()
    {
        if(is_null($this->_listCache) || $this->needReset){
            $this->_listCache = $this->_service->listVideos();
        }
        return $this->_listCache;
    }

    public function getVideoInfo($id)
    {
        if(is_null($this->_videoCache) || $this->needReset){
            $this->_videoCache = $this->_service->getVideoInfo($id);
        }
        return $this->_videoCache;
    }

    public function downloadVideo($id)
    {
        if(!downloadVideoExists($id) || $this->needReset){  // "downloadVideoExists" проверяет загружено ли уже видео
            $this->_service->downloadVideo($id);
        }
    }
}





/*
 * Так ну теперь применим написанное
 * Что иы имеем: Сервисный объект ютуба и его злого брата-близнеца
 * Теперь просто подсунем вместо ютуба, его близнеца и никто ничего не заметит
 */


//Класс, который работает с библиотекой ютуба
class YouTubeManager{
    protected ThirdPartyYouTubeLib $service; //Ждёт не двойника, а оригинал. Но подсунем двойника

    public function __construct(ThirdPartyYouTubeLib $service)
    {
        $this->service = $service;
    }

    public function renderVideoPage($id){
        $info = $this->service->getVideoInfo($id); // получает видео и что=то с ним делает(выводит)
    }

    public function renderListPanel(){
        $list = $this->service->listVideos(); // получает список видео и что=то с ним делает(выводит)
    }

    public function  reactOnUserInput(){
        $this->renderVideoPage();
        $this->renderListPanel();
    }

}



//Какое-то приложение
class Application{
    public function init(){
        $youTubeService = new ThirdPartyYoutubeClass(); //оригинал класса ютуба
        $youTubeProxy = new CachedYouTubeClass($youTubeService); //злой двойник
        /*
         * Вот тут то мы и осуществляем подставу
         * Выдаём клон за оригинал и никто не заметил разницы
         * так как они родственники(полиморфизм)
         * и менеджер его схавает только так
         */
        $manager = new YouTubeManager($youTubeProxy);
        $manager->reactOnUserInput();
    }
}