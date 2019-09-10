# FeedYaTurbo
Для яндекс турбо-страницы формирует RSS канал с полями для яндекса.
Все работает по образу и подобию штатного Feed от ZF3.
Убраны не нужные расширения и устаревший менеджер

Требования к ленте https://yandex.ru/dev/turbo/doc/rss/markup-docpage/

Установка composer require masterflash-ru/feed-yaturbo

пример использования:
```php
use Mf\FeedYaTurbo\Writer\Feed;

        $feed = new Feed;
        $feed->setTitle("автопортал За рулем Кубань");
        $feed->setLanguage('ru');

        $feed->setDescription("Новости");
        $feed->setLink('https://zrkuban.ru'); 
       
        $feed->addAnalytics([
            "type"=>"Yandex",
            "id"=>"234234234",
            "params"=>"{2222,2343434}"
        ]);
        $feed->addAnalytics([
            "type"=>"Google",
            "id"=>"456456dfghdfghfdgh",
        ]);
        
        $feed->addNetwork([
            "type"=>"AdFox",
            "turbo-ad-id"=>"456456dfghdfghfdgh",
            "content"=>"содержимое блока"
        ]);


        $entry = $feed->createEntry();

        $entry->setLink('http://www.example.com/all-your-base-are-belong-to-us');
        
        $entry->addAuthor([
            'name'  => 'Paddy',
            'email' => 'paddy@example.com',
            'uri'   => 'http://www.example.com',
        ]);
        
        $entry->setDateCreated(time());
        
        //раздел, например, новости
        $entry->addCategory([
                    'term'=>"news",
                ]);
        $entry->setSource("https://www.zrkuban.ru/news");
        $entry->setContent(
            'Подробно статья Подробно статья Подробно статья Подробно статья Подробно статья '
        );
 
        $feed->addEntry($entry);
        echo $feed->export();
```
