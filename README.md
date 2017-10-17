# Install DestinyCommand
1. Get the repository: `https://github.com/xgerhard/DestinyCommand.git`
2. Run `composer install` from the `src` folder
3. Rename `.env.example` to `.env`
4. Run `php artisan key:generate` to generate a key inside `.env`
5. Enter a Bungie API key in `.env` from `https://www.bungie.net/en/Application`

# Get manifest
1. Uncomment line 17 in `src\app\Destiny\Manifest.php`
2. Do a request that requires the manifest for example: `{url}/api/command?query=primary%20xgerhard`

# Support
For help contact us <a href="https://twitter.com/destinycommand">@DestinyCommand</a> on Twitter