# publish_vk_posts
Класс для публикации записей на стене группы, может публиковать запись с вложением (картинка, документ). Максимальное количество вложений к одной записи 5
Первая версия класса на PHP для публикации записей на стене группы в вк. 
Как пользоваться
Для работы понадобится получить токен
почитать об этом тут https://vk.com/dev/access_token
Выставляем права которые вам нужны пока для скрипта нужны только wall,photos,docs,
Качаем файл 
подключаем его
Создаем новый обьект

$p = new PublishVk();

Указываем токен
$p->token = 'токен';
$p->group_id = 'id группы';
$p->album_id = 'id альбома для загрузки картинок';
$p->v = 'версия api ';
$p->text = 'Текст записи';
и вызывам метод.
Пока доступно два метода
публикация картинок
$p->UploadPhoto([массив картинок]);

публикация документов
$p->UploadDocument([массив картинок]);

Вроде расписал все. 
если что пищите на почту
Vityaak84@gmail.com
