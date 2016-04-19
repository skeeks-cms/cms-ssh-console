<?php
$action->controller->layout = '@skeeks/cms/views/layouts/clean';
/**
 * Console
 *
 * @see https://github.com/elfet/console
 */
// Change next variables as you need.
// Digest HTTP Authentication
// To enable, add user: "name" => "password".
$users  = [];
$realm  = 'SkeekS CMS ssh console';
// Console theme.
// Available styles: white, green, grey, far, ubuntu
$theme = 'ubuntu';

// Start with this dir.
$currentDir         = ROOT_DIR;
$allowChangeDir     = false;

/**
 * List of commands filters. You can use * for any symbol. And $1 as replacement.
 * Usually you only need '*' => '$1' command. If you need some mapping add more.
 * Example: 'move * *' => 'mv $1 $2'
 */
$commands = array(
    'git*' => '/usr/bin/local/git $1',
    'yii*' => 'php yii $1',
    'composer*' => 'COMPOSER_HOME=.composer php composer.phar $1',
    'cms*' => 'php yii cms$1',
    '*' => '$1', // Allow any command. Must be at the end of the list.
);

// Allowed and denied commands.
/**
 * array of allowed commands. Default: empty array (all are allowed)
 * You can use * for any symbol.
 * Example: "branch*" will allow both "branch" and "branch -v" commands
 */
$allow = [];

/**
 * array of denied commands. Default: empty array (none is denied)
 * You can use * for any symbol.
 */
$deny = [
    "rm*",
    "cd*",
];



// Next comes the code...
###############################################
#                Controller                   #
###############################################
// Use next two for long time executing commands.
ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit","1024M");

//error_reporting(E_ALL | E_STRICT);
// определяем режим вывода ошибок
//ini_set('display_errors', 'On');



