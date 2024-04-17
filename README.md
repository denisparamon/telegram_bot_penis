```shell
ngrok http 8000
php -S localhost:8000 index.php
```

set webhook

```
https://api.telegram.org/bot{my_bot_token}/setWebhook?url={url_to_send_updates_to}?token={my_bot_token}
```
