## skypenotify (MODx Revolution)

подключить и инициализировать skypenotify
```
$skypenotify = $modx->getService('skypenotify')
$skypenotify->initialize();
```

подключить аккаунт скайп
```
$skypenotify->connect('login', 'password');

```

отправить сообщение
```
$skypenotify->sendMessage('bob_bobski', 'Привет Боб, как дела?');

```
