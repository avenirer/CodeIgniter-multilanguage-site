<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify my Codeigniter Framework</title>
    <style>
        /* a small css reset */
        html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video {padding: 0; border: 0; font-size: 100%; font: inherit; vertical-align: baseline;}
        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
        body {line-height: 1;}
        /* and some styling */
        strong {font-weight:bold;}
        body {font-family: "Arial", Helvetica, Arial, sans-serif;font-size: 14px;background-color: #fefedc;color: #1f191d;}
        ul {list-style: none;}
        h1 {font-size: 24px;font-weight: bold;text-align: center;line-height: 48px;}
        table {border-collapse: collapse; border-spacing: 0;width: 820px;}
        table, th, td {border: 1px solid #3a6fb7;background-color: #caebfc;margin: 10px auto;}
        th, td {padding: 5px;vertical-align: middle;}
        th {text-align: right;background-color: #3a6fb7;color: #fefedc;}
        thead th {background-color: #ff3f7b;font-size: 18px;font-weight: bold;color: #fefedc;text-align: center;}
        .center {text-align: center;}
        .red {color: #ff3f7b;}
    </style>
</head>
<body>
<h1>My Codeigniter Framework:</h1>
<table>
    <thead>
    <tr>
        <th>Environment</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            Current PHP version: <?php echo $phpversion; ?>
        </td>
    </tr>
    <tr>
        <td>
            CodeIgniter environment: <?php echo $environment; ?>
        </td>
    </tr>
    </tbody>
</table>
<table>
    <thead>
    <tr>
        <th colspan="3">Loader settings</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="center">Loaded classes:</th>
        <th class="center">Loaded helpers:</th>
        <th class="center">Loaded models:</th>
    </tr>
    <tr>
        <td>
            <ul>
                <?php
                foreach ($loaded_classes as $class) {
                    echo '<li>' . $class . '</li>';
                }
                ?>
            </ul>
        </td>
        <td>
            <ul>
                <?php
                foreach ($loaded_helpers as $helper) {
                    echo '<li>' . $helper . '</li>';
                }
                ?>
            </ul>
        </td>
        <td>
            <ul>
                <?php
                foreach ($loaded_models as $model) {
                    echo '<li>' . $model . '</li>';
                }
                ?>
            </ul>
        </td>
    </tr>
    </tbody>
</table>
<div style="clear:both; width: 820px; margin:auto;">
    <div style="float: left;">
        <table style="width: 400px;">
            <thead>
            <tr>
                <th colspan="2">Configuration:</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Base URL:</th>
                <td><?php echo $config['base_url']; ?></td>
            </tr>
            <tr>
                <th>Index page:</th>
                <td><?php echo $config['index_page']; ?></td>
            </tr>
            <tr>
                <th>Language:</th>
                <td><?php echo $config['language']; ?></td>
            </tr>
            <tr>
                <th>Charset:</th>
                <td><?php echo $config['charset']; ?></td>
            </tr>
            <tr>
                <th>Enable Hooks:</th>
                <td><?php echo(($config['enable_hooks']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Subclass prefix:</th>
                <td><?php echo $config['subclass_prefix']; ?></td>
            </tr>
            <tr>
                <th>Composer autoload:</th>
                <td><?php echo(($config['composer_autoload']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Permited URI chars:</th>
                <td><?php echo $config['permitted_uri_chars']; ?></td>
            </tr>
            <tr>
                <th>Allow GET array:</th>
                <td><?php echo(($config['allow_get_array']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Enable query strings:</th>
                <td><?php echo(($config['enable_query_strings']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Controller trigger:</th>
                <td><?php echo $config['controller_trigger']; ?></td>
            </tr>
            <tr>
                <th>Function trigger:</th>
                <td><?php echo $config['function_trigger']; ?></td>
            </tr>
            <tr>
                <th>Directory trigger:</th>
                <td><?php echo $config['directory_trigger']; ?></td>
            </tr>
            <tr>
                <th>Log threshold:</th>
                <td><?php echo $config['log_threshold']; ?></td>
            </tr>
            <tr>
                <th>Log path:</th>
                <td><?php echo(($config['log_path']) ? $config['log_path'] : 'DEFAULT'); ?></td>
            </tr>
            <tr>
                <th>Log file extension:</th>
                <td><?php echo(($config['log_file_extension']) ? $config['log_file_extension'] : 'DEFAULT'); ?></td>
            </tr>
            <tr>
                <th>Log file permissions:</th>
                <td><?php echo $config['log_file_permissions']; ?></td>
            </tr>
            <tr>
                <th>Log date format:</th>
                <td><?php echo $config['log_date_format']; ?></td>
            </tr>
            <tr>
                <th>Error views path:</th>
                <td><?php echo(($config['error_views_path']) ? $config['error_views_path'] : 'DEFAULT'); ?></td>
            </tr>
            <tr>
                <th>Cache path:</th>
                <td><?php echo(($config['error_views_path']) ? $config['cache_path'] : 'DEFAULT'); ?></td>
            </tr>
            <tr>
                <th>Standardize new lines:</th>
                <td><?php echo(($config['standardize_newlines']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Compress output:</th>
                <td><?php echo(($config['compress_output']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Time reference:</th>
                <td><?php echo $config['time_reference']; ?></td>
            </tr>
            <tr>
                <th>Rewrite short tags:</th>
                <td><?php echo $config['rewrite_short_tags'] ? 'TRUE' : '<span class="red">FALSE</span>'; ?></td>
            </tr>
            <tr>
                <th>Reverse Proxy IPs:</th>
                <td><?php echo $config['proxy_ips']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="float: right; width: 400px;">
        <table style="width: 100%;">
            <thead>
            <tr>
                <th>Writable directories</th>
            </tr>
            </thead>
            <tbody>
            <?php
            echo ($writable_cache) ? '<tr><td>The cache directory is writable</td></tr>' : '<tr><td><span class="red">The cache directory is not writable</span></td></tr>';
            echo ($writable_logs) ? '<tr><td>The logs directory is writable</td></tr>' : '<tr><td><span class="red">The logs directory is not writable</span></td></tr>';
            echo '<tr><td>'.$writable_uploads.'</td></tr>';
            ?>
            </tbody>
        </table>
        <table style="width: 100%;">
            <thead>
            <tr>
                <th colspan="2">XSS:</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Global XSS filter:</th>
                <td><?php echo(($config['global_xss_filtering']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            </tbody>
        </table>
        <table style="width: 100%;">
            <thead>
            <tr>
                <th colspan="2">CSRF protection</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>CSRF protection:</th>
                <td><?php echo(($config['csrf_protection']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>CSRF token name:</th>
                <td><?php echo $config['csrf_token_name']; ?></td>
            </tr>
            <tr>
                <th>CSRF cookie name:</th>
                <td><?php echo $config['csrf_cookie_name']; ?></td>
            </tr>
            <tr>
                <th>CSRF expire:</th>
                <td><?php echo $config['csrf_expire']; ?></td>
            </tr>
            <tr>
                <th>CSRF regenerate:</th>
                <td><?php echo(($config['csrf_regenerate']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>CSRF exclude URIs:</th>
                <td>
                    <?php
                    if (sizeof($config['csrf_exclude_uris']) > 0) {
                        foreach ($config['csrf_exclude_uris'] as $excluded) {
                            echo $excluded;
                        }
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        <table style="width: 100%;">
            <thead>
            <tr>
                <th colspan="2">Sessions and cookies</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Encryption key:</th>
                <td><?php echo $config['encryption_key']; ?></td>
            </tr>
            <tr>
                <th>Session driver:</th>
                <td><?php echo $config['sess_driver']; ?></td>
            </tr>
            <tr>
                <th>Session cookie name:</th>
                <td><?php echo $config['sess_cookie_name']; ?></td>
            </tr>
            <tr>
                <th>Session expiration:</th>
                <td><?php echo ($config['sess_expiration']==0) ? 'Expire on close' : $config['sess_expiration']; ?></td>
            </tr>
            <tr>
                <th>Match IP for sessions:</th>
                <td><?php echo(($config['sess_match_ip']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Session time to update:</th>
                <td><?php echo $config['sess_time_to_update']; ?></td>
            </tr>
            <tr>
                <th>Cookie prefix:</th>
                <td><?php echo(($config['cookie_prefix']) ? $config['cookie_prefix'] : 'DEFAULT'); ?></td>
            </tr>
            <tr>
                <th>Cookie domain:</th>
                <td><?php echo(($config['cookie_domain']) ? $config['cookie_prefix'] : 'DEFAULT'); ?></td>
            </tr>
            <tr>
                <th>Cookie path:</th>
                <td><?php echo $config['cookie_path']; ?></td>
            </tr>
            <tr>
                <th>Cookie secure:</th>
                <td><?php echo(($config['cookie_secure']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            <tr>
                <th>Cookie HTTP only:</th>
                <td><?php echo(($config['cookie_httponly']) ? 'TRUE' : '<span class="red">FALSE</span>'); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="clear:both;">
        <table>
            <thead>
            <tr>
                <th colspan="2">Database:<br/><?php echo $loaded_database; ?></th>
            </tr>
            </thead>
            <?php
            if (!empty($db_settings)) {
                ?>
                <tbody>
                <tr>
                    <th>DSN:</th>
                    <td><?php echo $db_settings['dsn']; ?></td>
                </tr>
                <tr>
                    <th>Hostname:</th>
                    <td><?php echo $db_settings['hostname']; ?></td>
                </tr>
                <tr>
                    <th>Port:</th>
                    <td><?php echo $db_settings['port']; ?></td>
                </tr>
                <tr>
                    <th>Username:</th>
                    <td><?php echo $db_settings['username']; ?></td>
                </tr>
                <tr>
                    <th>Password:</th>
                    <td><?php echo $db_settings['password']; ?></td>
                </tr>
                <tr>
                    <th>Database:</th>
                    <td><?php echo $db_settings['database']; ?></td>
                </tr>
                <tr>
                    <th>DB Driver:</th>
                    <td><?php echo $db_settings['driver']; ?></td>
                </tr>
                <tr>
                    <th>DB Prefix:</th>
                    <td><?php echo $db_settings['dbprefix']; ?></td>
                </tr>
                <tr>
                    <th>P Connect:</th>
                    <td><?php echo $db_settings['pconnect']; ?></td>
                </tr>
                <tr>
                    <th>DB Debug:</th>
                    <td><?php echo $db_settings['db_debug']; ?></td>
                </tr>
                <tr>
                    <th>Cache On:</th>
                    <td><?php echo $db_settings['cache_on']; ?></td>
                </tr>
                <tr>
                    <th>Cache Dir:</th>
                    <td><?php echo $db_settings['cachedir']; ?></td>
                </tr>
                <tr>
                    <th>Char Set:</th>
                    <td><?php echo $db_settings['char_set']; ?></td>
                </tr>
                <tr>
                    <th>DB Collation:</th>
                    <td><?php echo $db_settings['dbcollat']; ?></td>
                </tr>
                <tr>
                    <th>Swap pre:</th>
                    <td><?php echo $db_settings['swap_pre']; ?></td>
                </tr>
                <tr>
                    <th>Autoinit:</th>
                    <td><?php echo $db_settings['autoinit']; ?></td>
                </tr>
                <tr>
                    <th>Encrypt:</th>
                    <td><?php echo $db_settings['encrypt']; ?></td>
                </tr>
                <tr>
                    <th>Compress:</th>
                    <td><?php echo $db_settings['compress']; ?></td>
                </tr>
                <tr>
                    <th>Stricton:</th>
                    <td><?php echo $db_settings['stricton']; ?></td>
                </tr>
                <tr>
                    <th>Failover:</th>
                    <td>
                        <?php
                        foreach ($db_settings['failover'] as $fail) {
                            echo $fail . '<br />';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Save queries:</th>
                    <td><?php echo $db_settings['save_queries']; ?></td>
                </tr>
                </tbody>
            <?php
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>