// If we have a user command execute it.
// Otherwise send user interface.
if (isset($_GET['command'])) {
    $userCommand = urldecode($_GET['command']);
    $userCommand = escapeshellcmd($userCommand);
} else {
    $userCommand = false;
}
// If can - get current dir.
if ($allowChangeDir && isset($_GET['cd'])) {
    $newDir = urldecode($_GET['cd']);
    if (is_dir($newDir)) {
        $currentDir = $newDir;
    }
}
###############################################
#              Authentication                 #
###############################################
// If auth is enabled:
if (!empty($users)) {
    if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($realm) . '"');
        die("Bye-bye!\n");
    }
    // Analyze the PHP_AUTH_DIGEST variable
    if (!($data = httpDigestParse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
        die("Wrong Credentials!\n");
    }
    // Generate the valid response
    $A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
    $A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
    $valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);
    if ($data['response'] != $valid_response) {
        die("Wrong Credentials!\n");
    }
    // ok, valid username & password
    $httpUsername = $data['username'];
}
###############################################
#                   Action                    #
###############################################
// Choose action if we have user command in query - execute it.
// Else send to user html frontend of console.
if (false !== $userCommand) {
    // Check command by allow list.
    if (!empty($allow)) {
        if (!searchCommand($userCommand, $allow)) {
            $these = implode('<br>', $allow);
            die("<span class='error'>Sorry, but this command not allowed. Try these:<br>{$these}</span>\n");
        }
    }
    // Check command by deny list.
    if (!empty($deny)) {
        if (searchCommand($userCommand, $deny)) {
            die("<span class='error'>Sorry, but this command is denied.</span>\n");
        }
    }
    // Change current dir.
    if ($allowChangeDir && 1 === preg_match('/^cd\s+(?<path>.+?)$/i', $userCommand, $matches)) {
        $newDir = $matches['path'];
        $newDir = '/' === $newDir[0] ? $newDir : $currentDir . '/' . $newDir;
        if (is_dir($newDir)) {
            $newDir = realpath($newDir);
            // Interface will recognize this and save as current dir.
            die("set current directory $newDir");
        } else {
            die("<span class='error'>cd: $newDir: No such directory.</span>\n");
        }
    }
    // Easter egg
    if (1 === preg_match('/^(g+?(i((r)l+?)))$/i', $userCommand)) {
        die(base64_decode('ICAgICAgICAgICAgICAgICAgICAgIC4sLCw6Ojs7dDtNTU1NTU1NTU1CVnQ6Ky4uDQogICAgICAgICAgICAgICAgICAgICAsSVZYVllJQnR0dCs7OytJVlZNTU1NTU1SUjoNCiAgICAgICAgICAgICAgICAgICAgICxZWVZZSXRNWXRpK2krKztYK1J0O3RYV1JNUiwNCiAgICAgICAgICAgICAgICAgICAgIC5ZUmlJWVJNVmlpdFZYUldSWU1JKysrK2l0TU0uLg0KICAgICAgICAgICAgICAgICAgICAgIC5ZKywuLFg7OywsLFlNTU1NTU1NTVJWSXRYTXRpDQogICAgICAgICAgICAgICAgICAgICAgIDtYKzssWDosLiAuLGlpSVJNV01NTUJCUk1NQlkuDQogICAgICAgICAgICAgICAgICAgICAgICB0Uis6STtpOitZO0lpdFlWWU1NTU1NTU1NUmkuDQogICAgICAgICAgICAgICAgICAgICAgICAuK1JYdDssOzouOjpYWElCTU1NTU1NTU1NKzoNCiAgICAgICAgICAgICAgICAgICAgICAgICAgLFJSWGl0WSssLjo7UldNTU1NTU1NTXQuDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgVllJOjo7LC4uOnRWTU1NTU1NQlkrLg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgLlZCQlc7Ozs6OixpLk1NTU1NQmk7Lg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgLnRXUlJWaTs7Oi5YOlZNTU1NTU1ZLg0KICAgICAgICAgICAgICAgICAgICAgICAgICwraSs6LFhZdHQrOixpOixNTU1CUjoNCiAgICAgICAgICAgICAgICAgICAgICAgLlZWLi4uLjoudHQ7OysrOissUk1ZTVYuDQogICAgICAgICAgICAgICAgICAgICAgIDpNOzs6Li4sLC4rdCsrK1l0dC4sKzoNCiAgICAgICAgICAgICAgICAgICAgICAgdFJ0OywuLjsrLiw7Kyt0aXQsDQogICAgICAgICAgICAgICAgICAgICAgOnRYdDssLiwsKyw7K1lSWSwNCiAgICAgICAgICAgICAgICAgICAgOisrOzs7Liw6Ljo7KztpTWkNCiAgICAgICAgICAgICAgICAgICAsUmk6OjosOjs6Ozo6OitJaQ0KICAgICAgICAgICAgICAgICAgICwrO1hpaTssLDs7STt0aXQsLg0KICAgICAgICAgICAgICAgICAgICAgO0JCdCw7Kzo6LDo7aSsuDQogICAgICAgICAgICAgICAgICAgICA7QldYWDs6Ojs7OmlYLg0KICAgICAgICAgICAgICAgICAgICAgOkJXVklpKyt0KztWKw0KICAgICAgICAgICAgICAgICAgICAgIFdCWHRJdGlpK2lXSS4NCiAgICAgICAgICAgICAgICAgICAgICA6TVdJWUl0aStpVlJZLA0KICAgICAgICAgICAgICAgICAgICAgICBSQlhWWUl0aWlJWVhXSSwNCiAgICAgICAgICAgICAgICAgICAgICAgO01SV1dWWXR0dHRJSVhXdC4NCiAgICAgICAgICAgICAgICAgICAgICAgLlhNQlJSWEl0aSsraXRJWFcsDQogICAgICAgICAgICAgICAgICAgICAgICAuQk1CQlJWSWkrOzsrdHRYWC4NCiAgICAgICAgICAgICAgICAgICAgICAgICAsTU1CUlhZdGk7OzsrdElXOw0KICAgICAgICAgICAgICAgICAgICAgICAgICB0TU1SV1l0aSsrK2l0dFhWDQogICAgICAgICAgICAgICAgICAgICAgICAgICArTVJWWXRpKysraXR0V0kNCiAgICAgICAgICAgICAgICAgICAgICAgICAgLlZNV1Z0aWlpaWlpdElSLA0KICAgICAgICAgICAgICAgICAgICAgICAgIC5YQkJXVnR0dHR0dHR0WFINCiAgICAgICAgICAgICAgICAgICAgICAgLixXQlJCWFZ0dHR0dHR0SVd0DQogICAgICAgICAgICAgICAgICAgICAgIDtSV1hXQlhZdHR0dHR0dFlSOw0KICAgICAgICAgICAgICAgICAgICAgLmlSV1ZJaUJXWUl0dHR0dHRZVywNCiAgICAgICAgICAgICAgICAgICAgLnRXVll0aTtXUlZJdHRpdHRJVlYgICAgICAuOiwsDQogICAgICAgICAgICAgICAgICAgIHRXVklpKys7WFJWSUl0dHR0SVhZICAgLi46WVl0WWk7dGl0dFYsDQogICAgICAgICAgICAgICAgICAgdFhZdGkrKyt0V1JWWXR0aXR0WVdJaUlZWVZJdHQ7aVhXKy4uLi4NCiAgICAgICAgICAgICAgICAgIDtXSXQrKytpWFJCQlZZSXRpdElZWFhZdGkraUlZdCsrO0lNUmk7Lg0KICAgICAgICAgICAgICAgIC46WHRpKzsrdFJXdDtCVllJdGl0SVlXVklJSVlYWFdYVlhZdCtpK0lWOw0KICAgICAgICAgICAgICAgIC50WWkrOztJV0k7OztCVlZJdGl0SVhCUlZJdDs7Ojo6Ojt0SVZYUmlYdA0KICAgICAgICAgICAgICAgIDpWaWlpKytpO2l0SVhCWFZ0dGl0VlcsICAgICAgICAgICAgICAgdEJJWA0KICAgICAgICAgICAgICAgIC5YSWlYSXR0SVZSQlJCSUl0dHRJUlggICAgICAgICAgICAgICAgIDpWWA0KICAgICAgICAgICAgICAgIC4sdFhYV1dXVmkrLiBSWFhJdGlZUlYgICAgICAgICAgICAgICAgICAuLg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAsQldZaStJUlgNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRCWWlpdFdCLA0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLldWdGlpSVJJDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgVld0aWlpSUIsDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLEJJaWlpaVd0DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLkJWaWlpaVlWDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIFhYdGlpK1lWDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlSaWlpK1lZDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDtCdGlpK1hJDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBXdGlpK1I7DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBYWSt0K0IuDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBZWCt0WVIuDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0WCtpV1YNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlYaStSSQ0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdFlpSVhYDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICxYdGlJWFJ0Lg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA7QklWWVJXSVYNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgK1JZWFhXaVlSLg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpV0lWWXRYTVYNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdEJZSXRSdE0rDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIFhCV3R0WDpCOg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0WVlCWFhZUjssUjoNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdElZWVlJWTsgICwu') . "\n");
    }
    // Check if command is not in commands list.
    if (!searchCommand($userCommand, $commands, $command, false)) {
        $these = implode('<br>', array_keys($commands));
        die("<span class='error'>Sorry, but this command not allowed. Try these:<br>{$these}</span>");
    }
    // Create final command and execute it.
    $command = "cd $currentDir && $command";
    list($output, $error, $code) = \Yii::$app->console->executeProcOpen($command);
    header("Content-Type: text/plain; charset=utf-8");
    echo formatOutput($userCommand, htmlspecialchars($output));
    echo htmlspecialchars($error);
    exit(0); // Terminate app
} else {
    // Send frontend to user.
    header('Content-Type: text/html; charset=utf-8');
    // Show current dir name.
    $currentDirName = explode('/', $currentDir);
    $currentDirName = end($currentDirName);
    // Show current user.
    $whoami = isset($commands['*']) ? str_replace('$1', 'whoami', $commands['*']) : 'whoami';
    list($currentUser) = \Yii::$app->console->executeProcOpen($whoami);
    $currentUser = trim($currentUser);
}
###############################################
#                  Functions                  #
###############################################
function searchCommand($userCommand, array $commands, &$found = false, $inValues = true)
{
    foreach ($commands as $key => $value) {
        list($pattern, $format) = $inValues ? array($value, '$1') : array($key, $value);
        $pattern = '/^' . str_replace('\*', '(.*?)', preg_quote($pattern)) . '$/i';
        if (preg_match($pattern, $userCommand)) {
            if (false !== $found) {
                $found = preg_replace($pattern, $format, $userCommand);
            }
            return true;
        }
    }
    return false;
}

