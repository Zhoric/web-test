<div class="menu">
    <a href="/home" data-bind="css: {'current': $root.page() === menu.student.main}">Главная</a>
    <a href="/results" data-bind="css: {'current': $root.page() === menu.student.results}">Результаты</a>
    <a href="/materials" data-bind="css: {'current': $root.page() === menu.student.materials}">Материалы</a>
    <a class="user" data-bind="text: $root.user.name()"></a>
    <div class="menu-dd">
        <a data-bind="click: $root.user.password.change.bind($root)">Сменить пароль</a>
        <a href="/logout">Выход</a>
    </div>
</div>




