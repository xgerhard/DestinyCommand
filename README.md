# Install DestinyCommand
1. Get the repository: `https://github.com/xgerhard/DestinyCommand.git`
2. Run `composer install` from the src folder
3. Rename `.env.example` to `.env`
4. Run `php artisan key:generate` to generate a key inside `.env`
5. Ente a Bungie API key in `.env` from from `https://www.bungie.net/en/Application`

# Get manifest
1. Uncomment line 17 in `src\app\Destiny\Manifest.php`
2. Run a command that requires the manifest for example: `api/command?query=primary%20xgerhard`

# Support
For help contact us <a href="https://twitter.com/destinycommand">@DestinyCommand</a> on Twitter