function formatOutput($command, $output)
{
    if (preg_match("%^(git )?diff%is", $command) || preg_match("%^status.*?-.*?v%is", $command)) {
        $output = formatDiff($output);
    }
    $output = formatHelp($output);
    return $output;
}
function formatDiff($output)
{
    $lines = explode("\n", $output);
    foreach ($lines as $key => $line) {
        if (strpos($line, "-") === 0) {
            $lines[$key] = '<span class="diff-deleted">' . $line . '</span>';
        }
        if (strpos($line, "+") === 0) {
            $lines[$key] = '<span class="diff-added">' . $line . '</span>';
        }
        if (preg_match("%^@@.*?@@%is", $line)) {
            $lines[$key] = '<span class="diff-sub-header">' . $line . '</span>';
        }
        if (preg_match("%^index\s[^.]*?\.\.\S*?\s\S*?%is", $line) || preg_match("%^diff.*?a.*?b%is", $line)) {
            $lines[$key] = '<span class="diff-header">' . $line . '</span>';
        }
    }
    return implode("\n", $lines);
}
function formatHelp($output)
{
    // Underline words with _0x08* symbols.
    $output = preg_replace('/_[\b](.)/is', "<u>$1</u>", $output);
    // Highlight backslash words with *0x08* symbols.
    $output = preg_replace('/.[\b](.)/is', "<strong>$1</strong>", $output);
    return $output;
}
function httpDigestParse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));
    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }
    return $needed_parts ? false : $data;
}
###############################################
#                Autocomplete                 #
###############################################

