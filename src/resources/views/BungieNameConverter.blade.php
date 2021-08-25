<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<main role="main" class="container">

    <h1>BungieName Converter</h1>
    <p>
        Due to the recent crossplay update (JAY!) our search function has been broken in certain cases. The Destiny API requires the new Bungie Name, this username looks like <b>username#1234</b>, you can find yours at: <a href="https://www.bungie.net/7/en/User/Account/IdentitySettings">https://www.bungie.net/7/en/User/Account/IdentitySettings</a>.<br/><br/>
        Alot of !destiny command users use commands like <b>!primary</b> or similar command to show their loadout. These commands are probably broken now.. you can use this converter to update your URL's inside your command, this converter should cover most use-cases.<br/><br/>
        If after converting, you still have problems with profiles you can't find, please contact us <a href="https://twitter.com/destinycommand" target="blank">@DestinyCommand</a>.
    </p>
    @if($submit && !empty($errors))
    <div class="alert alert-danger" role="alert">
        <ul>
        @foreach($errors as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @elseif($newUrl)
    <div class="alert alert-success" role="alert">
        <h3>New url:</h3>
        <pre>{{ $newUrl }}</pre>
        <h3>Preview:</h3>
            <iframe src="{{ $newUrl }}" frameborder="1" scrolling="auto"></iframe>
        </div>
    @endif

    <form action="" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="form-group">
            <label for="username">Bungie name:</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="xgerhard_#3325" aria-describedby="usernameHelp" value="{{ $username }}">
            <small id="usernameHelp" class="form-text text-muted">See: <a href="https://www.bungie.net/7/en/User/Account/IdentitySettings" target="blank">https://www.bungie.net/7/en/User/Account/IdentitySettings</a></small>
        </div>
        <div class="form-group">
            <label for="url">Url inside your command:</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="https://destinycommand.com/live/api/command?query=primary%20xgerhard%2321555%pc&default_console=pc" value="{{ $url }}">
        </div>
        <button type="submit" class="btn btn-primary">Convert</button>
    </form>
</main>
<style>
.form-group {
    margin-bottom: 1rem;
}

iframe {
    border: 1px solid #0f5132;
    border-radius: .25rem;
    width: 100%;
}
</style>