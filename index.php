<form method="post" action="#">
    <label>
        Title:<br>
        <input name="title" type="text">
    </label>
    <label><br><br>
        Content:<br>
        <textarea name="content" rows="5" cols="20"></textarea>
    </label><br><br>
    <input type="submit" value="Отправить">
</form>
<form method="post" action="#">
    <label>
        Filter title:<br>
        <input type="search" name="search">
    </label>
    <input style="margin-left: -6px;" type="submit" value="Search" name="btnSearch">
        
</form>
<?php
try {
    //строка подключения mysql
    //$dsn = "mysql:host=localhost;dbname=blog";
    //строка подключения sqlite
    $dsn = "sqlite:blog.sqlite";
    //Подключение
    $db = new PDO($dsn,"blog","blog");
    $db->beginTransaction();//Начало транзакции
    //Настройки PDO(генерация ошибок для разробочикв)
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//    $sql = "create table post(
//        id integer primary key AUTOINCREMENT,
//        title varchar(60) not null,
//        content text not null,
//        published_date text
//    )";
//    $db->exec($sql);
    //Запрос без атвета(create, drop,delete,alter,insert...)
    if(!empty($_POST)){
        $db->commit();//Транзакция успешна
        if(isset($_POST['title'])&&isset($_POST['content'])){
            $title = $_POST['title'];
            $content = $_POST['content'];
            //for mysql
            //$sql = "insert into post(title,content)values('$title','$content')";
            //for sqlite
            $dbdate = date("Y-m-d H:i:s");
            $sql = "insert into post(title,content,published_date)values('$title','$content','$dbdate')";
            $count = $db->exec($sql);
        }
    }

    //Запросы с ответом (select,show databases),show tables)
    if(!isset($_POST['btnSearch'])) {
        //$sql = "select * from post order by published_date desc";
        $st = $db->prepare("select * from post order by published_date desc");
        $st->execute();
    }else{
        $search = $_POST['search'];
        //$sql = "select * from post where title LIKE '%%$search'";
        $st = $db->prepare("select * from post where title LIKE :filter order by published_date desc");
        $st->execute(['filter'=>"%$search%"]);

    }
        foreach ($st->fetchAll() as $row) {
            echo "<article>";
            echo "<header>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "</header>";
            echo "<div>{$row['content']}</div>";
            echo "<footer>";
            echo "<span style='font-size: 0.6em;'>{$row['published_date']}</span>";
            echo "</footer>";
            echo "</article>";
        }


    //Подгатовленые запросы


    //Транзакции
}catch(PDOException $ex) {
    $db->rollBack();//Отменна изменений
    //Обработки ошибок
    echo "<p style='color:red;'>".$ex->getMessage()."</p>";
}