$result = \Yii::$app->console->execute('cd '  . ROOT_DIR . '; php yii cms/utils/all-cmd');
$possibleCmd = explode("\n", $result);

$autocomplete = [
    '^\w*$' => array('cd', 'ls', 'mkdir', 'chmod', 'git', 'hg', 'diff', 'rm', 'mv', 'cp', 'more', 'grep', 'ff', 'whoami', 'kill', 'php', 'yii', 'composer', 'cms/update', 'cms/composer/status'),
    '^git \w*$' => array('status', 'push', 'pull', 'add', 'bisect', 'branch', 'checkout', 'clone', 'commit', 'diff', 'fetch', 'grep', 'init', 'log', 'merge', 'mv', 'rebase', 'reset', 'rm', 'show', 'tag', 'remote'),
    '^git \w* .*' => array('HEAD', 'origin', 'master', 'production', 'develop', 'rename', '--cached', '--global', '--local', '--merged', '--no-merged', '--amend', '--tags', '--no-hardlinks', '--shared', '--reference', '--quiet', '--no-checkout', '--bare', '--mirror', '--origin', '--upload-pack', '--template=', '--depth', '--help'),
    '^php \w*$' => array('yii'),
    '^php yii .*' => $possibleCmd,
    '^yii .*' => $possibleCmd,
    '^composer .*' => array('status', 'update', 'show', 'install', '-v', '-o', '--profile'),
];
###############################################
#                    View                     #
###############################################
?>

<pre></pre>
<form>
    <div id="currentDirName"><?php echo $currentDirName; ?></div>
    <div id="currentUser">&nbsp;<?php echo $currentUser; ?>$&nbsp;</div>
    <div id="command"><input type="text" value=""></div>
</form>

<?
/* @var $this yii\web\View */
\skeeks\cms\modules\admin\assets\AdminSshConsoleAsset::register($this);

$options = [
    'currentDirName'    => $currentDirName,
    'currentUser'       => $currentUser,
    'autocomplete'      => $autocomplete
];

$options = \yii\helpers\Json::encode($options);

$this->registerJs(<<<JS

(function(sx, $, _)
{
    sx.SshConsole = new sx.classes.SshConsole({$options});
})(sx, sx.$, sx._);
JS
)
?>