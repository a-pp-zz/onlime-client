# OnlimeClient
Получение данных из личного кабинета Онлайм: баланс, баллы и т.д

```
<?php
use AppZz\Http\OnlimeClient;

$username = 'user';
$password = 'password';

try {
    $o = new OnlimeClient ($username, $password);
    $b = $o->balance();
    var_dump ($b);
} catch (Throwable $e) {
    echo $e->getMessage(), PHP_EOL;
}
?>

```
Вывод:

```
Array
(
    [contract] => 999999999
    [account] => 888888888
    [balance] => 468.11
    [lock] => 26
    [credit] => 0.00
    [payment] => 79.36
    [status] => 0
    [points] => 145.11
    [tier] => Золотой
    [bstatus] => Active
    [cnt] => 1
)
```